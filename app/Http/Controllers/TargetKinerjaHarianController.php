<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetKinerjaHarian;
use App\Models\TargetKinerja;
use Illuminate\Support\Facades\Redirect;

class TargetKinerjaHarianController extends Controller
{
    public function index(Request $request)
    {
        $items = TargetKinerjaHarian::with('targetKinerja')->orderBy('id', 'desc')->get();
        return view('kelola_data.target_kinerja_harian.list', compact('items'));
    }

    public function create(Request $request)
    {
        $targets = TargetKinerja::where('is_active', 1)->orderBy('nama')->get();
        return view('kelola_data.target_kinerja_harian.input', compact('targets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pekerjaan' => 'required|string',
            'kontrak_type' => 'nullable|in:institusi,unit,pribadi',
            'target_kinerja_id' => 'nullable|exists:target_kinerja,id',
            'result' => 'nullable|string',
            'jumlah' => 'nullable|integer',
            'waktu_minutes' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'bobot' => 'nullable|integer',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        TargetKinerjaHarian::create($data);

        return Redirect::route('manage.target-kinerja.harian.list')->with('success', 'Target harian berhasil dibuat');
    }

    public function show($id)
    {
        $item = TargetKinerjaHarian::with('targetKinerja')->findOrFail($id);
        return view('kelola_data.target_kinerja_harian.view', compact('item'));
    }

    // Assignment related to daily target
    public function assign($id)
    {
        $harian = TargetKinerjaHarian::findOrFail($id);
        $pegawai = \App\Models\User::orderBy('nama_lengkap')->get();
        $assignedPegawai = $harian->pegawai;

        return view('kelola_data.target_kinerja_harian.assign', compact('harian', 'pegawai', 'assignedPegawai'));
    }

    public function storeAssignment(Request $request, $id)
    {
        $harian = TargetKinerjaHarian::with('targetKinerja')->findOrFail($id);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'catatan' => 'nullable|string',
        ]);

        // If parent target is 'pribadi', limit to 1 assignee per daily item
        if ($harian->targetKinerja && $harian->targetKinerja->status === 'pribadi') {
            $assignedCount = $harian->pegawai()->count();
            if ($assignedCount >= 1) {
                return Redirect::route('manage.target-kinerja.harian.assign', $id)
                    ->with('error', 'Kinerja pribadi hanya boleh memiliki 1 penanggung jawab untuk setiap target harian.');
            }
        }

        $alreadyAssigned = $harian->pegawai()->where('users.id', $data['user_id'])->exists();
        if ($alreadyAssigned) {
            return Redirect::route('manage.target-kinerja.harian.assign', $id)
                ->with('error', 'Pegawai sudah ditugaskan pada target harian ini.');
        }

        $pivotData = [
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'status' => $data['status'] ?? 'pending',
            'catatan' => $data['catatan'] ?? null,
        ];

        $harian->pegawai()->attach($data['user_id'], $pivotData);

        return Redirect::route('manage.target-kinerja.harian.assign', $id)->with('success', 'Pegawai berhasil ditambahkan ke target harian');
    }

    public function detachPegawai($id, $userId)
    {
        $harian = TargetKinerjaHarian::findOrFail($id);
        $harian->pegawai()->detach($userId);

        return Redirect::route('manage.target-kinerja.harian.assign', $id)->with('success', 'Pegawai berhasil dihapus dari target harian');
    }

    public function updateAssignmentStatus(Request $request, $id, $userId)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $harian = TargetKinerjaHarian::findOrFail($id);
        $exists = $harian->pegawai()->where('users.id', $userId)->exists();
        if (!$exists) {
            return Redirect::route('manage.target-kinerja.harian.assign', $id)->with('error', 'Pegawai tidak terdaftar untuk target harian ini.');
        }

        $harian->pegawai()->updateExistingPivot($userId, ['status' => $data['status']]);

        return Redirect::route('manage.target-kinerja.harian.assign', $id)->with('success', 'Status pegawai berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = TargetKinerjaHarian::findOrFail($id);
        $item->delete();

        return Redirect::route('manage.target-kinerja.harian.list')->with('success', 'Target harian dihapus');
    }
}
