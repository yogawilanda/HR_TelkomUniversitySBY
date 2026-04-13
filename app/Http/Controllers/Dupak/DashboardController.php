<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dupak\Pengajuan;
use App\Models\Dosen;
use App\Models\Dupak\RefTargetJabatanPengajuan;
use App\Models\refJabatanFungsionalAkademik;
use App\Models\riwayatJabatanFungsionalAkademik;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class DashboardController extends Controller
{
    protected $aturanPengajuanJFA = [
        // '8a7c0b44-2c2e-4a16-a4df-111111111111' => 'Non JAD',
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
            ? riwayatJabatanFungsionalAkademik::where('dosen_id', $dosen->id)->latest()->first()
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

        return $record->kumTarget;
    }

    private function buildProgress($current, $goal)
    {
        $percent = $goal > 0 ? min(100, ($current / $goal) * 100) : 0;

        return [
            'current' => number_format($current, 2),
            'goal' => number_format($goal, 2),
            'remaining' => number_format(
                max(0, $goal - $current),
                2
            ),
            'percent' => $percent,
            'statusColor' => $percent >= 100 ? 'bg-green-600' : ($percent >= 60 ? 'bg-yellow-500' : 'bg-indigo-600'),
        ];
    }

    private function submissions(User $user, ?string $dosenId)
    {
        // $q = Pengajuan::with(['dosen', 'dosen.pegawai'])->orderBy('id', 'desc');
        // get the latest submissions for the current user (if not admin) or all submissions (if admin)
        $q = Pengajuan::with(['dosen', 'dosen.pegawai'])->latest();

        if (!$user->is_admin) {
            $q->where('idDosen', $dosenId ?? '___INVALID___');
        }

        return $q;
    }

    private function hasPendingSubmission(?string $dosenId): bool
    {
        if (!$dosenId) {
            return false;
        }

        // Tentukan status-status yang dianggap "pending" atau sedang dalam proses.
        // Anda perlu menyesuaikan array status ini dengan nilai yang ada di kolom 'status'
        // tabel 'dupak_pengajuan'. Contoh di sini: draft, submitted, dan reviewed.
        $pendingStatuses = ['draft', 'submitted', 'reviewed', 'pending'];

        return Pengajuan::where('idDosen', $dosenId)
            ->whereIn('status', $pendingStatuses)
            ->exists();
    }

    private function getLatestSubmission(User $user, ?Dosen $dosen)
    {
        $query = Pengajuan::query();

        // dd($query);
        if ($user->is_admin) {
            // For admin, get the absolute latest submission in the system.
            return $query->latest()->first();
        }

        // Ambil pengajuan terakhir milik dosen tersebut tanpa mempedulikan statusnya
        // agar tombol "Detail Kegiatan" selalu mengarah ke aktivitas terbaru.
        return $query->where('idDosen', $dosen?->id)
            ->latest()
            ->first();

    }

    private function getJfaAndKumData(?Dosen $dosen, float $currentKum)
    {
        $riwayat = $this->getCurrentJFA($dosen);
        $jfaAsal = $riwayat?->ref_jfa_id;
        $refJfa = $jfaAsal ? refJabatanFungsionalAkademik::find($jfaAsal) : null;

        $minimalKum = $refJfa->minimal_kum ?? 0;

        $namaJabatanSaatIni = $refJfa->nama_jabatan ?? 'Anda bukan dosen';
        // JANGAN DIHAPUS/DONT DELETE !
        // dd($jfaAsal, $riwayat);



        $jfaTujuan = $this->getJfaTujuan($jfaAsal);
        $jfaTujuanNama = $jfaTujuan ? $this->aturanPengajuanJFA[$jfaTujuan] : 'Tidak Ada (Jabatan Tertinggi)';

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

        // Ambil pengajuan terbaru untuk menghitung KUM yang sedang diajukan
        $latestSubmission = $this->getLatestSubmission($user, $dosen);

        // FIX: Hanya hitung KUM pengajuan jika user adalah Dosen, dan pastikan hanya mengambil milik sendiri.
        $kumPengajuan = 0;
        if ($dosen) {
            $personalSubmission = Pengajuan::where('idDosen', $dosen->id)->latest()->first();
            $kumPengajuan = $personalSubmission ? $personalSubmission->details()->sum('angka_kredit_total') : 0;
        }

        // KUM saat ini adalah gabungan KUM di profil + KUM yang sedang diproses dalam pengajuan
        $totalKumSaatIni = ($user->kum ?? 0) + $kumPengajuan;

        $jfaData = $this->getJfaAndKumData($dosen, $totalKumSaatIni);
        $progress = $jfaData['progress'];

        $viewData = [
            'user' => $user,
            'dosen' => $dosen,
            'userIsAdminButNotDosen' => $user->is_admin && is_null($dosen),

            'kum' => [
                'current' => $progress['current'],
                'target' => $progress['goal'],
                'remaining' => $progress['remaining'],
                'percent' => $progress['percent'],
                'base_kum' => number_format($user->kum ?? 0, 2), // KUM dari profil
                'pending_kum' => number_format($kumPengajuan, 2), // KUM dari detail pengajuan
                'statusColor' => $progress['statusColor'],
                'updatedAtFormatted' => $user->kum_updated_at ? Carbon::parse($user->kum_updated_at)->diffForHumans() : 'Belum pernah diperbarui',
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
        ];
        // dd($jfaData, $jfaData['namaJabatanSaatIni']);

        return view('dupak.dashboard', $viewData);
    }
}
