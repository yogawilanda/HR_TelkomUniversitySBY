<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AkumulasiKinerja;
use App\Models\Presensi;
use App\Models\KinerjaSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->is_admin;
        $role = $user->role ?? 'pegawai';

        $query = AkumulasiKinerja::with('user');

        // Role-based data scoping
        if (!$isAdmin) {
            if ($role === 'pegawai') {
                $query->where('user_id', $user->id);
            } elseif ($role === 'atasan') {
                $query->whereHas('user', function ($q) use ($user) {
                    $q->where('unit_id', $user->unit_id);
                });
            }
            // pimpinan gets read-only access to all, no extra scoping here.
        }

        // Filtering by name / employee_id
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by month / year
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Sorting
        $items = $query->orderBy('year', 'desc')
                       ->orderBy('month', 'desc')
                       ->orderBy('fullname', 'asc')
                       ->paginate(15)
                       ->withQueryString();

        // Summary statistics based on the same scoping
        $summaryQuery = AkumulasiKinerja::query();
        if (!$isAdmin) {
            if ($role === 'pegawai') {
                $summaryQuery->where('user_id', $user->id);
            } elseif ($role === 'atasan') {
                $summaryQuery->whereHas('user', function ($q) use ($user) {
                    $q->where('unit_id', $user->unit_id);
                });
            }
        }

        if ($role === 'pegawai' && !$isAdmin) {
            $summary = [
                'total_pegawai' => 1,
                'avg_jam_kerja' => round($summaryQuery->sum('jam_kerja'), 1),
                'avg_kehadiran' => round($summaryQuery->avg('kehadiran'), 1),
                'masalah_tap'   => $summaryQuery->sum('tidak_tap_pulang'),
            ];
        } else {
            $summary = [
                'total_pegawai' => (clone $summaryQuery)->distinct('employee_id')->count('employee_id'),
                'avg_jam_kerja' => round((clone $summaryQuery)->avg('jam_kerja'), 1),
                'avg_kehadiran' => round((clone $summaryQuery)->avg('kehadiran'), 1),
                'masalah_tap'   => (clone $summaryQuery)->where('tidak_tap_pulang', '>', 0)->count(),
            ];
        }

        return view('kinerja_pegawai.presensi.index', compact('items', 'summary', 'role', 'isAdmin'));
    }

    public function settings()
    {
        // 2H2: Presence Settings
        // Only admin can access (Middleware should handle this, but adding check here too)
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $workStartTime = KinerjaSetting::get('work_start_time', '08:00');
        $workEndTime = KinerjaSetting::get('work_end_time', '17:00');
        $lateTolerance = KinerjaSetting::get('late_tolerance', 15);

        return view('kinerja_pegawai.presensi.settings', compact('workStartTime', 'workEndTime', 'lateTolerance'));
    }

    public function updateSettings(Request $request)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'late_tolerance' => 'required|integer|min:0'
        ]);

        KinerjaSetting::set('work_start_time', $request->work_start_time, 'string', 'Jam masuk kerja standar');
        KinerjaSetting::set('work_end_time', $request->work_end_time, 'string', 'Jam pulang kerja standar');
        KinerjaSetting::set('late_tolerance', $request->late_tolerance, 'number', 'Toleransi keterlambatan (menit)');

        // Update legacy max_check_in_time for backward compatibility if needed, 
        // though we'll update tardinessReport to calculate it.
        $carbonStart = \Carbon\Carbon::createFromFormat('H:i', $request->work_start_time);
        $maxCheckIn = $carbonStart->addMinutes($request->late_tolerance)->format('H:i');
        KinerjaSetting::set('max_check_in_time', $maxCheckIn, 'string', 'Batas jam masuk maksimal (format HH:mm) - Auto calculated');

        return redirect()->back()->with('success', 'Pengaturan presensi berhasil diperbarui');
    }

    public function tardinessReport(Request $request)
    {
        // 2H1: Tardiness Report
        if (!Auth::user()->is_admin && (Auth::user()->role ?? 'pegawai') === 'pegawai') {
            abort(403);
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $startTime = KinerjaSetting::get('work_start_time', '08:00');
        $tolerance = KinerjaSetting::get('late_tolerance', 15);
        
        $carbonStart = \Carbon\Carbon::createFromFormat('H:i', $startTime);
        $maxTime = $carbonStart->addMinutes($tolerance)->format('H:i:s');

        $report = User::where('is_admin', false)
            ->with(['presensis' => function($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            }])
            ->get()
            ->map(function($user) use ($maxTime) {
                $logs = $user->presensis;
                $tardinessCount = $logs->filter(function($log) use ($maxTime) {
                    return $log->jam_masuk > $maxTime;
                })->count();

                $avgCheckIn = null;
                if ($logs->count() > 0) {
                    $totalMinutes = $logs->sum(function($log) {
                        $parts = explode(':', $log->jam_masuk);
                        return ($parts[0] * 60) + $parts[1];
                    });
                    $avgMinutes = $totalMinutes / $logs->count();
                    $avgCheckIn = sprintf('%02d:%02d', floor($avgMinutes / 60), $avgMinutes % 60);
                }

                return [
                    'user' => $user,
                    'tardiness_count' => $tardinessCount,
                    'avg_check_in' => $avgCheckIn,
                ];
            });

        return view('kinerja_pegawai.presensi.tardiness', compact('report', 'maxTime', 'month', 'year'));
    }
}
