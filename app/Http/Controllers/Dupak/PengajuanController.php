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
        $dosen = Dosen::where('users_id', $user->id)->first();
        $dosenId = $dosen ? $dosen->id : null;

        $pengajuanQuery = Pengajuan::with('dosen')->orderBy('id', 'desc');

        if (!$user->is_admin) {
            // Hanya tampilkan pengajuan milik dosen yang sedang login
            $pengajuanQuery->where('idDosen', $dosenId);
        }

        $pengajuan = $pengajuanQuery->paginate(10);

        // 3. Pass the Paginator object to the view
        return view('dupak.pengajuan.index', compact('pengajuan', 'user', 'dosenId'));
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
        // get riwayat jfa dosen
        $pengajuan = Pengajuan::with(['dosen', 'details.komponen', 'details.evaluations'])->findOrFail($id);
    
        // Mapping UUID ke Label Nama Jabatan
        $jfaAsalLabel = $this->aturanPengajuanJFA[$pengajuan->jfaAsal] ?? 'Tidak Diketahui';
        $jfaTujuanLabel = $this->aturanPengajuanJFA[$pengajuan->jfaTujuan] ?? 'Tidak Diketahui';

        $riwayatJFA = RiwayatJabatanFungsionalAkademik::where('dosen_id', $pengajuan->idDosen)->latest()->get();

        // Mockup data timeline (Nantinya ambil dari tabel detail_pengajuan & evaluasi)
        $timelineData = [
            /**
             * TAHAP 1: PENGAJUAN DIBUAT
             */
            [
                'id' => 1,
                'title' => 'Pengajuan Dibuat',
                'date' => $pengajuan->created_at->format('d F Y'),
                'content' => "Draft pengajuan DUPAK dari <strong>{$jfaAsalLabel}</strong> ke <strong>{$jfaTujuanLabel}</strong>.",
                'border_color' => 'border-blue-600',
                'is_expanded' => true,
                'details' => null,
            ],
        ];

        // --- ALTERNATE FLOW: Cek apakah sudah ada detail kegiatan ---
        if ($pengajuan->details && $pengajuan->details->count() > 0) {
            $totalKum = $pengajuan->details->sum('angka_kredit_total');
            
            // Ambil semua nama pemeriksa untuk menghindari undefined variable dan query berulang
            $evaluatorIds = $pengajuan->details->flatMap->evaluations->pluck('idUserPemeriksa')->unique();

            // Ambil nama dari tabel User (untuk Admin yang bukan Dosen)
            $namesFromUsers = \App\Models\User::whereIn('id', $evaluatorIds)
                ->get()
                ->mapWithKeys(fn($user) => [
                    $user->id => $user->nama_lengkap ?? $user->nama ?? 'Pemeriksa'
                ])->toArray();

            // Ambil nama dari tabel Dosen (untuk TPAK). idUserPemeriksa merujuk ke ID Dosen.
            $namesFromDosens = Dosen::whereIn('id', $evaluatorIds)
                ->with('pegawai')
                ->get()
                ->mapWithKeys(fn($dosen) => [
                    $dosen->id => $dosen->pegawai->nama_lengkap ?? $dosen->pegawai->nama ?? 'Pemeriksa'
                ])->toArray();

            // Gabungkan kedua hasil pencarian. 
            // Menggunakan operator + memastikan key (UUID) tetap terjaga.
            $evaluatorNames = $namesFromDosens + $namesFromUsers;

            $activityDetails = ['Daftar Kegiatan:'];
            $allEvaluations = [];

            foreach ($pengajuan->details as $detail) {
                // Display setiap detail kegiatan dengan format yang lebih menarik
                $activityDetails[] = [
                    "<strong>{$detail->deskripsi_kegiatan}</strong> (" . number_format($detail->angka_kredit_total, 2) . " KUM)"
                ];
                // Ambil evaluasi untuk detail ini
                foreach ($detail->evaluations as $eval) {
                    $pemeriksa = $evaluatorNames[$eval->idUserPemeriksa] ?? 'Pemeriksa Terdaftar';
                    $allEvaluations[] = [
                        'role' => "{$eval->peran_pemeriksa} ({$pemeriksa})",
                        'status' => $eval->status_evaluasi,
                        'comment' => $eval->catatan,
                    ];
                }
            }

            $timelineData[] = [
                'id' => 2,
                'title' => 'Proses Penilaian Kegiatan',
                'date' => $pengajuan->updated_at->format('d F Y'),
                'content' => "Terdapat <strong>{$pengajuan->details->count()} kegiatan</strong> yang sedang diproses dengan total <strong>" . number_format($totalKum, 2) . " KUM</strong>.",
                'border_color' => 'border-emerald-500',
                'is_expanded' => true,
                'details' => $activityDetails,
                'evaluation' => $allEvaluations
            ];
        } else {
            // Tampilkan informasi jika data masih kosong (Alternate Flow)
            $timelineData[] = [
                'id' => 2,
                'title' => 'Menunggu Input Detail Kegiatan',
                'date' => '-',
                'content' => 'Belum ada detail kegiatan (Pendidikan, Penelitian, dll) yang ditambahkan ke dalam pengajuan ini.',
                'border_color' => 'border-gray-400',
                'is_expanded' => false,
                'details' => null,
            ];
        }

        // Tahap Akhir jika status sudah Diterima
        if ($pengajuan->status === 'Diterima') {
            $timelineData[] = [
                'id' => 3,
                'title' => 'Pengajuan Disetujui & Selesai',
                'date' => $pengajuan->updated_at->format('d F Y'),
                'content' => "Selamat! Pengajuan Anda telah disetujui. Anda sekarang menjabat sebagai <strong>{$jfaTujuanLabel}</strong>.",
                'dot_color' => 'bg-green-500',
                'border_color' => 'border-purple-600',
                'is_expanded' => true,
                'details' => [
                    'type' => 'button',
                    'label' => 'Unduh SK Jabatan',
                    'button_color' => 'bg-purple-600',
                ],
            ];
        }

        return view('dupak.pengajuan.show', compact('pengajuan', 'timelineData'));
    }
}
