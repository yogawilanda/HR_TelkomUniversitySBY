<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
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
        $detailPengajuan = DetailPengajuan::all();


        // dd($detailPengajuan);
        // $pengajuan = Pengajuan::all();
        return view('dupak.validasi.index', compact('detailPengajuan'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pengajuan = Pengajuan::with(['details.kegiatan', 'dosen'])
            ->findOrFail($id);

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
