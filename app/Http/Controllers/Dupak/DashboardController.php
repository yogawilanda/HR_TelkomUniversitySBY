<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Dupak\DetailPengajuan;
use App\Models\Dupak\HasilEvaluasi;
use App\Models\Dupak\NotifikasiDupakModel;
use App\Models\Dupak\Pengajuan;
use App\Models\Dupak\PenunjukanTPAKModel;
use App\Models\Dupak\RefKegiatanUtama;
use App\Models\Dupak\RefTargetJabatanPengajuan;
use App\Models\RefJabatanFungsionalAkademik;
use App\Models\RiwayatJabatanFungsionalAkademik;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            ? RiwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)
            ->whereNull('tmt_selesai')
            ->latest()
            ->first()
            : null;
    }

    private function getJfaTujuan(?string $jfaAsal, ?Pengajuan $personalSubmission = null)
    {
        if (! $jfaAsal) {
            return null;
        }

        if ($personalSubmission && $personalSubmission->jfaTujuan) {
            return $personalSubmission->jfaTujuan;
        }

        $keys = array_keys($this->aturanPengajuanJFA);
        $i = array_search($jfaAsal, $keys);

        return ($i !== false && isset($keys[$i + 1])) ? $keys[$i + 1] : null;
    }

    private function getTargetKum(?string $asal, ?string $tujuan, $minimal)
    {
        if (! $asal || ! $tujuan) {
            return $minimal;
        }

        $record = RefTargetJabatanPengajuan::where('jfaAsal', $asal)
            ->where('jfaTujuan', $tujuan)
            ->first();

        return $record->kumTarget ?? $minimal;
    }

    private function buildProgress($current, $goal)
    {
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
        $q = Pengajuan::with(['dosen.pegawai'])
            ->where('id', '!=', 9999)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (! $user->is_admin) {
            $assignedPengajuanIds = [];
            if ($dosenId) {
                $assignedPengajuanIds = PenunjukanTPAKModel::where('idDosenTpak', $dosenId)
                    ->pluck('pengajuan_id')
                    ->toArray();
            }

            $q->where(function ($query) use ($dosenId, $assignedPengajuanIds) {
                $query->where('idDosen', $dosenId ?? '___INVALID___');

                if (! empty($assignedPengajuanIds)) {
                    $query->orWhereIn('id', $assignedPengajuanIds);
                }
            });
        }

        return $q;
    }

    private function hasPendingSubmission(?string $dosenId): bool
    {
        if (! $dosenId) {
            return false;
        }

        $pendingStatuses = ['Draft', 'Pending', 'Diajukan', 'Revisi', 'Menunggu'];

        return Pengajuan::where('idDosen', $dosenId)
            ->whereIn('status', $pendingStatuses)
            ->exists();
    }

    private function isMaxJfa(?Dosen $dosen): bool
    {
        if (! $dosen) {
            return false;
        }

        $riwayat = $this->getCurrentJFA($dosen);
        $jfaId = $riwayat?->ref_jfa_id;

        $jfaKeys = array_keys($this->aturanPengajuanJFA);
        $lastJfaId = end($jfaKeys);

        return $jfaId === $lastJfaId;
    }

    private function getLatestSubmission(User $user, ?Dosen $dosen)
    {
        $query = Pengajuan::query();

        if ($user->is_admin && ! $dosen) {
            return $query->latest()->first();
        }

        return $query->where('idDosen', $dosen?->id)->latest()->first();
    }

    private function getJfaAndKumData(?Dosen $dosen, float $currentKum, ?Pengajuan $personalSubmission = null)
    {
        $riwayat = $this->getCurrentJFA($dosen);
        $jfaAsal = $riwayat?->ref_jfa_id;
        $refJfa = $jfaAsal ? RefJabatanFungsionalAkademik::find($jfaAsal) : null;

        $minimalKum = $refJfa?->kum ?? 0;
        $namaJabatanSaatIni = $refJfa?->nama_jabatan ?? 'Belum memiliki JFA';

        $jfaTujuan = $this->getJfaTujuan($jfaAsal, $personalSubmission);
        $jfaTujuanNama = $jfaTujuan ? ($this->aturanPengajuanJFA[$jfaTujuan] ?? 'Tidak Diketahui') : 'Jabatan Tertinggi';

        $targetKum = $this->getTargetKum($jfaAsal, $jfaTujuan, $minimalKum);
        $progress = $this->buildProgress($currentKum, $targetKum);

        return [
            'namaJabatanSaatIni' => $namaJabatanSaatIni,
            'jfaTujuanNama' => $jfaTujuanNama,
            'progress' => $progress,
            'jfaTujuan' => $jfaTujuan,
        ];
    }

    /**
     * Cek eligibilitas dosen berdasarkan riwayat JFA aktif
     */
    private function checkDosenEligibility($dosenId)
    {
        if (!$dosenId) {
            return false;
        }

        $duaTahunLalu = Carbon::now()->subYears(2);

        $riwayatJfaEligible = RiwayatJabatanFungsionalAkademik::with('jfa')
            ->where('dosen_id', $dosenId)
            ->whereNotNull('tmt_mulai')
            ->where('tmt_mulai', '<=', $duaTahunLalu)
            ->where(function ($q) {
                $q->whereNull('tmt_selesai')
                    ->orWhere('tmt_selesai', '>', now());
            })
            ->first();

        if (!$riwayatJfaEligible) {
            return false;
        }

        $namaJfa = strtolower($riwayatJfaEligible->jfa?->nama_jabatan ?? '');
        $kodeJfa = strtoupper($riwayatJfaEligible->jfa?->kode ?? '');

        $isGuruBesar = str_contains($namaJfa, 'guru besar')
            || str_contains($namaJfa, 'profesor')
            || $kodeJfa === 'GB';

        return !$isGuruBesar;
    }

    public function index()
    {
        $user = Auth::user();
        $dosen = $this->getDosen($user);

        if (! $dosen && ! $user->is_admin) {
            abort(403, 'Akses ditolak. Anda bukan Dosen.');
        }

        $isProfileIncomplete = false;
        if ($dosen) {
            $riwayatJfa = $this->getCurrentJFA($dosen);

            if (
                empty($user->nik) ||
                is_null($riwayatJfa) ||
                (empty($dosen->nidn) && empty($dosen->nidk))
            ) {
                $isProfileIncomplete = true;
            }
        }

        $latestSubmission = $this->getLatestSubmission($user, $dosen);

        $kumPengajuan = 0;
        $kumDisetujui = 0;
        $personalSubmission = null;

        if ($dosen) {
            $personalSubmission = Pengajuan::where('idDosen', $dosen->id)->latest()->first();

            if ($personalSubmission) {
                $evaluatedIds = HasilEvaluasi::join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
                    ->where('detail_pengajuan.pengajuan_id', $personalSubmission->id)
                    ->where('hasil_evaluasi.peran_pemeriksa', 'TPAK')
                    ->pluck('detail_pengajuan.id');

                $kumPengajuan = (float) $personalSubmission->details()
                    ->whereNotIn('id', $evaluatedIds)
                    ->sum('angka_kredit_total');

                $kumDisetujui = (float) HasilEvaluasi::join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
                    ->where('detail_pengajuan.pengajuan_id', $personalSubmission->id)
                    ->where('hasil_evaluasi.peran_pemeriksa', 'TPAK')
                    ->groupBy('hasil_evaluasi.detail_pengajuan_id')
                    ->selectRaw('AVG(hasil_evaluasi.nilai_angka_kredit) as avg_nilai')
                    ->get()
                    ->sum('avg_nilai');
            }
        }

        $baseKum = (float) ($user->kum ?? 0);
        $jfaData = $this->getJfaAndKumData($dosen, $baseKum + $kumDisetujui, $personalSubmission);
        $progress = $jfaData['progress'];

        $hasNoPengajuan = $dosen ? ! Pengajuan::where('idDosen', $dosen->id)->exists() : true;
        $totalPengajuanMandiri = $dosen ? Pengajuan::where('idDosen', $dosen->id)->count() : 0;
        $totalSeluruhPengajuan = Pengajuan::where('id', '!=', 9999)->count();

        $tugasTpak = collect([]);
        if ($dosen) {
            $tugasTpak = PenunjukanTPAKModel::where('idDosenTpak', $dosen->id)
                ->whereHas('pengajuan', function ($query) {
                    $query->where('id', '!=', 9999);
                })
                ->with(['pengajuan.dosen.pegawai'])
                ->latest()
                ->get();
        }

        $kegiatanUtama = RefKegiatanUtama::select('id', 'nama')
            ->with([
                'komponens' => function ($query) {
                    $query->select('id', 'nama', 'idKegiatanUtama');
                },
            ])
            ->where('status', 1)
            ->get();

        $statistik = [
            'selesai' => Pengajuan::where('id', '!=', 9999)->where('status', 'Diterima')->count(),
            'pending' => Pengajuan::where('id', '!=', 9999)->whereIn('status', ['Draft', 'Pending', 'Diajukan', 'Revisi', 'Menunggu'])->count(),
        ];

        $kumBreakdown = [
            'pendidikan' => ['approved' => 0.0, 'pending' => 0.0, 'target' => 100],
            'pelaksanaan_pendidikan' => ['approved' => 0.0, 'pending' => 0.0, 'target' => 142],
            'penelitian' => ['approved' => 0.0, 'pending' => 0.0, 'target' => 100],
            'pengabdian' => ['approved' => 0.0, 'pending' => 0.0, 'target' => 40],
            'penunjang' => ['approved' => 0.0, 'pending' => 0.0, 'target' => 100],
        ];

        if ($dosen && $personalSubmission) {
            $details = DetailPengajuan::join('ref_kegiatan_komponen', 'detail_pengajuan.idKomponen', '=', 'ref_kegiatan_komponen.id')
                ->join('ref_kegiatan_utama', 'ref_kegiatan_komponen.idKegiatanUtama', '=', 'ref_kegiatan_utama.id')
                ->where('detail_pengajuan.pengajuan_id', $personalSubmission->id)
                ->select(
                    'detail_pengajuan.id as detail_id',
                    'ref_kegiatan_utama.nama as kategori',
                    'detail_pengajuan.status',
                    'detail_pengajuan.angka_kredit_total'
                )
                ->get();

            $evaluationsMap = HasilEvaluasi::join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
                ->where('detail_pengajuan.pengajuan_id', $personalSubmission->id)
                ->where('hasil_evaluasi.peran_pemeriksa', 'TPAK')
                ->select('detail_pengajuan_id', 'nilai_angka_kredit')
                ->get()
                ->groupBy('detail_pengajuan_id');

            $riwayat = $this->getCurrentJFA($dosen);
            $jfaAsal = $riwayat?->ref_jfa_id;

            $jfaTujuan = $jfaData['jfaTujuan'];

            $targetJabatan = null;
            if ($jfaAsal && $jfaTujuan) {
                $targetJabatan = RefTargetJabatanPengajuan::where('jfaAsal', $jfaAsal)
                    ->where('jfaTujuan', $jfaTujuan)
                    ->first();
            }

            foreach ($details as $det) {
                $lowerCat = strtolower($det->kategori);
                $status = strtolower($det->status);

                $key = 'penunjang';
                if (str_contains($lowerCat, 'pelaksanaan pendidikan')) {
                    $key = 'pelaksanaan_pendidikan';
                } elseif (str_contains($lowerCat, 'pendidikan')) {
                    $key = 'pendidikan';
                } elseif (str_contains($lowerCat, 'penelitian')) {
                    $key = 'penelitian';
                } elseif (str_contains($lowerCat, 'pengabdian')) {
                    $key = 'pengabdian';
                } elseif (str_contains($lowerCat, 'penunjang')) {
                    $key = 'penunjang';
                }

                if ($targetJabatan) {
                    if ($key === 'pendidikan') {
                        $kumBreakdown[$key]['target'] = (float) ($targetJabatan->limit_lampiran_1 ?? 100);
                    } elseif ($key === 'pelaksanaan_pendidikan') {
                        $kumBreakdown[$key]['target'] = (float) ($targetJabatan->limit_lampiran_2 ?? 142);
                    } elseif ($key === 'penelitian') {
                        $kumBreakdown[$key]['target'] = (float) ($targetJabatan->limit_lampiran_3 ?? 100);
                    } elseif ($key === 'pengabdian') {
                        $kumBreakdown[$key]['target'] = (float) ($targetJabatan->limit_lampiran_4 ?? 40);
                    } elseif ($key === 'penunjang') {
                        $kumBreakdown[$key]['target'] = (float) ($targetJabatan->limit_lampiran_5 ?? 100);
                    }
                }

                if ($status === 'approved') {
                    $val = $evaluationsMap->has($det->detail_id)
                        ? (float) $evaluationsMap->get($det->detail_id)->avg('nilai_angka_kredit')
                        : (float) $det->angka_kredit_total;
                    $kumBreakdown[$key]['approved'] += $val;
                } else {
                    $val = (float) $det->angka_kredit_total;
                    $kumBreakdown[$key]['pending'] += $val;
                }
            }
        }

        foreach ($kumBreakdown as $key => $values) {
            $kumBreakdown[$key]['approved'] = number_format($values['approved'], 2);
            $kumBreakdown[$key]['pending'] = number_format($values['pending'], 2);
            $kumBreakdown[$key]['target'] = number_format($values['target'], 2);
        }

        $isMaxJfa = $this->isMaxJfa($dosen);

        // --- Cek eligibilitas dosen yang sedang login ---
        $isEligible = $dosen ? $this->checkDosenEligibility($dosen->id) : false;

        $totalDosenEligible = 0;
        if ($user->is_admin) {
            $duaTahunLalu = Carbon::now()->subYears(2);

            $totalDosenEligible = RiwayatJabatanFungsionalAkademik::with('jfa')
                ->whereNotNull('tmt_mulai')
                ->where('tmt_mulai', '<=', $duaTahunLalu)
                ->where(function ($q) {
                    $q->whereNull('tmt_selesai')
                        ->orWhere('tmt_selesai', '>', now());
                })
                ->get()
                ->reject(function ($item) {
                    $namaJfa = strtolower($item->jfa?->nama_jabatan ?? '');
                    $kodeJfa = strtoupper($item->jfa?->kode ?? '');

                    return str_contains($namaJfa, 'guru besar')
                        || str_contains($namaJfa, 'profesor')
                        || $kodeJfa === 'GB';
                })
                ->count();
        }

        $viewData = [
            'user' => $user,
            'dosen' => $dosen,
            'isProfileIncomplete' => $isProfileIncomplete,
            'userIsAdminButNotDosen' => $user->is_admin && is_null($dosen),
            'hasNoPengajuan' => $hasNoPengajuan,
            'totalPengajuanMandiri' => $totalPengajuanMandiri,
            'totalSeluruhPengajuan' => $totalSeluruhPengajuan,
            'totalDosenEligible' => $totalDosenEligible,
            'isMaxJfa' => $isMaxJfa,
            'isEligible' => $isEligible, // <--- SUDAH DITAMBAHKAN
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
                'count' => $tugasTpak->count(),
            ],
            'kegiatanUtama' => $kegiatanUtama,
            'statistik' => $statistik,
        ];

        return view('dupak.dashboard', $viewData);
    }

    public function eligibilitas()
    {
        $jfas = RiwayatJabatanFungsionalAkademik::with(['dosen.pegawai', 'jfa'])
            ->paginate(5);

        return view('dupak.eligibilitas.index', compact('jfas'));
    }
}