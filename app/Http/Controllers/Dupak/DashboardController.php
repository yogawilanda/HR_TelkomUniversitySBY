<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dupak\Pengajuan;
use App\Models\Dosen;
use App\Models\Dupak\RefJenisInput;
use App\Models\Dupak\RefKegiatanKomponen;
use App\Models\Dupak\RefTargetJabatanPengajuan;
use App\Models\refJabatanFungsionalAkademik;
use App\Models\Dupak\RefKegiatanUtama;
use App\Models\Dupak\PenunjukanTPAKModel;
use App\Models\riwayatJabatanFungsionalAkademik;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $aturanPengajuanJFA = [
        '8a7c0b44-2c2e-4a16-a4df-111111111111' => 'Non JAD',
        'b467678d-8e9f-4453-bb76-f0cba91468dc' => 'Asisten Ahli',
        'f6890047-b0ea-4b45-a9f9-b0584c65bdd6' => 'Lektor',
        '21ac00aa-1f19-4347-84c1-9e70413209ab' => 'Lektor Kepala',
        'd6418a5e-b76f-4d67-9990-056e1acabe66' => 'Guru Besar (Profesor)',
    ];

    private function getDosen(User $user)
    {
        return Dosen::where('users_id', $user->id)->first();
    }

    private function getCurrentJFA(?Dosen $dosen)
    {
        return $dosen
            ? riwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)
                ->whereNull('tmt_selesai')
                ->latest()
                ->first()
            : null;
    }

    private function getJfaTujuan(?string $jfaId)
    {
        if (!$jfaId) return null;

        $keys = array_keys($this->aturanPengajuanJFA);
        $i = array_search($jfaId, $keys);

        return ($i !== false && isset($keys[$i + 1])) ? $keys[$i + 1] : null;
    }

    private function getTargetKum(?string $asal, ?string $tujuan, $minimal)
    {
        if (!$asal || !$tujuan) return $minimal;

        $record = RefTargetJabatanPengajuan::where('jfaAsal', $asal)
            ->where('jfaTujuan', $tujuan)
            ->first();

        return $record->kumTarget ?? $minimal;
    }

    private function buildProgress($current, $goal)
    {
        // Pastikan goal tidak nol untuk menghindari division by zero error
        $goal = (float) $goal;
        $current = (float) $current;
        $percent = $goal > 0 ? min(100, ($current / $goal) * 100) : 0;

        return [
            'current' => number_format($current, 2, '.', ''),
            'goal' => number_format($goal, 2, '.', ''),
            'remaining' => number_format(max(0, $goal - $current), 2, '.', ''),
            'percent' => $percent,
            'statusColor' => $percent >= 100 ? 'bg-green-600' : ($percent >= 60 ? 'bg-yellow-500' : 'bg-indigo-600'),
        ];
    }

    private function submissions(User $user, ?string $dosenId)
    {   
        // fixing sorting algorithm for 2 layered sorts.
        $q = Pengajuan::with(['dosen.pegawai'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (!$user->is_admin) {
            $q->where('idDosen', $dosenId ?? '___INVALID___');
        }

        return $q;
    }

    private function hasPendingSubmission(?string $dosenId): bool
    {
        if (!$dosenId) return false;

        $pendingStatuses = ['Draft', 'Pending', 'Diajukan', 'Revisi', 'Menunggu'];

        return Pengajuan::where('idDosen', $dosenId)
            ->whereIn('status', $pendingStatuses)
            ->exists();
    }

    /**
     * Cek apakah dosen sudah mencapai jabatan fungsional tertinggi (Guru Besar).
     * UUID "d6418a5e-b76f-4d67-9990-056e1acabe66" = Guru Besar (Profesor)
     */
    private function isMaxJfa(?Dosen $dosen): bool
    {
        if (!$dosen) return false;

        $riwayat = $this->getCurrentJFA($dosen);
        $jfaId = $riwayat?->ref_jfa_id;

        // Guru Besar adalah elemen terakhir pada map urutan JFA
        $jfaKeys = array_keys($this->aturanPengajuanJFA);
        $lastJfaId = end($jfaKeys);

        return $jfaId === $lastJfaId;
    }

    private function getLatestSubmission(User $user, ?Dosen $dosen)
    {
        $query = Pengajuan::query();

        if ($user->is_admin && !$dosen) {
            return $query->latest()->first();
        }

        return $query->where('idDosen', $dosen?->id)->latest()->first();
    }

    private function getJfaAndKumData(?Dosen $dosen, float $currentKum)
    {
        $riwayat = $this->getCurrentJFA($dosen);
        $jfaAsal = $riwayat?->ref_jfa_id;
        $refJfa = $jfaAsal ? refJabatanFungsionalAkademik::find($jfaAsal) : null;

        $minimalKum = $refJfa?->kum ?? 0;
        $namaJabatanSaatIni = $refJfa?->nama_jabatan ?? 'Belum memiliki JFA';

        $jfaTujuan = $this->getJfaTujuan($jfaAsal);
        $jfaTujuanNama = $jfaTujuan ? ($this->aturanPengajuanJFA[$jfaTujuan] ?? 'Tidak Diketahui') : 'Jabatan Tertinggi';

        $targetKum = $this->getTargetKum($jfaAsal, $jfaTujuan, $minimalKum);
        $progress = $this->buildProgress($currentKum, $targetKum);

        return [
            'namaJabatanSaatIni' => $namaJabatanSaatIni,
            'jfaTujuanNama' => $jfaTujuanNama,
            'progress' => $progress,
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $dosen = $this->getDosen($user);

        if (!$dosen && !$user->is_admin) {
            abort(403, 'Akses ditolak. Anda bukan Dosen.');
        }

        $latestSubmission = $this->getLatestSubmission($user, $dosen);

        $kumPengajuan = 0;
        $kumDisetujui = 0;
        $personalSubmission = null;

        if ($dosen) {
            $personalSubmission = Pengajuan::where('idDosen', $dosen->id)->latest()->first();

            if ($personalSubmission) {
                // Ambil ID detail kegiatan yang sudah memiliki penilaian dari TPAK
                $evaluatedIds = \App\Models\Dupak\HasilEvaluasi::join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
                    ->where('detail_pengajuan.pengajuan_id', $personalSubmission->id)
                    ->where('hasil_evaluasi.peran_pemeriksa', 'TPAK')
                    ->pluck('detail_pengajuan.id');

                // KUM Pending: Butir kegiatan yang BELUM dinilai sama sekali oleh TPAK
                $kumPengajuan = (float) $personalSubmission->details()
                    ->whereNotIn('id', $evaluatedIds)
                    ->sum('angka_kredit_total');

                // KUM Disetujui: Akumulasi nilai yang sudah diberikan TPAK (rata-rata jika penilai > 1)
                $kumDisetujui = (float) \App\Models\Dupak\HasilEvaluasi::join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
                    ->where('detail_pengajuan.pengajuan_id', $personalSubmission->id)
                    ->where('hasil_evaluasi.peran_pemeriksa', 'TPAK')
                    ->groupBy('hasil_evaluasi.detail_pengajuan_id')
                    ->selectRaw('AVG(hasil_evaluasi.nilai_angka_kredit) as avg_nilai')
                    ->get()
                    ->sum('avg_nilai');
            }
        }

        $baseKum = (float)($user->kum ?? 0);
        // Progress sekarang menghitung Base KUM profil + KUM yang sudah divalidasi TPAK di pengajuan aktif
        $jfaData = $this->getJfaAndKumData($dosen, $baseKum + $kumDisetujui);
        $progress = $jfaData['progress'];

        $hasNoPengajuan = $dosen ? !Pengajuan::where('idDosen', $dosen->id)->exists() : true;
        $totalPengajuanMandiri = $dosen ? Pengajuan::where('idDosen', $dosen->id)->count() : 0;
        $totalSeluruhPengajuan = Pengajuan::count();

        $tugasTpak = collect([]);
        if ($dosen) {
            $tugasTpak = PenunjukanTPAKModel::where('idDosenTpak', $dosen->id)
                ->with(['pengajuan.dosen.pegawai'])
                ->latest()
                ->get();
        }

        $kegiatanUtama = RefKegiatanUtama::select('id', 'nama')
            ->with(['komponens' => function ($query) {
                $query->select('id', 'nama', 'idKegiatanUtama');
            }])
            ->where('status', 1)
            ->get();

        $statistik = [
            'selesai' => Pengajuan::where('status', 'Diterima')->count(),
            'pending' => Pengajuan::whereIn('status', ['Draft', 'Pending', 'Diajukan', 'Revisi', 'Menunggu'])->count(),
        ];

        $kumBreakdown = [
            'pendidikan' => '0.00',
            'pelaksanaan_pendidikan' => '0.00',
            'penelitian' => '0.00',
            'pengabdian' => '0.00',
            'penunjang' => '0.00',
        ];

        if ($dosen && $personalSubmission) {
            $breakdown = \App\Models\Dupak\DetailPengajuan::join('ref_kegiatan_komponen', 'detail_pengajuan.idKomponen', '=', 'ref_kegiatan_komponen.id')
                ->join('ref_kegiatan_utama', 'ref_kegiatan_komponen.idKegiatanUtama', '=', 'ref_kegiatan_utama.id')
                ->where('detail_pengajuan.pengajuan_id', $personalSubmission->id)
                ->select('ref_kegiatan_utama.nama as kategori', DB::raw('COALESCE(SUM(detail_pengajuan.angka_kredit_total), 0) as total'))
                ->groupBy('ref_kegiatan_utama.nama')
                ->pluck('total', 'kategori');

            foreach ($breakdown as $kategori => $total) {
                $lower = strtolower($kategori);
                if (str_contains($lower, 'pelaksanaan pendidikan')) {
                    $kumBreakdown['pelaksanaan_pendidikan'] = number_format($total, 2);
                } elseif (str_contains($lower, 'pendidikan')) {
                    $kumBreakdown['pendidikan'] = number_format($total, 2);
                } elseif (str_contains($lower, 'penelitian')) {
                    $kumBreakdown['penelitian'] = number_format($total, 2);
                } elseif (str_contains($lower, 'pengabdian')) {
                    $kumBreakdown['pengabdian'] = number_format($total, 2);
                } elseif (str_contains($lower, 'penunjang')) {
                    $kumBreakdown['penunjang'] = number_format($total, 2);
                }
            }
        }

        $isMaxJfa = $this->isMaxJfa($dosen);

        $viewData = [
            'user' => $user,
            'dosen' => $dosen,
            'userIsAdminButNotDosen' => $user->is_admin && is_null($dosen),
            'hasNoPengajuan' => $hasNoPengajuan,
            'totalPengajuanMandiri' => $totalPengajuanMandiri,
            'totalSeluruhPengajuan' => $totalSeluruhPengajuan,
            'isMaxJfa' => $isMaxJfa,

            // kum['pending_kum'] = Kum yang diajukan oleh pengaju
            // kum['current'] = Kum yang sudah di acc oleh TPAK dan dianggap sebagai progress oleh bar
            'kum' => [
                'current' => $progress['current'],
                'pending_kum' => number_format($kumPengajuan, 2),
                'target' => $progress['goal'],
                'remaining' => $progress['remaining'],
                'percent' => $progress['percent'],
                'base_kum' => number_format($baseKum, 2),
                'statusColor' => $progress['statusColor'],
                'updatedAtFormatted' => $user->kum_updated_at ? Carbon::parse($user->kum_updated_at)->diffForHumans() : 'Belum pernah diperbarui',
                'pendidikan' => $kumBreakdown['pendidikan'],
                'pelaksanaan_pendidikan' => $kumBreakdown['pelaksanaan_pendidikan'],
                'penelitian' => $kumBreakdown['penelitian'],
                'pengabdian' => $kumBreakdown['pengabdian'],
                'penunjang' => $kumBreakdown['penunjang'],
            ],

            'jfa' => [
                'current' => $jfaData['namaJabatanSaatIni'],
                'next' => $jfaData['jfaTujuanNama'],
            ],

            'submissions' => [
                'list' => $this->submissions($user, $dosen?->id)->paginate(10),
                'has_pending' => $this->hasPendingSubmission($dosen?->id),
                'latest' => $latestSubmission,
            ],
            'isTpak' => $tugasTpak->isNotEmpty(),
            'penugasanTpak' => $tugasTpak,

            'tpak' => [
                'is_tpak' => $tugasTpak->isNotEmpty(),
                'assignments' => $tugasTpak,
                'count' => $tugasTpak->count()
            ],
            'kegiatanUtama' => $kegiatanUtama,
            'statistik' => $statistik,
        ];

        return view('dupak.dashboard', $viewData);
    }
}
