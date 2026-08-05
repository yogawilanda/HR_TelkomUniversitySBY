<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Dupak\DetailPengajuan;
use App\Models\Dupak\HasilEvaluasi;
use App\Models\Dupak\Pengajuan;
use App\Models\Dupak\PenunjukanTPAKModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidasiController extends Controller
{
    /**
     * Cek apakah user yang login adalah TPAK yang ditunjuk untuk pengajuan ini.
     */
    private function isAuthorizedTpak(string $pengajuanId): bool
    {
        $dosen = Dosen::where('users_id', Auth::id())->first();
        if (!$dosen) return false;

        return PenunjukanTPAKModel::where('pengajuan_id', $pengajuanId)
            ->where('idDosenTpak', $dosen->id)
            ->exists();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $userId = Auth::id();

        // --- Statistik Real-Time ---
        $dosen = Dosen::where('users_id', $userId)->first();
        $tpakDosenId = $dosen?->id;

        if ($tpakDosenId) {
            // Get pengajuan IDs where this TPAK is assigned
            $assignedPengajuanIds = PenunjukanTPAKModel::where('idDosenTpak', $tpakDosenId)
                ->pluck('pengajuan_id')
                ->toArray();

            // Query pengajuan dengan eager loading details dan komponen
            // update: tambah mengambil data dari model dosen juga.
            $query = Pengajuan::with(['details.komponen', 'dosen'])
                ->whereIn('id', $assignedPengajuanIds);
        } else {
            $query = Pengajuan::query()->whereRaw('1 = 0');
        }

        if ($search) {
            $query->where('nama_dosen', 'LIKE', "%{$search}%");
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $pengajuanList = $query->orderBy('created_at', 'desc')->paginate(15);

        // Ambil semua pengajuan_id yang ditampilkan
        $allPengajuanIds = $pengajuanList->pluck('id')->toArray();

        // Ambil semua detail per pengajuan untuk ditampilkan di accordion
        // Include evaluasi juga agar status display bisa benar
        $allDetailsMap = [];
        $detailPengajuan = \App\Models\Dupak\DetailPengajuan::whereIn('pengajuan_id', $allPengajuanIds)
            ->with('komponen')
            ->get();

        // Collect semua detail IDs
        $allDetailIds = $detailPengajuan->pluck('id')->toArray();

        // Ambil evaluasi oleh TPAK ini saja
        $evaluationsByDetail = [];
        if (!empty($allDetailIds)) {
            $evaluations = HasilEvaluasi::whereIn('detail_pengajuan_id', $allDetailIds)
                ->where('idUserPemeriksa', $userId)
                ->get()
                ->keyBy('detail_pengajuan_id');
            $evaluationsByDetail = $evaluations->toArray();
        }

        // Group detail by pengajuan_id - tetap preserve semua detail, tidak perlu filter!
        foreach ($detailPengajuan as $detail) {
            $pid = $detail->pengajuan_id;
            if (!isset($allDetailsMap[$pid])) {
                $allDetailsMap[$pid] = [];
            }
            // Attach evaluasi ke detail object untuk view access
            $detail->evaluation = $evaluationsByDetail[$detail->id] ?? null;
            $allDetailsMap[$pid][] = $detail;
        }

        // Hitung evaluasi yang sudah dilakukan - dari $evaluationsByDetail keys
        $evaluatedIds = array_keys($evaluationsByDetail);
        $selesaiCount = count($evaluatedIds);
        $totalTugas = count($allDetailIds);

        // Hitung rata-rata score (persentase nilai yang diberikan)
        $avgScore = 0;
        if ($selesaiCount > 0) {
            $evalData = HasilEvaluasi::whereIn('detail_pengajuan_id', $allDetailIds)
                ->where('idUserPemeriksa', $userId)
                ->get();

            $totalPercent = 0;
            $counted = 0;
            foreach ($evalData as $eval) {
                $detail = \App\Models\Dupak\DetailPengajuan::find($eval->detail_pengajuan_id);
                if ($detail && $detail->angka_kredit_total > 0) {
                    $totalPercent += ($eval->nilai_angka_kredit / $detail->angka_kredit_total) * 100;
                    $counted++;
                }
            }
            $avgScore = $counted > 0 ? round($totalPercent / $counted, 1) : 0;
        }

        // Mapping progress per pengajuan (berdasarkan detail yang sudah di-evaluasi)
        $progressMap = [];
        foreach ($allDetailsMap as $pid => $details) {
            $detailIds = array_map(function ($d) {
                return $d->id;
            }, $details);
            $evaluated = array_intersect($detailIds, $evaluatedIds);
            $totalDetail = count($detailIds);
            $evaluatedCount = count($evaluated);

            $progressMap[$pid] = [
                'evaluated' => $evaluatedCount === $totalDetail && $totalDetail > 0,
                'percent' => $totalDetail > 0 ? round(($evaluatedCount / $totalDetail) * 100) : 0,
                'totalDetail' => $totalDetail,
                'evaluatedCount' => $evaluatedCount,
            ];
        }
        // dd($pengajuanList);

        return view('dupak.validasi.index', compact(
            'pengajuanList',
            'allDetailsMap',
            'selesaiCount',
            'totalTugas',
            'avgScore',
            'progressMap'
        ));
    }

    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {
    //     $pengajuan = Pengajuan::with(['details.komponen', 'dosen'])->findOrFail($id);

    //     if (!$this->isAuthorizedTpak($pengajuan->id)) {
    //         abort(403, 'Anda tidak memiliki akses untuk menilai pengajuan ini.');
    //     }

    //     $nidnDosenPengaju = $pengajuan->dosen?->nidn;
    //     // dd($nidnDosenPengaju);

    //     $detailIds = $pengajuan->details->pluck('id')->toArray();

    //     $myEvaluations = HasilEvaluasi::whereIn('detail_pengajuan_id', $detailIds)
    //         ->where('idUserPemeriksa', Auth::id())
    //         ->get()
    //         ->keyBy('detail_pengajuan_id');

    //     $otherEvaluations = HasilEvaluasi::whereIn('detail_pengajuan_id', $detailIds)
    //         ->where('idUserPemeriksa', '!=', Auth::id())
    //         ->get()
    //         ->groupBy('detail_pengajuan_id');

    //     // Fetch the PenunjukanTPAKModel for overall notes
    //     $tpakDosen = Dosen::where('users_id', Auth::id())->first();
    //     $penunjukan = null;
    //     if ($tpakDosen) {
    //         $penunjukan = PenunjukanTPAKModel::where('pengajuan_id', $pengajuan->id)
    //             ->where('idDosenTpak', $tpakDosen->id)
    //             ->first();
    //     }
    //     $overallNotes = $penunjukan->catatan ?? ''; // Pass this to the view

    //     return view('dupak.validasi.show', compact('pengajuan', 'myEvaluations', 'otherEvaluations', 'overallNotes'));
    // }

    public function show($id)
    {
        $pengajuan = Pengajuan::with(['details.komponen', 'dosen'])->findOrFail($id);

        if (!$this->isAuthorizedTpak($pengajuan->id)) {
            abort(403, 'Anda tidak memiliki akses untuk menilai pengajuan ini.');
        }

        $detailIds = $pengajuan->details->pluck('id')->toArray();

        $myEvaluations = HasilEvaluasi::whereIn('detail_pengajuan_id', $detailIds)
            ->where('idUserPemeriksa', Auth::id())
            ->get()
            ->keyBy('detail_pengajuan_id');

        // Ambil evaluasi dari anggota Tim PAK lain
        $otherEvaluations = HasilEvaluasi::whereIn('detail_pengajuan_id', $detailIds)
            ->where('idUserPemeriksa', '!=', Auth::id())
            ->get()
            ->groupBy('detail_pengajuan_id');

        $tpakDosen = Dosen::where('users_id', Auth::id())->first();
        $penunjukan = null;
        if ($tpakDosen) {
            $penunjukan = PenunjukanTPAKModel::where('pengajuan_id', $pengajuan->id)
                ->where('idDosenTpak', $tpakDosen->id)
                ->first();
        }
        $overallNotes = $penunjukan->catatan ?? '';

        return view('dupak.validasi.show', compact('pengajuan', 'myEvaluations', 'otherEvaluations', 'overallNotes'));
    }

    /**
     * Save evaluation for single detail kegiatan (per-component).
     */
    public function saveDetail(Request $request, $pengajuanId, $detailId)
    {
        $pengajuan = Pengajuan::findOrFail($pengajuanId);
        $detail = DetailPengajuan::where('id', $detailId)
            ->where('pengajuan_id', $pengajuanId)
            ->firstOrFail();

        if (!$this->isAuthorizedTpak($pengajuan->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'flag' => 'required|in:OK,Doubt,Rejected',
            'note' => 'nullable|string|max:1000',
        ]);

        $scorePercent = $request->score / 100;
        $approvedCredit = $detail->angka_kredit_total * $scorePercent;

        HasilEvaluasi::updateOrCreate(
            [
                'detail_pengajuan_id' => $detailId,
                'idUserPemeriksa' => Auth::id(),
            ],
            [
                'peran_pemeriksa' => 'TPAK',
                'status_evaluasi' => $request->flag,
                'catatan' => $request->note,
                'nilai_angka_kredit' => $approvedCredit,
            ]
        );

        // Update detail status
        $detailStatus = match ($request->flag) {
            'OK' => 'approved',
            'Doubt' => 'revision',
            'Rejected' => 'rejected',
            default => 'pending',
        };
        $detail->update(['status' => $detailStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Evaluasi disimpan untuk kegiatan ini.',
            'data' => [
                'score' => $request->score,
                'flag' => $request->flag,
                'approved_ak' => round($approvedCredit, 2),
            ]
        ]);
    }

    /**
     * Update the specified resource in storage (batch).
     */
    public function update(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        if (!$this->isAuthorizedTpak($pengajuan->id)) {
            abort(403, 'Anda tidak memiliki akses untuk menilai pengajuan ini.');
        }

        $request->validate([
            'scores.*' => 'nullable|numeric|min:0|max:100',
            'flags.*' => 'nullable|in:OK,Doubt,Rejected',
            'notes.*' => 'nullable|string',
            'overall_notes' => 'nullable|string',
        ]);

        $tpakDosen = Dosen::where('users_id', Auth::id())->first();
        if (!$tpakDosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        $savedCount = 0;

        DB::transaction(function () use ($request, $pengajuan, &$savedCount, $tpakDosen) {
            foreach ($request->scores as $detailId => $score) {
                // if (empty($score) && empty($request->flags[$detailId])) continue;
                if (!isset($score) && empty($request->flags[$detailId])) continue;
                $score = $score ?? 0;

                $flag = $request->flags[$detailId] ?? 'OK';
                $note = $request->notes[$detailId] ?? null;

                $detail = DetailPengajuan::where('id', $detailId)
                    ->where('pengajuan_id', $pengajuan->id)
                    ->first();

                if (!$detail) continue;

                $approvedCredit = $detail->angka_kredit_total * ($score / 100);

                HasilEvaluasi::updateOrCreate(
                    [
                        'detail_pengajuan_id' => $detailId,
                        'idUserPemeriksa' => Auth::id(),
                    ],
                    [
                        'peran_pemeriksa' => 'TPAK',
                        'status_evaluasi' => $flag,
                        'catatan' => $note,
                        'nilai_angka_kredit' => $approvedCredit,
                    ]
                );

                // Update detail status
                $detailStatus = match ($flag) {
                    'OK' => 'approved',
                    'Rejected' => 'rejected',
                    'Doubt' => 'revision',
                    default => 'pending',
                };
                $detail->update(['status' => $detailStatus]);

                $savedCount++;
            }

            // Update catatan TPAK
            $penunjukan = PenunjukanTPAKModel::where('pengajuan_id', $pengajuan->id)
                ->where('idDosenTpak', $tpakDosen->id)
                ->first();

            if ($penunjukan && $request->overall_notes) {
                $penunjukan->update(['catatan' => $request->overall_notes]);
            }
        });

        return redirect()->route('dupak.validasi.show', $pengajuan->id)
            ->with('success', "Berhasil menyimpan {$savedCount} evaluasi kegiatan.");
    }
}
