<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dupak\Pengajuan;
use App\Models\Dosen;
use App\Models\refJabatanFungsionalAkademik;
use App\Models\Dupak\RefKegiatanUtama;
use App\Models\RiwayatJabatanFungsional;
use App\Models\riwayatJabatanFungsionalAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        $kegiatanUtama = RefKegiatanUtama::with('komponens')->where('status', 1)->get();

        // Cek apakah dosen sudah Guru Besar (jabatan tertinggi)
        $dosen = Dosen::where('users_id', $user->id)->first();
        $isMaxJfa = $dosen ? $this->isMaxJfa($dosen) : false;

        // 3. Pass the Paginator object to the view
        return view('dupak.pengajuan.index', compact('pengajuan', 'user', 'dosenId', 'kegiatanUtama', 'isMaxJfa'));
    }

    // Peta urutan Jabatan Fungsional Akademik (UUID ke Nama Jabatan)
    // URUTAN INI HARUS SESUAI DENGAN KENAIKAN JABATAN YANG SAH.
    // Pastikan UUID di bawah ini sesuai dengan data di tabel ref_jfa Anda!
    protected $aturanPengajuanJFA = [
        // ID NJAD (8a7c0b44-2c2e-4a16-a4df-111111111111)
        '8a7c0b44-2c2e-4a16-a4df-111111111111' => 'Non JAD',

        // ID Asisten Ahli (b467678d-8e9f-4453-bb76-f0cba91468dc)
        'b467678d-8e9f-4453-bb76-f0cba91468dc' => 'Asisten Ahli',

        // ID Lektor (f6890047-b0ea-4b45-a9f9-b0584c65bdd6)
        'f6890047-b0ea-4b45-a9f9-b0584c65bdd6' => 'Lektor',

        // ID Lektor Kepala (21ac00aa-1f19-4347-84c1-9e70413209ab)
        '21ac00aa-1f19-4347-84c1-9e70413209ab' => 'Lektor Kepala',

        // ID Guru Besar (d6418a5e-b76f-4d67-9990-056e1acabe66)
        'd6418a5e-b76f-4d67-9990-056e1acabe66' => 'Guru Besar (Profesor)',

        // Anda bisa tambahkan jabatan fungsional lain di sini, pastikan urut!
    ];

    /**
     * Cek apakah dosen sudah mencapai jabatan fungsional tertinggi (Guru Besar).
     */
    private function isMaxJfa(Dosen $dosen): bool
    {
        $riwayat_jfa = RiwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)
            ->whereNull('tmt_selesai') // Pastikan hanya JFA aktif
            ->latest('tmt_mulai') // Ambil yang terbaru berdasarkan tanggal mulai
            ->first();

        if (!$riwayat_jfa) return false;

        $jfaKeys = array_keys($this->aturanPengajuanJFA);
        $lastJfaId = end($jfaKeys);

        return $riwayat_jfa->ref_jfa_id === $lastJfaId;
    }

    /**
     * Menentukan ID JFA tujuan berikutnya berdasarkan JFA aktif dosen saat ini.
     * Mengembalikan UUID JFA tujuan atau null jika sudah di jabatan tertinggi.
     */
    private function getNextJfaId(Dosen $dosen): ?string
    {
        $riwayat_jfa = RiwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)
            ->whereNull('tmt_selesai') // Pastikan hanya JFA aktif
            ->latest('tmt_mulai') // Ambil yang terbaru berdasarkan tanggal mulai
            ->first();

        if (!$riwayat_jfa) {
            // Jika tidak ada JFA aktif, asumsikan NJAD dan target Asisten Ahli (indeks 1 di map)
            return array_keys($this->aturanPengajuanJFA)[1] ?? null;
        }

        $jfa_id_saat_ini = $riwayat_jfa->ref_jfa_id;
        $jfaKeys = array_keys($this->aturanPengajuanJFA);
        $currentKeyIndex = array_search($jfa_id_saat_ini, $jfaKeys);

        if ($currentKeyIndex !== false) {
            $nextKeyIndex = $currentKeyIndex + 1;
            if (isset($jfaKeys[$nextKeyIndex])) {
                return $jfaKeys[$nextKeyIndex];
            }
        }
        return null; // Sudah di jabatan tertinggi atau JFA tidak ditemukan di map
    }

    public function create()
    {
        // 1. Ambil data Dosen.
        $dosen = Dosen::where('users_id', Auth::id())->first();

        // negatif case : jika user bukan dosen atau dosen tidak ditemukan sudah dihandle di dalam front, jadi halaman tidak akan bisa diakses.
        // namun untuk safety, pengecekan juga dilakukan pada controller ini.
        if (!$dosen) {
            return redirect()->route('dupak.dashboard')->with('error', 'Akses ditolak. Anda bukan Dosen.');
        }

        // Validasi keras: Guru Besar tidak boleh mengajukan kenaikan jabatan lagi.
        if ($this->isMaxJfa($dosen)) {
            return redirect()->route('dupak.dashboard')
                ->with('error', 'Anda sudah mencapai jabatan fungsional tertinggi (Guru Besar). Pengajuan kenaikan jabatan tidak diperbolehkan.');
        }

        $nidn = $dosen->nidn ?? 'NIDN Belum Terisi';
        $jabatan_fungsional = 'Belum Ada Riwayat Jabatan';
        $jfa_tujuan = 'Belum Ada Riwayat Jabatan';

        // 2. Ambil riwayat JFA terakhir (pastikan tidak null)
        $riwayat_jfa_aktif = RiwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)
            ->whereNull('tmt_selesai')
            ->latest('tmt_mulai')
            ->first();

        if ($riwayat_jfa_aktif) {
            $jabatan_fungsional = RefJabatanFungsionalAkademik::find($riwayat_jfa_aktif->ref_jfa_id)->nama_jabatan ?? 'Tidak Diketahui';
            $nextJfaId = $this->getNextJfaId($dosen);
            if ($nextJfaId) {
                $jfa_tujuan = $this->aturanPengajuanJFA[$nextJfaId];
            } else {
                $jfa_tujuan = 'Jabatan Tertinggi (Puncak Karir)';
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

        // Ambil JFA aktif saat ini
        $riwayat_jfa_aktif = RiwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)
            ->whereNull('tmt_selesai')
            ->latest('tmt_mulai')
            ->first();

        if (!$riwayat_jfa_aktif) {
            return redirect()->route('dupak.dashboard')
                ->with('error', 'Anda belum memiliki riwayat Jabatan Fungsional Akademik aktif.');
        }

        // Create new Pengajuan
        // Validasi keras: Guru Besar tidak boleh mengajukan kenaikan jabatan lagi.
        if ($this->isMaxJfa($dosen)) {
            return redirect()->route('dupak.dashboard')
                ->with('error', 'Anda sudah mencapai jabatan fungsional tertinggi (Guru Besar). Pengajuan kenaikan jabatan tidak diperbolehkan.');
        }

        $nextJfaId = $this->getNextJfaId($dosen);
        if (!$nextJfaId) {
            return redirect()->route('dupak.dashboard')
                ->with('error', 'Tidak dapat menentukan jabatan tujuan. Anda mungkin sudah mencapai jabatan tertinggi.');
        }

        $today = Carbon::now();
        $currentYear = date('Y');
        $targetYear = $currentYear + 20;

        $pengajuan = new Pengajuan();
        $pengajuan->idDosen = $dosen->id;
        $pengajuan->start = $today->format('Y-m-d');
        $pengajuan->end   = $today->copy()->addYears(20)->format('Y-m-d');
        $pengajuan->TahunAjaranAjuanAwal = $currentYear . '/' . ($currentYear + 1);
        $targetYear = $currentYear + 20;
        $pengajuan->TahunAjaranAjuanAkhir = $targetYear . '/' . ($targetYear + 1);
        $pengajuan->semesterAjuan = collect(['Ganjil', 'Genap'])->random();
        $pengajuan->status = 'Pending';
        $pengajuan->jfaAsal = $riwayat_jfa_aktif->ref_jfa_id;
        $pengajuan->jfaTujuan = $nextJfaId;
        // dd($pengajuan);
        $pengajuan->save();

        return redirect()->route('dupak.dashboard')
            ->with('success', 'Pengajuan DUPAK berhasil disimpan.');
    }

    /**
     * Kirim pengajuan dari status Draft/Pending ke Diajukan.
     */
    public function submit(string $id)
    {
        $user = Auth::user();
        $dosen = Dosen::where('users_id', $user->id)->first();

        if (!$dosen) {
            return redirect()->route('dupak.dashboard')->with('error', 'Akses ditolak. Anda bukan Dosen.');
        }

        $pengajuan = Pengajuan::where('id', $id)
            ->where('idDosen', $dosen->id)
            ->first();

        if (!$pengajuan) {
            return redirect()->route('dupak.dashboard')->with('error', 'Pengajuan tidak ditemukan atau bukan milik Anda.');
        }

        $allowedStatuses = ['Draft', 'Pending', 'Revisi'];
        if (!in_array($pengajuan->status, $allowedStatuses)) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat dikirim. Status saat ini: ' . $pengajuan->status);
        }

        // Opsional: cek minimal ada detail kegiatan
        if ($pengajuan->details()->count() === 0) {
            return redirect()->back()->with('error', 'Tidak dapat mengirim pengajuan. Tambahkan minimal satu detail kegiatan terlebih dahulu.');
        }

        $pengajuan->update(['status' => 'Diajukan']);

        return redirect()->route('dupak.pengajuan.show', $pengajuan->id)
            ->with('success', 'Pengajuan berhasil dikirim! Menunggu penilaian TPAK.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $dosen = Dosen::where('users_id', $user->id)->first();

        $pengajuan = Pengajuan::findOrFail($id);

        // Hanya pemilik atau admin yang bisa edit
        if (!$user->is_admin && $pengajuan->idDosen !== ($dosen?->id)) {
            return redirect()->route('dupak.dashboard')->with('error', 'Akses ditolak. Anda bukan pemilik pengajuan ini.');
        }

        // Hanya status Draft atau Revisi yang bisa diedit
        if (!in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat diedit. Status saat ini: ' . $pengajuan->status);
        }

        return view('dupak.pengajuan.edit', compact('pengajuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $dosen = Dosen::where('users_id', $user->id)->first();

        $pengajuan = Pengajuan::findOrFail($id);

        if (!$user->is_admin && $pengajuan->idDosen !== ($dosen?->id)) {
            return redirect()->route('dupak.dashboard')->with('error', 'Akses ditolak. Anda bukan pemilik pengajuan ini.');
        }

        if (!in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat diupdate. Status saat ini: ' . $pengajuan->status);
        }

        $validated = $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'TahunAjaranAjuanAwal' => 'nullable|string|max:10',
            'TahunAjaranAjuanAkhir' => 'nullable|string|max:10',
            'semesterAjuan' => 'nullable|string|max:50',
        ]);

        $pengajuan->update($validated);

        return redirect()->route('dupak.pengajuan.show', $pengajuan->id)
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $dosen = Dosen::where('users_id', $user->id)->first();

        $pengajuan = Pengajuan::findOrFail($id);

        if (!$user->is_admin && $pengajuan->idDosen !== ($dosen?->id)) {
            return redirect()->route('dupak.dashboard')->with('error', 'Akses ditolak. Anda bukan pemilik pengajuan ini.');
        }

        // Hanya pengajuan dengan status Draft/Pending/Revisi yang bisa dihapus
        if (!in_array($pengajuan->status, ['Draft', 'Pending', 'Revisi'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak dapat dihapus. Status saat ini: ' . $pengajuan->status);
        }

        // Hapus hasil evaluasi terkait (must be BEFORE detail_pengajuan deletion)
        \App\Models\Dupak\HasilEvaluasi::whereIn('detail_pengajuan_id', function ($query) use ($pengajuan) {
            $query->select('id')->from('detail_pengajuan')->where('pengajuan_id', $pengajuan->id);
        })->delete();

        // Hapus detail kegiatan terkait
        $pengajuan->details()->delete();

        // Hapus penunjukan TPAK terkait
        \App\Models\Dupak\PenunjukanTPAKModel::where('pengajuan_id', $pengajuan->id)->delete();

        $pengajuan->delete();

        return redirect()->route('dupak.pengajuan.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function show(string $id)
    {
        // get riwayat jfa dosen
        $pengajuan = Pengajuan::with(['dosen', 'details.komponen.kegiatanUtama', 'details.evaluations'])->findOrFail($id);

        // Mapping UUID ke Label Nama Jabatan
        $jfaAsalLabel = $this->aturanPengajuanJFA[$pengajuan->jfaAsal] ?? 'Tidak Diketahui';
        $jfaTujuanLabel = $this->aturanPengajuanJFA[$pengajuan->jfaTujuan] ?? 'Tidak Diketahui';

        // --- Hitung Data KUM untuk Visualisasi Grafis (berdasarkan Pengaju) ---
        $dosenPengaju = $pengajuan->dosen;
        $baseKum = (float) ($dosenPengaju->user->kum ?? 0);

        // Ambil ID detail yang sudah dinilai TPAK untuk pengajuan ini
        $evaluatedIds = \App\Models\Dupak\HasilEvaluasi::join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
            ->where('detail_pengajuan.pengajuan_id', $pengajuan->id)
            ->where('hasil_evaluasi.peran_pemeriksa', 'TPAK')
            ->pluck('detail_pengajuan.id');

        // KUM Pending: Butir kegiatan yang BELUM dinilai sama sekali oleh TPAK di pengajuan ini
        $kumPengajuanVal = (float) $pengajuan->details()
            ->whereNotIn('id', $evaluatedIds)
            ->sum('angka_kredit_total');

        // KUM Disetujui: Akumulasi nilai yang sudah diberikan TPAK di pengajuan ini
        $kumDisetujuiVal = (float) \App\Models\Dupak\HasilEvaluasi::join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
            ->where('detail_pengajuan.pengajuan_id', $pengajuan->id)
            ->where('hasil_evaluasi.peran_pemeriksa', 'TPAK')
            ->groupBy('hasil_evaluasi.detail_pengajuan_id')
            ->selectRaw('AVG(hasil_evaluasi.nilai_angka_kredit) as avg_nilai')
            ->get()
            ->sum('avg_nilai');

        $targetKumRecord = \App\Models\Dupak\RefTargetJabatanPengajuan::where('jfaAsal', $pengajuan->jfaAsal)
            ->where('jfaTujuan', $pengajuan->jfaTujuan)
            ->first();
        $targetKumVal = $targetKumRecord->kumTarget ?? 0;

        $currentTotalKum = $baseKum + $kumDisetujuiVal;
        $percent = $targetKumVal > 0 ? min(100, ($currentTotalKum / $targetKumVal) * 100) : 0;

        // Breakdown KUM per kategori (Gunakan nilai ACC jika sudah dinilai, atau nilai Ajuan jika belum)
        $allDetails = $pengajuan->details;
        $evaluationsMap = \App\Models\Dupak\HasilEvaluasi::join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
            ->where('detail_pengajuan.pengajuan_id', $pengajuan->id)
            ->where('hasil_evaluasi.peran_pemeriksa', 'TPAK')
            ->select('detail_pengajuan_id', 'nilai_angka_kredit')
            ->get()
            ->groupBy('detail_pengajuan_id');

        $breakdown = [
            'Pendidikan' => 0,
            'Pelaksanaan Pendidikan' => 0,
            'Pelaksanaan Penelitian' => 0,
            'Pelaksanaan Pengabdian' => 0,
            'Pelaksanaan Penunjang' => 0,
        ];

        foreach ($allDetails as $detail) {
            $rawCat = strtolower($detail->komponen->kegiatanUtama->nama ?? '');
            $targetKey = 'Pelaksanaan Penunjang'; // Default

            if (str_contains($rawCat, 'pelaksanaan pendidikan')) $targetKey = 'Pelaksanaan Pendidikan';
            elseif (str_contains($rawCat, 'pelaksanaan penelitian') || str_contains($rawCat, 'penelitian')) $targetKey = 'Pelaksanaan Penelitian';
            elseif (str_contains($rawCat, 'pengabdian')) $targetKey = 'Pelaksanaan Pengabdian';
            elseif (str_contains($rawCat, 'pendidikan')) $targetKey = 'Pendidikan';
            elseif (str_contains($rawCat, 'penunjang')) $targetKey = 'Pelaksanaan Penunjang';

            $val = $evaluationsMap->has($detail->id) ? (float)$evaluationsMap->get($detail->id)->avg('nilai_angka_kredit') : (float)$detail->angka_kredit_total;
            $breakdown[$targetKey] += $val;
        }

        $kumStats = [
            'base_kum' => number_format($baseKum, 2),
            'current_total' => number_format($currentTotalKum, 2),
            'approved_this_submission' => number_format($kumDisetujuiVal, 2),
            'pending_this_submission' => number_format($kumPengajuanVal, 2),
            'target' => number_format($targetKumVal, 2),
            'remaining' => number_format(max(0, $targetKumVal - $currentTotalKum), 2),
            'percent' => $percent,
            'jfa_asal' => $jfaAsalLabel,
            'jfa_tujuan' => $jfaTujuanLabel,
            'breakdown' => $breakdown
        ];

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
            $evaluatorNames = [];
            $tpakEvaluatorIds = $pengajuan->details->flatMap->evaluations->where('peran_pemeriksa', 'TPAK')->pluck('idUserPemeriksa')->unique();
            $adminEvaluatorIds = $pengajuan->details->flatMap->evaluations->where('peran_pemeriksa', 'Admin')->pluck('idUserPemeriksa')->unique();

            if ($tpakEvaluatorIds->isNotEmpty()) {
                $namesFromDosens = Dosen::whereIn('id', $tpakEvaluatorIds)
                    ->with('user') // Asumsi Dosen memiliki relasi ke User
                    ->get()
                    ->mapWithKeys(fn($dosen) => [
                        $dosen->id => $dosen->user->nama_lengkap ?? $dosen->user->nama ?? 'Pemeriksa TPAK'
                    ])->toArray();
                $evaluatorNames = $evaluatorNames + $namesFromDosens;
            }

            if ($adminEvaluatorIds->isNotEmpty()) {
                $namesFromUsers = \App\Models\User::whereIn('id', $adminEvaluatorIds)
                    ->get()
                    ->mapWithKeys(fn($user) => [
                        $user->id => $user->nama_lengkap ?? $user->nama ?? 'Pemeriksa Admin'
                    ])->toArray();
                $evaluatorNames = $evaluatorNames + $namesFromUsers;
            }

            // Build structured activity items for timeline display
            $activityItems = [];
            $allEvaluations = [];

            foreach ($pengajuan->details as $detail) {
                $detailEvals = [];
                foreach ($detail->evaluations as $eval) {
                    $pemeriksa = $evaluatorNames[$eval->idUserPemeriksa] ?? 'Pemeriksa Terdaftar';
                    $detailEvals[] = [
                        'role'    => "{$eval->peran_pemeriksa} ({$pemeriksa})",
                        'status'  => $eval->status_evaluasi,
                        'comment' => $eval->catatan,
                    ];
                    $allEvaluations[] = [
                        'role'    => "{$eval->peran_pemeriksa} ({$pemeriksa})",
                        'status'  => $eval->status_evaluasi,
                        'comment' => $eval->catatan,
                    ];
                }
                $activityItems[] = [
                    'deskripsi'   => $detail->deskripsi_kegiatan,
                    'komponen'    => $detail->komponen->nama ?? 'N/A',
                    'kum'         => number_format($detail->angka_kredit_total, 2),
                    'status'      => $detail->status,
                    'evaluations' => $detailEvals,
                ];
            }

            $timelineData[] = [
                'id'           => 2,
                'title'        => 'Proses Penilaian Kegiatan',
                'date'         => $pengajuan->updated_at->format('d F Y'),
                'content'      => "Terdapat <strong>{$pengajuan->details->count()} kegiatan</strong> yang sedang diproses dengan total <strong>" . number_format($totalKum, 2) . " KUM</strong>.",
                'border_color' => 'border-emerald-500',
                'is_expanded'  => true,
                'activity_items' => $activityItems,
                'evaluation'   => $allEvaluations,
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

        $kegiatanUtama = RefKegiatanUtama::with('komponens')->where('status', 1)->get();

        return view('dupak.pengajuan.show', compact('pengajuan', 'timelineData', 'kumStats', 'kegiatanUtama', 'jfaAsalLabel', 'jfaTujuanLabel'));
    }
}
