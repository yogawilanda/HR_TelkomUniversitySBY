<!-- TPAK Controller -->
<!-- TPAK : Tenaga penilaian angka kredit 
TPAK merupakan badan yang memiliki tugas untuk melakukan penilaian angka kredit bagi guru yang mengajukan kenaikan pangkat. TPAK bertanggung jawab untuk mengevaluasi dan menilai kinerja guru berdasarkan kriteria yang telah ditetapkan, serta memberikan rekomendasi terkait kenaikan pangkat. TPAK juga berperan dalam memastikan bahwa proses penilaian berjalan dengan adil dan transparan, serta memberikan masukan untuk perbaikan sistem penilaian di masa depan.

Dalam konteks aplikasi, TPAKController dapat digunakan untuk mengelola data terkait penilaian angka kredit, seperti menyimpan hasil penilaian, menampilkan daftar guru yang telah dinilai, dan memberikan akses kepada pengguna untuk melihat hasil penilaian mereka. TPAKController juga dapat berfungsi sebagai penghubung antara model data

Aturan TPAK :
1. TPAK memberikan penilaian dari dupak yang telah diajukan oleh dosen, dengan memberikan nilai berupa angka kredit dan catatan.
2. 1 Dupak akan dikelola oleh 2 TPAK.
3. Kriteria menjadi TPAK ditentukan oleh pihak SDM universitas terkait, berdasarkan kualifikasi dan pengalaman yang setara dengan tugas penilaian angka kredit.
4. Untuk sementara, penunjukan TPAK dalam sistem yang sedang dikembangkan menggunakan bukti penunjukan yang perlu diunggah oleh pihak SDM universitas.
-->

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class TPAKController extends Controller
{
    // get all TPAK
    public function index() {}

    public function getAllTPAK()
    {
        $user = Auth::user();

        $allTPAK = $user->tpak()->get();
    }

    public function limitTPAK()
    {
        $user = Auth::user();

        // if the assigned TPAk is more than 2, return response of restriction
        if ($user->tpak()->count() > 2) {
            return response()->json(['message' => 'You have reached the maximum number of TPAK assignments'], 403);
        }
    }

    // ! dummy implentative
    public function assignTPAK(Request $request)
    {
        $request->validate([
            'tpak_id' => 'required|exists:tpaks,id',
            'pengajuan_id' => 'required|exists:pengajuans,id',
        ]);

        $user = Auth::user();

        // check if the dupak has already been assigned to 2 TPAK
        if ($user->tpak()->count() >= 2) {
            return response()->json(['message' => 'You have reached the maximum number of TPAK assignments'], 403);
        }
    }

    // data assignment TPAK apakah disimpan di tabel pengajuan atau tabel dosen?
    /// jika di tabel pengajuan,
    // maka:  
    // 1. create table tpaks dengan kolom id, id_dosen,
    // 2. alter table pengajuan add column tpak_id, dan relasikan dengan tabel tpaks
    // kelebihannya : tidak perlu meminta izin kepada tim proyek bagian kepegawaian untuk mengalterisasi tabel dosen, karena data TPAK hanya akan disimpan di tabel pengajuan, sehingga tidak mempengaruhi data dosen secara keseluruhan.

    // gambaran table tpaks:
    // Table: tpak_assignments
    // | id | pengajuan_id | dosen_id (TPAK) | bukti_penunjukan | nilai | catatan |
    // | :--- | :--- | :--- | :--- | :--- | :--- |
    // | 1 | 101 | 5 (Dosen A) | surat_01.pdf | 4.5 | Good |
    // | 2 | 101 | 9 (Dosen B) | surat_01.pdf | 4.2 | Valid |

    // create table tpaks dengan 
    // kolom id (pk), 
    // id_dosen (FK ke tabel dosen), 
    // bukti_penunjukan (string, 255, nullable), 
    // nilai (float, 4, 2, nullable), 
    // catatan (string, 255, nullable),

    // table pengajuan add column tpak_id
    // | id | dosen_id | tpak_id | ... | ak_akhir | notes
    // | :--- | :--- | :--- | :--- | :--- | :--- |
    // 

    // table pengajuan notes:
    // | id | dosen_id | tpak_id | ... | notes } created_at | updated_at | link_detil_pengajuan
    // | :--- | :--- | :--- | :--- | :--- | :
}
