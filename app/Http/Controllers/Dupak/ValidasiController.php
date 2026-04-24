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
        $userId = Auth::id();

        $dbSdm = config('database.connections.mysql.database');

        $query = \App\Models\Dupak\DetailPengajuan::with(['pengajuan.dosen', 'komponen'])
            ->join('pengajuan', 'detail_pengajuan.pengajuan_id', '=', 'pengajuan.id')
            ->join('penunjukan_tpak', 'pengajuan.id', '=', 'penunjukan_tpak.pengajuan_id')
            ->join("{$dbSdm}.dosens", 'penunjukan_tpak.idDosenTpak', '=', "{$dbSdm}.dosens.id")
            ->where("{$dbSdm}.dosens.users_id", $userId)
            ->select('detail_pengajuan.*');

        if ($search) {
            $query->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('nama_dosen', 'LIKE', "%{$search}%");
            });
        }

        $detailPengajuanTPAK = $query->orderBy('detail_pengajuan.created_at', 'desc')->paginate(15);

        return view('dupak.validasi.index', compact('detailPengajuanTPAK'));
    }

    /**
     * Display the specified resource.
     */
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

        $otherEvaluations = HasilEvaluasi::whereIn('detail_pengajuan_id', $detailIds)
            ->where('idUserPemeriksa', '!=', Auth::id())
            ->get()
            ->groupBy('detail_pengajuan_id');

        return view('dupak.validasi.show', compact('pengajuan', 'myEvaluations', 'otherEvaluations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        if (!$this->isAuthorizedTpak($pengajuan->id)) {
            abort(403, 'Anda tidak memiliki akses untuk menilai pengajuan ini.');
        }

        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100',
            'flags' => 'required|array',
            'flags.*' => 'nullable|in:OK,Doubt,Fake',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string',
            'overall_notes' => 'nullable|string',
        ]);

        $tpakDosen = Dosen::where('users_id', Auth::id())->first();
        if (!$tpakDosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        $savedCount = 0;

        try {
            DB::beginTransaction();

            foreach ($request->scores as $detailId => $score) {
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

                $savedCount++;
            }

            // Simpan catatan umum ke record penunjukan TPAK yang bersangkutan
            $penunjukan = PenunjukanTPAKModel::where('pengajuan_id', $pengajuan->id)
                ->where('idDosenTpak', $tpakDosen->id)
                ->first();

            if ($penunjukan) {
                $penunjukan->update([
                    'catatan' => $request->overall_notes ?? $penunjukan->catatan,
                ]);
            }

            DB::commit();

            // Redirect kembali ke halaman show agar user lihat flash message & hasil
            return redirect()->route('dupak.validasi.show', $pengajuan->id)
                ->with('success', "Validasi berhasil disimpan. {$savedCount} detail kegiatan dinilai.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan validasi TPAK', [
                'message' => $e->getMessage(),
                'pengajuan_id' => $id,
                'tpak_id' => Auth::id(),
            ]);

            $debug = config('app.debug') ? ' [DEBUG: ' . $e->getMessage() . ']' : '';

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan validasi. Silakan coba lagi.' . $debug);
        }
    }
}
