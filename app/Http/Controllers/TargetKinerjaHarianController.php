<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetKinerjaHarian;
use App\Models\TargetKinerja;
use App\Models\PelaporanPekerjaan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TargetKinerjaHarianController extends Controller
{
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isAdmin = $user->is_admin;
        $role = $user->role ?? 'pegawai';

        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Top 5 Leaderboard (Bulan Berjalan)
        $leaderboard = PelaporanPekerjaan::where('status', 'approved')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->select('user_id', DB::raw('SUM(approved_waktu_minutes) as total_minutes'))
            ->groupBy('user_id')
            ->orderByDesc('total_minutes')
            ->limit(5)
            ->with('pelapor:id,nama_lengkap')
            ->get();

        $query = TargetKinerjaHarian::with('targetKinerja');

        if (!$isAdmin) {
            if ($role === 'pegawai') {
                $query->whereHas('pegawai', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            } elseif ($role === 'atasan') {
                $query->whereHas('pegawai', function ($q) use ($user) {
                    $q->where('unit_id', $user->unit_id);
                });
            }
        }

        $items = $query->orderBy('id', 'desc')->paginate(15);
        return view('kinerja_pegawai.target_kinerja_harian.list', compact('items', 'leaderboard', 'role', 'isAdmin'));
    }

    public function create(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isAdmin = $user->is_admin;
        $role = $user->role ?? 'pegawai';

        $query = TargetKinerja::where('is_active', 1)->orderBy('nama_kpi');

        if (!$isAdmin) {
            if ($role === 'pegawai') {
                $query->whereHas('pegawai', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            } elseif ($role === 'atasan') {
                $query->whereHas('pegawai', function ($q) use ($user) {
                    $q->where('unit_id', $user->unit_id);
                });
            }
        }

        $targets = $query->get();
        return view('kinerja_pegawai.target_kinerja_harian.input', compact('targets'));
    }

    public function edit($id)
    {
        $harian = TargetKinerjaHarian::findOrFail($id);
        
        $user = \Illuminate\Support\Facades\Auth::user();
        $isAdmin = $user->is_admin;
        $role = $user->role ?? 'pegawai';

        $query = TargetKinerja::where('is_active', 1)->orderBy('nama_kpi');

        if (!$isAdmin) {
            if ($role === 'pegawai') {
                $query->whereHas('pegawai', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            } elseif ($role === 'atasan') {
                $query->whereHas('pegawai', function ($q) use ($user) {
                    $q->where('unit_id', $user->unit_id);
                });
            }
        }

        $targets = $query->get();
        return view('kinerja_pegawai.target_kinerja_harian.edit', compact('harian', 'targets'));
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

        $harian = TargetKinerjaHarian::create($data);

        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user->is_admin && ($user->role ?? 'pegawai') === 'pegawai') {
            $harian->pegawai()->attach($user->id, [
                'tanggal_mulai' => $data['start'] ?? now(),
                'tanggal_selesai' => $data['end'] ?? now(),
                'status' => 'pending'
            ]);
        }

        return Redirect::route('manage.target-kinerja.harian.list')->with('success', 'Target harian berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $harian = TargetKinerjaHarian::findOrFail($id);

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
            'user_id' => 'nullable|exists:users,id',
            'induk_kpi_ids' => 'nullable|array',
            'induk_kpi_ids.*' => 'exists:target_kinerja,id',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $harian->update($data);

        // Sync Pivot Table
        if (isset($data['induk_kpi_ids'])) {
            $harian->indukKpi()->sync($data['induk_kpi_ids']);
        }

        return Redirect::route('manage.target-kinerja.harian.list')->with('success', 'Target harian berhasil diperbarui');
    }

    public function show($id)
    {
        $item = TargetKinerjaHarian::with('targetKinerja')->findOrFail($id);

        $user = \Illuminate\Support\Facades\Auth::user();
        $isAdmin = $user->is_admin;
        $role = $user->role ?? 'pegawai';

        if (!$isAdmin && $role === 'pegawai') {
            if (!$item->pegawai()->where('users.id', $user->id)->exists()) {
                abort(403, 'Anda tidak memiliki akses ke target harian ini.');
            }
        }

        return view('kinerja_pegawai.target_kinerja_harian.view', compact('item'));
    }

    // Assignment related to daily target
    public function assign($id)
    {
        $harian = TargetKinerjaHarian::findOrFail($id);
        $pegawai = \App\Models\User::orderBy('nama_lengkap')->get();
        $assignedPegawai = $harian->pegawai;

        return view('kinerja_pegawai.target_kinerja_harian.assign', compact('harian', 'pegawai', 'assignedPegawai'));
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
        $user = \Illuminate\Support\Facades\Auth::user();
        $item = TargetKinerjaHarian::findOrFail($id);

        if (!$user->is_admin && ($user->role ?? 'pegawai') === 'pegawai') {
            // Only allow deleting if they are assigned to it
            $isAssigned = $item->pegawai()->where('users.id', $user->id)->exists();
            if (!$isAssigned) {
                abort(403, 'Pegawai hanya dapat menghapus target kinerja yang ditugaskan/dibuat sendiri.');
            }
        }

        $item->delete();

        return Redirect::route('manage.target-kinerja.harian.list')->with('success', 'Target harian dihapus');
    }
}
