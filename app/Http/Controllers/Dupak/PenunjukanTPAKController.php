<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenunjukanTPAKController extends Controller
{
    // get all TPAK
    public function index(Request $request)
    {
        $search = $request->input('search');

        // get all current dosen users_id from the main DB, then display them in the view, with the option to assign them as TPAK for a specific pengajuan. the users_id is exist on the dosens table, but the name is exist on the users table, so we need to join the two tables to get the name of the dosen.
        // Using paginate(10) to support large datasets and provide pagination links in the view.
        $dosens = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
            ->select('dosens.id', 'users.nama_lengkap')
            ->when($search, function ($query, $search) {
                return $query->where('users.nama_lengkap', 'like', '%' . $search . '%');
            })
            ->paginate(5)
            ->withQueryString();

        $user = Auth::user();
        $pengajuan = DB::connection('dupak')->table('pengajuan')->get();
        $penunjukanTpak = DB::connection('dupak')->table('penunjukan_tpak')->paginate(5);

        return view(
            'dupak.penunjukan_tpak.show',
            compact('dosens', 'user', 'pengajuan', 'penunjukanTpak')
        );
    }

    public function create()
    {
        return view('dupak.penunjukan_tpak.create');
    }

    public function getAllTPAK()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json($user->tpak()->get());
    }

    public function limitTPAK()
    {
        $user = Auth::user();

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

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

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

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
