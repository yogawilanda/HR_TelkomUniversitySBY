<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Dupak\DetailPengajuan;
use App\Models\Dupak\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $search = $request->input('search');
        $userId = Auth::id();

        // Ambil nama database dari config secara langsung untuk keamanan
        $dbSdm = config('database.connections.mysql.database'); // sdm_tus

        // Query dari DetailPengajuan, filter by TPAK user via JOINs
        $query = \App\Models\Dupak\DetailPengajuan::with(['pengajuan.dosen', 'komponen'])
            ->join('pengajuan', 'detail_pengajuan.pengajuan_id', '=', 'pengajuan.id')
            ->join('penunjukan_tpak', 'pengajuan.id', '=', 'penunjukan_tpak.pengajuan_id')
            ->join("$dbSdm.dosens", 'penunjukan_tpak.idDosenTpak', '=', "$dbSdm.dosens.id")
            ->where("$dbSdm.dosens.users_id", $userId)
            ->select('detail_pengajuan.*');

        // Handle search on dosen name
        if ($search) {
            $query->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('nama_dosen', 'LIKE', "%$search%");
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

        // Hasil akhir /dupak/pengajuan/validate/<userid>/<pengajuanid>
        $pengajuan = Pengajuan::with(['details.kegiatan', 'dosen'])->findOrFail($id);

        // dd($pengajuan);

        return view('dupak.validasi.show', compact('pengajuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $pengajuan = Pengajuan::findOrFail($id);

        // Update pengajuan status
        $pengajuan->update([
            'status' => $request->status,
            'catatan' => $request->notes,
        ]);

        // Update angka kredit for each detail
        foreach ($pengajuan->details as $detail) {
            $creditField = $detail->kegiatan->id . '_credit';
            if ($request->has($creditField)) {
                $detail->update([
                    'angka_kredit_disetujui' => $request->input($creditField)
                ]);
            }
        }

        // Calculate and update total approved credit
        $totalApprovedCredit = $pengajuan->details->sum('angka_kredit_disetujui');
        $pengajuan->update([
            'total_angka_kredit_disetujui' => $totalApprovedCredit
        ]);

        return redirect()->route('dupak.validasi.index')
            ->with('success', 'Validasi DUPAK berhasil disimpan.');
    }
}
