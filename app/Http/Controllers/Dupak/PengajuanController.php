<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dupak\Pengajuan;
use App\Models\Dosen;
use App\Models\refJabatanFungsionalAkademik;
use App\Models\RiwayatJabatanFungsional;
use App\Models\riwayatJabatanFungsionalAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // If admin open this, then admin can see all submission which called pengajuan.
        // If user which is have to be dosen, then he/she can only his/her own submission.
        $user = Auth::user();

        if ($user->is_admin) {
            $pengajuan = Pengajuan::all();
        } else if ($user->is_dosen) {
            $pengajuan = Pengajuan::where('idDosen', $user->id);
        }


        // 1. Define the base query: fetch all submissions and eager load the cross-DB relationship ('dosen')
        $pengajuanQuery = Pengajuan::with('dosen') // Eager load the Dosen
            ->orderBy('id', 'desc');

        // 2. Execute the query and paginate the results
        $pengajuan = $pengajuanQuery;

        // 3. Pass the Paginator object to the view
        return view('dupak.pengajuan.index', compact('pengajuan', 'user',
        //  'pengajuanTerbaru'
         ));
    }

    // Peta urutan Jabatan Fungsional Akademik (UUID ke Nama Jabatan)
    // URUTAN INI HARUS SESUAI DENGAN KENAIKAN JABATAN YANG SAH.
    // Pastikan UUID di bawah ini sesuai dengan data di tabel ref_jfa Anda!
    protected $aturanPengajuanJFA = [
        // ID Asisten Ahli (Contoh: b467678d-8e9f-4453-bb76-f0cba91468dc)
        'b467678d-8e9f-4453-bb76-f0cba91468dc' => 'Asisten Ahli',

        // ID Lektor (Contoh: f6890047-b0ea-4b45-a9f9-b0584c65bdd6)
        'f6890047-b0ea-4b45-a9f9-b0584c65bdd6' => 'Lektor',

        // ID Lektor Kepala (Contoh: 21ac00aa-1f19-4347-84c1-9e70413209ab)
        '21ac00aa-1f19-4347-84c1-9e70413209ab' => 'Lektor Kepala',

        // ID Guru Besar (Contoh: d6418a5e-b76f-4d67-9990-056e1acabe66)
        'd6418a5e-b76f-4d67-9990-056e1acabe66' => 'Guru Besar (Profesor)',

        // Anda bisa tambahkan jabatan fungsional lain di sini, pastikan urut!
    ];

    public function create()
    {
        // 1. Ambil data Dosen.
        $dosen = Dosen::where('users_id', Auth::id())->first();

        // negatif case : jika user bukan dosen atau dosen tidak ditemukan sudah dihandle di dalam front, jadi halaman tidak akan bisa diakses.
        // namun untuk safety, pengecekan juga dilakukan pada controller ini.
        if (!$dosen) {
            return redirect()->route('dupak.dashboard')->with('error', 'Akses ditolak. Anda bukan Dosen.');
        }

        $nidn = $dosen->nidn ?? 'NIDN Belum Terisi';
        $jabatan_fungsional = 'Belum Ada Riwayat Jabatan';
        $jfa_tujuan = 'Belum Ada Riwayat Jabatan';

        // 2. Ambil riwayat JFA terakhir (pastikan tidak null)
        $riwayat_jfa = RiwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)
            ->latest()
            ->first();

        if ($riwayat_jfa) {
            $jfa_id_saat_ini = $riwayat_jfa->ref_jfa_id;

            // Ambil detail jabatan fungsional saat ini (untuk nama jabatan)
            $refJfaSaatIni = RefJabatanFungsionalAkademik::find($jfa_id_saat_ini);

            if ($refJfaSaatIni) {
                $jabatan_fungsional = $refJfaSaatIni->nama_jabatan;

                // --- Logika Penentuan JFA Tujuan menggunakan Array Map ---

                // Ambil semua kunci (UUID) dari peta urutan
                $jfaKeys = array_keys($this->aturanPengajuanJFA);

                // Cari posisi (index) ID saat ini dalam array kunci
                $currentKeyIndex = array_search($jfa_id_saat_ini, $jfaKeys);

                // Jika ID saat ini ditemukan di map
                if ($currentKeyIndex !== false) {
                    $nextKeyIndex = $currentKeyIndex + 1;

                    // Cek apakah ada index berikutnya (jabatan berikutnya)
                    if (isset($jfaKeys[$nextKeyIndex])) {
                        $nextJfaId = $jfaKeys[$nextKeyIndex];
                        // Ambil nama jabatan dari map
                        $jfa_tujuan = $this->aturanPengajuanJFA[$nextJfaId];
                    } else {
                        // Tidak ada jabatan di atas level ini (sudah tertinggi)
                        $jfa_tujuan = 'Jabatan Tertinggi (Puncak Karir)';
                    }
                } else {
                    // ID JFA saat ini tidak terdaftar di map urutan.
                    $jfa_tujuan = 'Tidak dapat ditentukan (JFA saat ini tidak ada di daftar urutan).';
                }
            }
        }

        // dd($jfa_tujuan);

        return view('dupak.pengajuan.create', compact(
            'nidn',
            'jabatan_fungsional',
            'jfa_tujuan'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $dosen = Dosen::where('users_id', $user->id)->first();

        if (!$dosen) {
            return redirect()->route('dupak.dashboard')->with('error', 'Akses ditolak. Anda bukan Dosen.');
        }

        $riwayat_jfa = RiwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)
            ->latest()
            ->first();

        if ($riwayat_jfa) {
            $jfa_id_saat_ini = $riwayat_jfa->ref_jfa_id;

            // Ambil detail jabatan fungsional saat ini (untuk nama jabatan)
            $refJfaSaatIni = RefJabatanFungsionalAkademik::find($jfa_id_saat_ini);

            if ($refJfaSaatIni) {
                $jabatan_fungsional = $refJfaSaatIni->nama_jabatan;

                // --- Logika Penentuan JFA Tujuan menggunakan Array Map ---

                // Ambil semua kunci (UUID) dari peta urutan
                $jfaKeys = array_keys($this->aturanPengajuanJFA);

                // Cari posisi (index) ID saat ini dalam array kunci
                $currentKeyIndex = array_search($jfa_id_saat_ini, $jfaKeys);

                // Jika ID saat ini ditemukan di map
                if ($currentKeyIndex !== false) {
                    $nextKeyIndex = $currentKeyIndex + 1;

                    // Cek apakah ada index berikutnya (jabatan berikutnya)
                    if (isset($jfaKeys[$nextKeyIndex])) {
                        $nextJfaId = $jfaKeys[$nextKeyIndex];
                        // Ambil nama jabatan dari map
                        $jfa_tujuan = $this->aturanPengajuanJFA[$nextJfaId];
                    } else {
                        // Tidak ada jabatan di atas level ini (sudah tertinggi)
                        $jfa_tujuan = 'Jabatan Tertinggi (Puncak Karir)';
                    }
                } else {
                    // ID JFA saat ini tidak terdaftar di map urutan.
                    $jfa_tujuan = 'Tidak dapat ditentukan (JFA saat ini tidak ada di daftar urutan).';
                }
            }
        }

        // Create new Pengajuan
        $pengajuan = new Pengajuan();
        $pengajuan->idDosen = $dosen->id;
        $pengajuan->status = 'Pending';
        $pengajuan->jfaAsal =  $jfa_id_saat_ini;
        $pengajuan->jfaTujuan =  $nextJfaId;
        // dd($request->all());
        // dd($jfa_id_saat_ini, $nextJfaId);
        $pengajuan->save();

        return redirect()->route('dupak.dashboard')
            ->with('success', 'Pengajuan DUPAK berhasil disimpan.');
    }

    public function show(string $id)
    {   
        // get pengajuan by id
        $pengajuan = Pengajuan::find($id);

        if (!$pengajuan) {
            return redirect()->route('dupak.dashboard')->with('error', 'Pengajuan tidak ditemukan.');
        }

        

        return view('dupak.pengajuan.show');
    }
}
