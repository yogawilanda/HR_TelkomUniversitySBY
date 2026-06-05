<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetKinerja;
use App\Models\TargetKinerjaHarian;
use App\Models\PelaporanPekerjaan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KinerjaDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $accountRole = session('account')['role'] ?? [];
        
        $isAdmin = $accountRole['is_admin'] ?? false;
        $isSDM = isset($accountRole['sumber daya manusia']);
        $isTopLevel = isset($accountRole['top-level']) && $accountRole['top-level'] <= 3;
        
        // Admin by is_admin flag or by role logic
        $canSeeGlobal = $isAdmin || $isSDM || $isTopLevel;

        if ($canSeeGlobal) {
            // Ringkasan KPI Global
            $totalTarget    = TargetKinerja::where('is_active', 1)->count();
            $laporanPending = PelaporanPekerjaan::where('status', 'pending')->count();
            $totalHarian    = TargetKinerjaHarian::where('is_active', 1)->count();

            // Laporan terkini Global
            $laporanTerkini = PelaporanPekerjaan::with(['pelapor', 'targetHarian'])
                ->latest()->take(10)->get();

            // 1. Ambil data 90 hari terakhir (Global)
            $rawReportsQuery = DB::table('pelaporan_pekerjaan')
                ->where('status', 'approved')
                ->where('pelaporan_pekerjaan.created_at', '>=', now()->subDays(90));

            // 3. Bandingkan dengan 90 hari sebelumnya untuk Tren (Global)
            $totalMinutesCurrent = PelaporanPekerjaan::where('status', 'approved')
                ->where('created_at', '>=', now()->subDays(90))
                ->sum('approved_waktu_minutes');
                
            $totalMinutesPast = PelaporanPekerjaan::where('status', 'approved')
                ->whereBetween('created_at', [now()->subDays(180), now()->subDays(91)])
                ->sum('approved_waktu_minutes');
        } else {
            // Ringkasan KPI Personal
            $totalTarget    = TargetKinerja::where('is_active', 1)
                ->whereHas('pegawai', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->count();
            $laporanPending = PelaporanPekerjaan::where('user_id', $user->id)
                ->where('status', 'pending')->count();
            $totalHarian    = TargetKinerjaHarian::whereHas('pegawai', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('is_active', 1)->count();

            // Laporan terkini Personal
            $laporanTerkini = PelaporanPekerjaan::with(['pelapor', 'targetHarian'])
                ->where('user_id', $user->id)
                ->latest()->take(10)->get();

            // 1. Ambil data 90 hari terakhir (Personal)
            $rawReportsQuery = DB::table('pelaporan_pekerjaan')
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('pelaporan_pekerjaan.created_at', '>=', now()->subDays(90));

            // 3. Bandingkan dengan 90 hari sebelumnya untuk Tren (Personal)
            $totalMinutesCurrent = PelaporanPekerjaan::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('created_at', '>=', now()->subDays(90))
                ->sum('approved_waktu_minutes');
                
            $totalMinutesPast = PelaporanPekerjaan::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereBetween('created_at', [now()->subDays(180), now()->subDays(91)])
                ->sum('approved_waktu_minutes');
        }

        $rawReports = $rawReportsQuery
            ->join('users', 'users.id', '=', 'pelaporan_pekerjaan.user_id')
            ->select(
                DB::raw('DATE(pelaporan_pekerjaan.created_at) as date'),
                'users.nama_lengkap',
                'pelaporan_pekerjaan.approved_waktu_minutes'
            )
            ->get();

        // 2. Kalkulasi Per Hari
        $dailyData = [];
        foreach ($rawReports as $report) {
            $date = $report->date;
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = ['total_min' => 0, 'users' => []];
            }
            $dailyData[$date]['total_min'] += $report->approved_waktu_minutes;
            
            // Simpan akumulasi per user untuk mencari top contributor
            $name = explode(' ', $report->nama_lengkap)[0]; 
            $dailyData[$date]['users'][$name] = ($dailyData[$date]['users'][$name] ?? 0) + $report->approved_waktu_minutes;
        }

        $trend = 0;
        if ($totalMinutesPast > 0) {
            $trend = (($totalMinutesCurrent - $totalMinutesPast) / $totalMinutesPast) * 100;
        }

        // 4. Bangun Heatmap Data (90 Hari)
        $heatmapData = [];
        $peakMinutes = 0;
        $peakDate = '-';
        $startDate = now()->subDays(89);

        for ($date = $startDate->copy(); $date->lte(now()); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $data = $dailyData[$dateStr] ?? ['total_min' => 0, 'users' => []];
            
            // Cari Top 3
            arsort($data['users']);
            $topThree = array_slice(array_keys($data['users']), 0, 3);
            $othersCount = max(0, count($data['users']) - 3);

            $minutes = $data['total_min'];
            if ($minutes > $peakMinutes) {
                $peakMinutes = $minutes;
                $peakDate = $date->format('d M Y');
            }

            $heatmapData[] = [
                'x' => $dateStr,
                'y' => round($minutes / 60, 1),
                'contributors' => count($data['users']),
                'top_names' => implode(', ', $topThree) . ($othersCount > 0 ? " +$othersCount" : '')
            ];
        }

        $stats = [
            'total_hours' => round($totalMinutesCurrent / 60),
            'avg_daily'   => round(($totalMinutesCurrent / 90) / 60, 1),
            'trend'       => round($trend, 1),
            'peak_day'    => $peakDate,
            'peak_value'  => round($peakMinutes / 60, 1)
        ];

        // 5. Achievement Badges (Gamifikasi - Fitur 2A5)
        $userId = auth()->id();
        $badges = $this->calculateBadgesForUser($userId);

        // 6. Recent Achievements List (Global - Max 5)
        $recentAchievements = [];
        $activeUserIds = PelaporanPekerjaan::latest()
            ->distinct('user_id')
            ->take(20)
            ->pluck('user_id');

        foreach ($activeUserIds as $uId) {
            $uBadges = $this->calculateBadgesForUser($uId);
            if ($uBadges['reliable'] || $uBadges['speedy']) {
                $userModel = User::find($uId);
                if ($userModel) {
                    $recentAchievements[] = [
                        'user' => $userModel,
                        'badges' => $uBadges
                    ];
                }
            }
            if (count($recentAchievements) >= 5) break;
        }

        return view('kinerja_pegawai.index', compact(
            'totalTarget', 'laporanPending', 'totalHarian', 'laporanTerkini', 'heatmapData', 'stats', 'badges', 'recentAchievements'
        ));
    }

    private function calculateBadgesForUser($userId)
    {
        $lastTenReports = PelaporanPekerjaan::where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        $badges = [
            'reliable' => false,
            'speedy'   => false
        ];

        // "The Reliable": 10 consecutive approved reports
        if ($lastTenReports->count() >= 10) {
            $badges['reliable'] = $lastTenReports->every(fn($rep) => $rep->status === 'approved' || $rep->status === 'completed');
        }

        // "Speedy Submitter": Avg input time < work_end_time in last 5 reports
        $lastFiveReports = $lastTenReports->take(5);
        if ($lastFiveReports->count() >= 5) {
            $workEndTime = \App\Models\KinerjaSetting::get('work_end_time', '17:00');
            $endHour = (int) explode(':', $workEndTime)[0];
            $avgHour = $lastFiveReports->avg(fn($rep) => $rep->created_at->hour);
            $badges['speedy'] = $avgHour < $endHour;
        }

        return $badges;
    }

    public function monitoring()
    {
        $accountRole = session('account')['role'] ?? [];
        $isAdmin = $accountRole['is_admin'] ?? false;
        $isSDM = isset($accountRole['sumber daya manusia']);
        $isTopLevel = isset($accountRole['top-level']) && $accountRole['top-level'] <= 3;
        
        if (!$isAdmin && !$isSDM && !$isTopLevel) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman monitoring.');
        }

        // Fitur 2G2: Zero Activity Tracker
        $today = now()->toDateString();
        
        // 1. Pegawai Belum Lapor (Inactive)
        $inactiveUsers = User::whereDoesntHave('pelaporanKinerja', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })
        ->where('is_admin', false)
        ->orderBy('nama_lengkap')
        ->get();

        // 2. Pegawai Sudah Lapor (Active)
        $activeUsers = User::whereHas('pelaporanKinerja', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })
        ->where('is_admin', false)
        ->orderBy('nama_lengkap')
        ->get();

        return view('kinerja_pegawai.monitoring.index', compact('inactiveUsers', 'activeUsers', 'today'));
    }

    public function targetDetail($id)
    {
        $target = TargetKinerja::findOrFail($id);
        
        // Menggunakan helper auth() secara eksplisit
        $currentUserId = auth()->id();

        // Hitung Total Realisasi MILIK USER (Sum dari approved_jumlah di PelaporanPekerjaan)
        $totalRealisasi = PelaporanPekerjaan::where('status', 'approved')
            ->where('user_id', $currentUserId)
            ->whereHas('targetHarian', function($q) use ($id) {
                $q->where('target_kinerja_id', $id);
            })
            ->sum('approved_jumlah');
            
        // Target angka dihitung dari jumlah seluruh target triwulan
        $totalTarget = ($target->tw1_target ?? 0) + ($target->tw2_target ?? 0) + ($target->tw3_target ?? 0) + ($target->tw4_target ?? 0);
        
        $percentage = $totalTarget > 0 ? ($totalRealisasi / $totalTarget) * 100 : 0;
        $percentage = min($percentage, 100);
        $percentage = round($percentage, 1);
        
        return view('kinerja_pegawai.dashboard_target.detail', compact('target', 'totalRealisasi', 'totalTarget', 'percentage'));
    }
}
