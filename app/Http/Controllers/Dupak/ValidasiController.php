<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Dupak\DetailPengajuan;
use App\Models\Dupak\Pengajuan;
use Illuminate\Http\Request;

class ValidasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get all current dosen users_id from the main DB, then display them in the view, with the option to assign them as TPAK for a specific pengajuan. the users_id is exist on the dosens table, but the name is exist on the users table, so we need to join the two tables to get the name of the dosen.
        // Using paginate(10) to support large datasets and provide pagination links in the view.


        $detailPengajuan = DetailPengajuan::all();
        // dd($detailPengajuan);

        // $detailPengajuan = DetailPengajuan::all()->with(['pengajuan.dosen.users']);

        // $dosens = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
        //     ->select('dosens.id', 'users.nama_lengkap')
        //     ->when($search, function ($query, $search) {
        //         return $query->where('users.nama_lengkap', 'like', '%' . $search . '%');
        //     })
        //     ->paginate(5)
        //     ->withQueryString();




        // dd($detailPengajuan);
        // $pengajuan = Pengajuan::all();
        return view('dupak.validasi.index', compact('detailPengajuan'));
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
