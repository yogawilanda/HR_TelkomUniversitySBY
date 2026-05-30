<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PelaporanPekerjaan;
use App\Models\TargetKinerjaHarian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PelaporanPekerjaanController extends Controller
{
    public function create($targetHarianId)
    {
        try {
            $user = Auth::user();
            $isAdmin = $user->is_admin;
            $role = $user->role ?? 'pegawai';

            $target = TargetKinerjaHarian::findOrFail($targetHarianId);

            return view('kinerja_pegawai.pelaporan_pekerjaan.create', compact('target'));
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function store(Request $request, $targetHarianId)
    {
        try {
            $user = Auth::user();
            $isAdmin = $user->is_admin;
            $role = $user->role ?? 'pegawai';

            $target = TargetKinerjaHarian::findOrFail($targetHarianId);

            $data = $request->validate([
                'realisasi' => 'nullable|string',
                'referensi_set_target_id' => 'nullable|exists:target_kinerja_harian,id',
                'realisasi_jumlah' => 'nullable|integer',
                'realisasi_waktu_minutes' => 'nullable|integer',
                'pencapaian_percent' => 'nullable|integer',
                'evidence' => 'nullable|string',
                'waktu_pengerjaan' => 'required|integer|min:1',
            ]);

            $report = PelaporanPekerjaan::create([
                'user_id' => $user->id,
                'target_harian_id' => $target->id,
                'realisasi' => $data['realisasi'] ?? null,
                'referensi_set_target_id' => $data['referensi_set_target_id'] ?? $target->id,
                'realisasi_jumlah' => $data['realisasi_jumlah'] ?? null,
                'realisasi_waktu_minutes' => $data['realisasi_waktu_minutes'] ?? null,
                'status' => 'pending',
                'pencapaian_percent' => $data['pencapaian_percent'] ?? null,
                'evidence' => $data['evidence'] ?? null,
                'created_by' => $user->id,
                'waktu_pengerjaan' => $data['waktu_pengerjaan'],
            ]);

            return Redirect::route('manage.target-kinerja.harian.list')->with('success', 'Laporan kinerja harian berhasil dikirim');
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function laporanIndividual(Request $request)
    {
        $userId = Auth::id();
        $query = PelaporanPekerjaan::where('created_by', $userId)
            ->where('status', 'approved')
            ->selectRaw('DATE(created_at) as tanggal, SUM(realisasi_waktu_minutes) as total_input, SUM(waktu_validasi_atasan) as total_validasi, SUM(waktu_pengerjaan) as total_klaim')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc');

        $items = $query->get()->map(function ($item) {
            // TUGAS 5: Logika Efektivitas Harian
            $item->efektivitas = $item->total_validasi > 0 ? ($item->total_validasi / 450) : 0;

            if ($item->efektivitas > 1.0) {
                $item->status_efektivitas = 'Overload';
                $item->badge_color = 'bg-red-100 text-red-800';
            } elseif ($item->efektivitas >= 0.75) {
                $item->status_efektivitas = 'Optimal';
                $item->badge_color = 'bg-green-100 text-green-800';
            } else {
                $item->status_efektivitas = 'Kurang';
                $item->badge_color = 'bg-yellow-100 text-yellow-800';
            }

            $item->efektivitas_percent = round($item->efektivitas * 100, 2);
            return $item;
        });

        // TUGAS 5: Logika Efektivitas Bulanan (Agregasi)
        $totalValidasiBulan = $items->sum('total_validasi');
        $jumlahHariLapor = $items->count();
        $efektivitasBulanan = $jumlahHariLapor > 0 ? ($totalValidasiBulan / ($jumlahHariLapor * 450)) : 0;

        $statusBulanan = 'Kurang';
        if ($efektivitasBulanan > 1.0) $statusBulanan = 'Overload';
        elseif ($efektivitasBulanan >= 0.75) $statusBulanan = 'Optimal';

        return view('kinerja_pegawai.pelaporan_pekerjaan.laporan', compact('items', 'efektivitasBulanan', 'statusBulanan'));
    }

    public function approvalList()
    {
        // Force disable debugbar for this large data page to save memory
        if (class_exists('\Barryvdh\Debugbar\Facades\Debugbar')) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }

        $user = Auth::user();
        $isAdmin = $user->is_admin;
        $role = $user->role ?? 'pegawai';

        // Fitur 2g1: Statistik SLA & Progress Bulan Berjalan
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $baseQuery = PelaporanPekerjaan::query();

        // Filter based on role
        if (!$isAdmin) {
            if ($role === 'atasan') {
                $baseQuery->whereHas('pelapor', function ($q) use ($user) {
                    $q->where('unit_id', $user->unit_id);
                });
            } else {
                // By default, if they somehow get here, only see their own?
                // Or maybe only higher roles can approve.
                // Request said: "approval dilakukan atasan dan admin atau mungkin role sejajar diatas pegawai"
                $baseQuery->where('user_id', $user->id); // fallback
            }
        }

        $processedReports = clone $baseQuery;
        $processedReports->whereIn('status', ['approved', 'rejected'])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear);

        $slaStats = [
            'avg_minutes' => $processedReports->clone()->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_sla')->value('avg_sla') ?? 0,
            'total_processed' => $processedReports->count(),
            'approved_count' => $processedReports->clone()->where('status', 'approved')->count(),
            'rejected_count' => $processedReports->clone()->where('status', 'rejected')->count(),
            'pending_count' => (clone $baseQuery)->where('status', 'pending')->count(),
        ];

        $slaStats['avg_hours'] = round($slaStats['avg_minutes'] / 60, 1);

        $items = $baseQuery->with('targetHarian')->orderBy('id', 'desc')->paginate(15);
        return view('kinerja_pegawai.pelaporan_pekerjaan.list', compact('items', 'slaStats'));
    }

    public function showApproval($id)
    {
        try {
            $user = Auth::user();
            $isAdmin = $user->is_admin;
            $role = $user->role ?? 'pegawai';

            if (!$isAdmin && $role === 'pegawai') {
                abort(403, 'Pegawai tidak memiliki hak untuk melakukan validasi/approval laporan.');
            }

            $item = null;
            try {
                $item = PelaporanPekerjaan::with('targetHarian')->findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Pelaporan Pekerjaan ini tidak terdaftar!.');
            }
            
            // Check if atasan is allowed to view this specific report
            if (!$isAdmin && $role === 'atasan') {
                $pelapor = $item->pelapor;
                if ($pelapor && $pelapor->unit_id != $user->unit_id) {
                    abort(403, 'Anda hanya dapat memvalidasi laporan dari bawahan di unit anda.');
                }
            }

            return view('kinerja_pegawai.pelaporan_pekerjaan.approval', compact('item'));
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $isAdmin = $user->is_admin;
            $role = $user->role ?? 'pegawai';

            if (!$isAdmin && $role === 'pegawai') {
                abort(403, 'Pegawai tidak memiliki hak untuk melakukan validasi/approval laporan.');
            }

            $item = null;
            try {
                $item = PelaporanPekerjaan::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Pelaporan Pekerjaan ini tidak terdaftar!.');
            }

            // Check if atasan is allowed to approve this specific report
            if (!$isAdmin && $role === 'atasan') {
                $pelapor = $item->pelapor;
                if ($pelapor && $pelapor->unit_id != $user->unit_id) {
                    abort(403, 'Anda hanya dapat memvalidasi laporan dari bawahan di unit anda.');
                }
            }

            $data = $request->validate([
                'approved_jumlah' => 'nullable|integer',
                'approved_waktu_minutes' => 'nullable|integer',
                'assignment_status' => 'nullable|in:pending,in_progress,completed,cancelled',
                'pencapaian_percent' => 'nullable|integer',
                'evidence' => 'nullable|string',
                'waktu_validasi_atasan' => 'required_if:assignment_status,completed|numeric|min:1',
            ]);

            $item->approved_jumlah = $data['approved_jumlah'] ?? null;
            $item->approved_waktu_minutes = $data['approved_waktu_minutes'] ?? null;
            $item->approved_by = Auth::id();
            $item->waktu_validasi_atasan = $data['waktu_validasi_atasan'] ?? null;

            // set report status if provided (follow approval form)
            if (!empty($data['assignment_status'])) {
                $item->status = $data['assignment_status'] === 'completed' ? 'approved' : 'rejected';
            }
            // save pencapaian and evidence on the report
            if (array_key_exists('pencapaian_percent', $data)) {
                $item->pencapaian_percent = $data['pencapaian_percent'];
            }
            if (array_key_exists('evidence', $data)) {
                $item->evidence = $data['evidence'];
            }
            $item->save();

            // If an assignment status was provided, update the pivot status for the related target_kinerja and the report creator
            if (!empty($data['assignment_status'])) {
                $targetHarian = $item->targetHarian;
                if ($targetHarian && $targetHarian->target_kinerja_id && $item->created_by) {
                    $targetKinerja = \App\Models\TargetKinerja::find($targetHarian->target_kinerja_id);
                    if ($targetKinerja) {
                        // update pivot where user_id == created_by
                        $exists = $targetKinerja->pegawai()->where('users.id', $item->created_by)->exists();
                        if ($exists) {
                            $targetKinerja->pegawai()->updateExistingPivot($item->created_by, ['status' => $data['assignment_status']]);
                        }
                    }
                }
            }

            return Redirect::route('manage.target-kinerja.harian.reports')->with('success', 'Laporan disetujui');
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function reporting(Request $request)
    {
        $userLogged = Auth::user();
        $isAdmin = $userLogged->is_admin;
        $role = $userLogged->role ?? 'pegawai';

        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);
        $nama  = $request->get('nama');

        // Pemuatan Daftar User & Base Query (RBAC Ketat)
        if ($isAdmin || in_array($role, ['atasan', 'pimpinan'])) {
            $users = \App\Models\User::where('is_admin', 0)->orderBy('nama_lengkap')->get();

            $queryBase = PelaporanPekerjaan::where('status', 'approved')
                ->when($nama, function($q) use ($nama) {
                    return $q->whereHas('pelapor', function($sq) use ($nama) {
                        $sq->where('nama_lengkap', 'like', "%$nama%");
                    });
                });
        } else {
            // Role Pegawai: Hanya data sendiri, daftar user lain disembunyikan
            $users = collect();
            $nama = $userLogged->nama_lengkap;
            $queryBase = PelaporanPekerjaan::where('status', 'approved')
                ->where('user_id', $userLogged->id);
        }

        // DATA HARIAN (Detail per laporan)
        $dataHarian = (clone $queryBase)
            ->with('pelapor')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->map(function ($item) {
                $item->efektivitas = $item->waktu_validasi_atasan / 450;
                return $item;
            });

        // DATA BULANAN (Aggregasi per User)
        $dataBulanan = (clone $queryBase)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('user_id, SUM(waktu_validasi_atasan) as total_validasi, COUNT(DISTINCT tanggal) as hari_lapor')
            ->groupBy('user_id')
            ->with('pelapor')
            ->get()
            ->map(function ($item) {
                $pembagi = $item->hari_lapor * 450;
                $item->efektivitas = $pembagi > 0 ? ($item->total_validasi / $pembagi) : 0;

                if ($item->efektivitas > 1.0) {
                    $item->status_teks = 'Overload';
                    $item->color_class = 'bg-red-100 text-red-800';
                } elseif ($item->efektivitas >= 0.75) {
                    $item->status_teks = 'Optimal';
                    $item->color_class = 'bg-green-100 text-green-800';
                } else {
                    $item->status_teks = 'Kurang';
                    $item->color_class = 'bg-yellow-100 text-yellow-800';
                }
                return $item;
            });

        // DATA TAHUNAN (Aggregasi per User)
        $dataTahunan = (clone $queryBase)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('user_id, SUM(waktu_validasi_atasan) as total_validasi, COUNT(DISTINCT tanggal) as hari_lapor')
            ->groupBy('user_id')
            ->with('pelapor')
            ->get()
            ->map(function ($item) {
                $pembagi = $item->hari_lapor * 450;
                $item->efektivitas = $pembagi > 0 ? ($item->total_validasi / $pembagi) : 0;

                if ($item->efektivitas > 1.0) {
                    $item->status_teks = 'Overload';
                    $item->color_class = 'bg-red-100 text-red-800';
                } elseif ($item->efektivitas >= 0.75) {
                    $item->status_teks = 'Optimal';
                    $item->color_class = 'bg-green-100 text-green-800';
                } else {
                    $item->status_teks = 'Kurang';
                    $item->color_class = 'bg-yellow-100 text-yellow-800';
                }
                return $item;
            });

        return view('kinerja_pegawai.pelaporan_pekerjaan.reporting', compact('dataHarian', 'dataBulanan', 'dataTahunan', 'bulan', 'tahun', 'nama', 'users'));
    }
}
