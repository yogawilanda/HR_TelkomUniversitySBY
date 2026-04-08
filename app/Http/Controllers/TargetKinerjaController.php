<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetKinerja;
use App\Models\TargetKinerjaHarian;
use App\Models\PelaporanPekerjaan;
use Illuminate\Support\Facades\Redirect;

class TargetKinerjaController extends Controller
{
    public function laporan(Request $request)
    {
        $query = \App\Models\TargetKinerja::with(['pegawai' => function($q) {
            $q->with('dosen');
        }]);

        // Filter opsional
        if ($request->filled('status')) {
            $query->whereHas('pegawai', function($q) use ($request) {
                $q->wherePivot('status', $request->status);
            });
        }
        if ($request->filled('user_id')) {
            $query->whereHas('pegawai', function($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }
        if ($request->filled('target_id')) {
            $query->where('id', $request->target_id);
        }

        $targetKinerjaList = $query->get();

        // collect pelaporan (reports) for targets shown so laporan can include submitted reports
        $targetIds = $targetKinerjaList->pluck('id')->all();
        $harianIds = TargetKinerjaHarian::whereIn('target_kinerja_id', $targetIds)->pluck('id')->all();
        $pelaporanItems = PelaporanPekerjaan::with(['targetHarian'])->whereIn('target_harian_id', $harianIds)->orderBy('id', 'desc')->get();

        // Untuk filter dropdown
        $allUsers = \App\Models\User::orderBy('nama_lengkap')->get();
        $allTargets = \App\Models\TargetKinerja::orderBy('nama')->get();

        return view('kelola_data.target_kinerja.laporan', compact('targetKinerjaList', 'allUsers', 'allTargets', 'pelaporanItems'));
    }
    public function index()
    {
        $items = TargetKinerja::orderBy('id', 'desc')->get();
        return view('kelola_data.target_kinerja.list', ['targetKinerja' => $items]);
    }

    public function create()
    {
        return view('kelola_data.target_kinerja.input');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'bobot' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'responsibility' => 'nullable|string',
            'satuan' => 'nullable|string',
            'target_percent' => 'nullable|integer',
            'status' => 'nullable|string',
            'unit_penanggung_jawab' => 'nullable|string',
            'periode' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $data['bobot'] = $data['bobot'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        TargetKinerja::create($data);

        return Redirect::route('manage.target-kinerja.list')->with('success', 'Target Kinerja dibuat');
    }

    public function show($id)
    {
        $item = TargetKinerja::findOrFail($id);
        return view('kelola_data.target_kinerja.view', ['targetKinerja' => $item]);
    }

    public function edit($id)
    {
        $item = TargetKinerja::findOrFail($id);
        return view('kelola_data.target_kinerja.edit', ['targetKinerja' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = TargetKinerja::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'bobot' => 'nullable|integer',
            'responsibility' => 'nullable|string',
            'satuan' => 'nullable|string',
            'target_percent' => 'nullable|integer',
            'status' => 'nullable|string',
            'unit_penanggung_jawab' => 'nullable|string',
            'periode' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $data['bobot'] = $data['bobot'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $item->update($data);

        return Redirect::route('manage.target-kinerja.list')->with('success', 'Target Kinerja diperbarui');
    }

    public function destroy($id)
    {
        $item = TargetKinerja::findOrFail($id);
        $item->delete();

        return Redirect::route('manage.target-kinerja.list')->with('success', 'Target Kinerja dihapus');
    }

    public function assign($id)
    {
        $targetKinerja = TargetKinerja::findOrFail($id);
        $pegawai = \App\Models\User::orderBy('nama_lengkap')->get();
        $assignedPegawai = $targetKinerja->pegawai;

        return view('kelola_data.target_kinerja.assign', compact('targetKinerja', 'pegawai', 'assignedPegawai'));
    }

    public function storeAssignment(Request $request, $id)
    {
        $targetKinerja = TargetKinerja::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'catatan' => 'nullable|string',
        ]);

        // Jika status target 'pribadi', batasi maksimal 1 orang yang ditugaskan
        if ($targetKinerja->status === 'pribadi') {
            $assignedCount = $targetKinerja->pegawai()->count();
            // jika sudah ada penanggung (lebih dari atau sama dengan 1), batalkan
            if ($assignedCount >= 1) {
                return Redirect::route('manage.target-kinerja.assign', $id)
                    ->with('error', 'Kinerja pribadi hanya boleh memiliki 1 penanggung jawab.');
            }
        }

        // Cegah duplikasi assignment untuk user yang sama
        $alreadyAssigned = $targetKinerja->pegawai()->where('users.id', $data['user_id'])->exists();
        if ($alreadyAssigned) {
            return Redirect::route('manage.target-kinerja.assign', $id)
                ->with('error', 'Pegawai sudah ditugaskan pada target ini.');
        }

        $pivotData = [
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'status' => $data['status'] ?? 'pending',
            'catatan' => $data['catatan'] ?? null,
        ];

        $targetKinerja->pegawai()->attach($data['user_id'], $pivotData);

        return Redirect::route('manage.target-kinerja.assign', $id)->with('success', 'Pegawai berhasil ditambahkan');
    }

    public function detachPegawai($id, $userId)
    {
        $targetKinerja = TargetKinerja::findOrFail($id);
        $targetKinerja->pegawai()->detach($userId);

        return Redirect::route('manage.target-kinerja.assign', $id)->with('success', 'Pegawai berhasil dihapus');
    }

    public function updateAssignmentStatus(Request $request, $id, $userId)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $targetKinerja = TargetKinerja::findOrFail($id);
        $exists = $targetKinerja->pegawai()->where('users.id', $userId)->exists();
        if (!$exists) {
            return Redirect::route('manage.target-kinerja.assign', $id)->with('error', 'Pegawai tidak terdaftar untuk target ini.');
        }

        $targetKinerja->pegawai()->updateExistingPivot($userId, ['status' => $data['status']]);

        return Redirect::route('manage.target-kinerja.assign', $id)->with('success', 'Status pegawai berhasil diperbarui');
    }

    public function settings()
    {
        abort(404);
    }

    public function updateSettings(Request $request)
    {
        abort(404);
    }
}
