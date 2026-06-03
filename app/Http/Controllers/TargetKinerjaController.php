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
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user->is_admin && ($user->role ?? 'pegawai') === 'pegawai') {
            abort(403, 'Pegawai tidak memiliki hak untuk melihat laporan KM & SM global.');
        }

        $query = \App\Models\TargetKinerja::with(['pegawai', 'unit']);

        // Filter opsional
        if ($request->filled('status')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->wherePivot('status', $request->status);
            });
        }
        if ($request->filled('user_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }
        if ($request->filled('target_id')) {
            $query->where('id', $request->target_id);
        }

        $targetKinerjaList = $query->get();

        $targetIds = $targetKinerjaList->pluck('id')->all();
        $harianIds = TargetKinerjaHarian::whereIn('target_kinerja_id', $targetIds)->pluck('id')->all();

        // Optimize: Use pagination for reports to avoid memory exhaustion
        $pelaporanItems = PelaporanPekerjaan::with(['targetHarian', 'pembuat_laporan'])
            ->whereIn('target_harian_id', $harianIds)
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Untuk filter dropdown - Optimize: Only select necessary fields
        $allUsers = \App\Models\User::select('id', 'nama_lengkap')->orderBy('nama_lengkap')->get();
        $allTargets = \App\Models\TargetKinerja::select('id', 'nama_kpi')->orderBy('nama_kpi')->get();

        return view('kinerja_pegawai.target_kinerja.laporan', compact('targetKinerjaList', 'allUsers', 'allTargets', 'pelaporanItems'));
    }
    public function laporanCapaian(Request $request)
    {
        $items = TargetKinerja::with(['targetHarian.pegawai' => function($q) {
            $q->with('pelaporan');
        }])->get()->map(function($target) {
            // Logic sederhana hitung realisasi: jumlah approved_jumlah dari semua laporan yang terhubung ke target ini
            $totalRealisasi = 0;
            foreach ($target->targetHarian as $harian) {
                $totalRealisasi += PelaporanPekerjaan::where('target_harian_id', $harian->id)
                    ->where('status', 'approved')
                    ->sum('approved_jumlah');
            }
            $target->total_realisasi = $totalRealisasi;
            return $target;
        });

        return view('kinerja_pegawai.target_kinerja.laporan_capaian', compact('items'));
    }

    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isAdmin = $user->is_admin;
        $role = $user->role ?? 'pegawai';

        if (!$isAdmin && $role === 'pegawai') {
            abort(403, 'Pegawai tidak memiliki akses ke daftar KM & Sasaran Mutu.');
        }

        $query = TargetKinerja::query();

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

        $items = $query->get();
        return view('kinerja_pegawai.target_kinerja.list', [
            'targetKinerja' => $items,
            'role' => $role,
            'isAdmin' => $isAdmin
        ]);
    }

    public function create()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user->is_admin && ($user->role ?? 'pegawai') === 'pegawai') {
            abort(403, 'Pegawai tidak memiliki hak untuk membuat target kinerja baru.');
        }
        return view('kinerja_pegawai.target_kinerja.input');
    }

    public function store(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user->is_admin && ($user->role ?? 'pegawai') === 'pegawai') {
            abort(403, 'Pegawai tidak memiliki hak untuk membuat KM & Sasaran Mutu baru.');
        }

        $data = $request->validate([
            'nama_kpi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'responsibility_id' => 'nullable|integer',
            'jenis' => 'nullable|in:Kontrak Manajemen,Sasaran Mutu',
            'satuan' => 'nullable|string',
            'tahun' => 'nullable|integer|min:2000|max:2100',
            'status' => 'nullable|string',
            'tw1_target' => 'nullable|numeric',
            'tw1_bobot' => 'nullable|numeric',
            'tw2_target' => 'nullable|numeric',
            'tw2_bobot' => 'nullable|numeric',
            'tw3_target' => 'nullable|numeric',
            'tw3_bobot' => 'nullable|numeric',
            'tw4_target' => 'nullable|numeric',
            'tw4_bobot' => 'nullable|numeric',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // Force default 0 for TW fields if empty
        $twFields = [
            'tw1_target', 'tw1_bobot', 'tw2_target', 'tw2_bobot',
            'tw3_target', 'tw3_bobot', 'tw4_target', 'tw4_bobot'
        ];
        foreach ($twFields as $field) {
            $data[$field] = $data[$field] ?? 0;
        }

        TargetKinerja::create($data);

        return Redirect::route('manage.target-kinerja.list')->with('success', 'KM & Sasaran Mutu berhasil dibuat');
    }

    public function show($id)
    {
        try {
            $item = null;
            try {
                $item = TargetKinerja::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Target Kinerja ini tidak terdaftar!.');
            }

            $user = \Illuminate\Support\Facades\Auth::user();
            $isAdmin = $user->is_admin;
            $role = $user->role ?? 'pegawai';

            if (!$isAdmin && $role === 'pegawai') {
                if (!$item->pegawai()->where('users.id', $user->id)->exists()) {
                    abort(403, 'Anda tidak memiliki akses ke target ini.');
                }
            }

            return view('kinerja_pegawai.target_kinerja.view', ['targetKinerja' => $item]);
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user->is_admin && ($user->role ?? 'pegawai') === 'pegawai') {
                abort(403, 'Pegawai tidak memiliki hak untuk mengedit target kinerja.');
            }

            $item = null;
            try {
                $item = TargetKinerja::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Target Kinerja ini tidak terdaftar!.');
            }
            return view('kinerja_pegawai.target_kinerja.edit', ['targetKinerja' => $item]);
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $item = null;
            try {
                $item = TargetKinerja::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('KM & Sasaran Mutu ini tidak terdaftar!.');
            }

            $data = $request->validate([
                'nama_kpi' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'responsibility_id' => 'nullable|integer',
                'jenis' => 'nullable|in:Kontrak Manajemen,Sasaran Mutu',
                'satuan' => 'nullable|string',
                'tahun' => 'nullable|integer|min:2000|max:2100',
                'status' => 'nullable|string',
                'tw1_target' => 'nullable|numeric',
                'tw1_bobot' => 'nullable|numeric',
                'tw2_target' => 'nullable|numeric',
                'tw2_bobot' => 'nullable|numeric',
                'tw3_target' => 'nullable|numeric',
                'tw3_bobot' => 'nullable|numeric',
                'tw4_target' => 'nullable|numeric',
                'tw4_bobot' => 'nullable|numeric',
            ]);

            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            // Force default 0 for TW fields if empty
            $twFields = [
                'tw1_target', 'tw1_bobot', 'tw2_target', 'tw2_bobot',
                'tw3_target', 'tw3_bobot', 'tw4_target', 'tw4_bobot'
            ];
            foreach ($twFields as $field) {
                $data[$field] = $data[$field] ?? 0;
            }

            $item->update($data);

            return Redirect::route('manage.target-kinerja.list')->with('success', 'KM & Sasaran Mutu diperbarui');
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {

            $item = null;
            try {
                $item = TargetKinerja::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Target Kinerja ini tidak terdaftar!.');
            }
            $item->delete();

            return Redirect::route('manage.target-kinerja.list')->with('success', 'Target Kinerja dihapus');
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function assign($id)
    {
        try {


            $targetKinerja = null;
            try {
                $targetKinerja = TargetKinerja::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Target Kinerja ini tidak terdaftar!.');
            }
            $pegawai = \App\Models\User::orderBy('nama_lengkap')->get();
            $assignedPegawai = $targetKinerja->pegawai;

            return view('kinerja_pegawai.target_kinerja.assign', compact('targetKinerja', 'pegawai', 'assignedPegawai'));
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function storeAssignment(Request $request, $id)
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user->is_admin && ($user->role ?? 'pegawai') === 'pegawai') {
                abort(403, 'Pegawai tidak memiliki hak untuk menugaskan pegawai pada target kinerja.');
            }

            $targetKinerja = null;
            try {
                $targetKinerja = TargetKinerja::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Target Kinerja ini tidak terdaftar!.');
            }

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
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function detachPegawai($id, $userId)
    {
        try {

            $targetKinerja = null;
            try {
                $targetKinerja = TargetKinerja::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Target Kinerja ini tidak terdaftar!.');
            }
            $targetKinerja->pegawai()->detach($userId);

            return Redirect::route('manage.target-kinerja.assign', $id)->with('success', 'Pegawai berhasil dihapus');
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function updateAssignmentStatus(Request $request, $id, $userId)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);
        try {
            $targetKinerja = null;
            try {
                $targetKinerja = TargetKinerja::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Target Kinerja ini tidak terdaftar!.');
            }
            $exists = $targetKinerja->pegawai()->where('users.id', $userId)->exists();
            if (!$exists) {
                return Redirect::route('manage.target-kinerja.assign', $id)->with('error', 'Pegawai tidak terdaftar untuk target ini.');
            }

            $targetKinerja->pegawai()->updateExistingPivot($userId, ['status' => $data['status']]);

            return Redirect::route('manage.target-kinerja.assign', $id)->with('success', 'Status pegawai berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
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
