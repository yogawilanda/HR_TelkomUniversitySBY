<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Dupak\DetailPengajuan;
use App\Models\Dupak\Pengajuan;
use App\Models\Dupak\RefKegiatanKomponen;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetilPengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // The view folder uses underscores (pengisian_detil_pengajuan). Return the correct view.
        return view('dupak.pengisian_detil_pengajuan.create');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $getPengajuan = Pengajuan::find($id);
        if (!$getPengajuan) {
            return redirect()->back()->with('error', 'Pengajuan not found.');
        }
        $id = $getPengajuan->id;
        return view('dupak.pengisian_detil_pengajuan.show', ['id' => $id]);
    }

    /**
     * Menampilkan form dinamis berdasarkan kategori (pendidikan, penelitian, dsb)
     */
    public function showForm(Request $request, $category, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        // Mapping category slugs to idKegiatanUtama
        $categoryMap = [
            'pendidikan' => 1,
            'pelaksanaan_pendidikan' => 2,
            'penelitian' => 3,
            'pengabdian' => 4,
            'penunjang' => 5
        ];

        $idUtama = $categoryMap[strtolower($category)] ?? null;
        $komponenId = $request->query('komponen_id');

        // dd($category, $pengajuan->id);

        // Ambil komponen berdasarkan ID dan pastikan sesuai dengan kategori utama
        $komponen = RefKegiatanKomponen::where('id', $komponenId)
            ->where('idKegiatanUtama', $idUtama)
            ->firstOrFail();

        // Fetch specific activity items (S1, S2, etc.) based on the component ID
        $jenisInputs = DB::connection('dupak')
            ->table('ref_jenis_input')
            ->where('idKomponen', $komponen->id)
            ->get(); // This populates the "Detail Butir Kegiatan" dropdown
        // dd($jenisInputs);

        return view('dupak.pengisian_detil_pengajuan.generic_form', compact('pengajuan', 'komponen', 'jenisInputs', 'category'));
    }

    /**
     */
    public function store(Request $request, $category, $id)
    {
        $user = Auth::user();
        $dosen = Dosen::where('users_id', $user->id)->first();

        if (!$dosen) {
            return redirect()->route('dupak.dashboard')->with('error', 'Akses ditolak. Anda bukan Dosen.');
        }

        $request->validate([
            'id_komponen' => 'required',
            'id_jenis_input' => 'required',
            'deskripsi_kegiatan' => 'required|string',
            'link_bukti_pendukung' => 'required|url',
            'volume' => 'nullable|numeric',
        ]);

        // Cari nilai baku (Angka Kredit) dari ref_jenis_input
        $refInput = DB::connection('dupak')
            ->table('ref_jenis_input')
            ->where('id', $request->id_jenis_input)
            ->first();

        $nilaiBaku = $refInput->nilai_baku ?? 0;
        $volume = $request->input('volume', 1);

        $detail = new DetailPengajuan();
        $detail->setConnection('dupak'); // Pastikan menyimpan ke DB dupak
        $detail->pengajuan_id = $id;
        $detail->idKomponen = $request->id_komponen;
        $detail->idJenisInput = $request->id_jenis_input;
        $detail->deskripsi_kegiatan = $request->deskripsi_kegiatan;
        $detail->angka_kredit_murni = $nilaiBaku;
        $detail->angka_kredit_total = $nilaiBaku * $volume;
        $detail->status = 'pending';
        $detail->link_bukti_pendukung = $request->link_bukti_pendukung;
        $detail->volume = $volume;
        $detail->save();

        // return redirect()->route('dupak.pengajuan.show', $id)
        // ->with('success', 'Detail kegiatan berhasil ditambahkan.');

        // return to dashboard.dupak
        return redirect()->route('dupak.dashboard')->with('success', 'Detail kegiatan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}
}
