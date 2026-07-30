<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Dupak\DetailPengajuan;
use App\Models\Dupak\HasilEvaluasi;
use App\Models\Dupak\Pengajuan;
use App\Models\Dupak\PenunjukanTPAKModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenunjukanTPAKController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // 0. Ambil Master Nama JFA dari database untuk mapping global
        $jfaGlobalNames = DB::connection('mysql')
            ->table('ref_jabatan_fungsional_akademiks')
            ->pluck('nama_jabatan', 'id');

        // 1. Ambil data Dosen (Calon TPAK) dari database sdm_tus
        $dosens = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
            ->whereIn('dosens.id', function ($query) {
                $query->select('dosen_id')
                    ->from('riwayat_jabatan_fungsional_akademiks')
                    ->whereNull('tmt_selesai'); // Mengambil semua dosen yang JFA-nya masih aktif
            })
            ->select('dosens.id', 'users.nama_lengkap')
            ->orderBy('users.nama_lengkap', 'asc')
            ->get();

        // 1b. Ambil Jabatan Fungsional Akademik aktif (rank)
        $tpakJfaNama = [];
        if ($dosens->isNotEmpty()) {
            $tpakJfaRows = DB::connection('mysql')
                ->table('riwayat_jabatan_fungsional_akademiks')
                ->join('ref_jabatan_fungsional_akademiks', 'riwayat_jabatan_fungsional_akademiks.ref_jfa_id', '=', 'ref_jabatan_fungsional_akademiks.id')
                ->whereIn('riwayat_jabatan_fungsional_akademiks.dosen_id', $dosens->pluck('id')->all())
                ->whereNull('riwayat_jabatan_fungsional_akademiks.tmt_selesai')
                ->orderBy('riwayat_jabatan_fungsional_akademiks.tmt_mulai', 'desc')
                ->select('riwayat_jabatan_fungsional_akademiks.dosen_id', 'ref_jabatan_fungsional_akademiks.nama_jabatan')
                ->get();

            foreach ($tpakJfaRows as $row) {
                if (! isset($tpakJfaNama[$row->dosen_id])) {
                    $tpakJfaNama[$row->dosen_id] = $row->nama_jabatan;
                }
            }
        }

        // 2. Ambil data Pengajuan DUPAK (Tetap Abaikan ID 9999 untuk Antrean Pengajuan)
        $antreanSearch = $request->input('antrean_search');

        $pengajuanQuery = Pengajuan::with(['dosen'])
            ->where('id', '!=', 9999)
            ->whereIn('status', ['Pending', 'Submitted'])
            ->where(function ($q) {
                $q->selectRaw('count(*)')
                    ->from('penunjukan_tpak')
                    ->whereColumn('penunjukan_tpak.pengajuan_id', 'pengajuan.id');
            }, '<', 5);

        if ($antreanSearch) {
            $pengajuanQuery->whereHas('dosen', function ($query) use ($antreanSearch) {
                $query->whereIn('users_id', function ($sub) use ($antreanSearch) {
                    $sub->select('id')
                        ->from('users')
                        ->where('nama_lengkap', 'like', "%{$antreanSearch}%")
                        ->orWhere('nama', 'like', "%{$antreanSearch}%");
                });
            });
        }

        $pengajuan = $pengajuanQuery->get();

        // Ambil semua users_id dosen pengaju secara massal untuk mapping manual
        $pengajuUserIds = $pengajuan->pluck('dosen.users_id')->filter()->unique()->toArray();
        $pengajuNames = DB::connection('mysql')
            ->table('users')
            ->whereIn('id', $pengajuUserIds)
            ->pluck('nama_lengkap', 'id');

        // Hitung jumlah TPAK yang sudah ditunjuk per pengajuan (khusus pengajuan nyata)
        $tpakCounts = PenunjukanTPAKModel::where('pengajuan_id', '!=', 9999)
            ->select('pengajuan_id', DB::raw('count(*) as total'))
            ->groupBy('pengajuan_id')
            ->pluck('total', 'pengajuan_id')
            ->toArray();

        // Hitung beban kerja TPAK (Abaikan penunjukan dari master 9999)
        // Hitung beban kerja TPAK (Penunjukan ID 9999 dihitung sebagai 0)
        $dosenWorkload = PenunjukanTPAKModel::select(
            'idDosenTpak',
            DB::raw('SUM(CASE WHEN pengajuan_id != 9999 THEN 1 ELSE 0 END) as total')
        )
            ->groupBy('idDosenTpak')
            ->pluck('total', 'idDosenTpak')
            ->toArray();

        // Mapping pengajuan_id -> idDosen pengaju
        $pengajuMap = $pengajuan->pluck('idDosen', 'id')->toArray();

        // Mapping pengajuan_id -> array idDosenTpak yang sudah ditunjuk
        $assignedMap = PenunjukanTPAKModel::select('pengajuan_id', 'idDosenTpak')
            ->where('pengajuan_id', '!=', 9999)
            ->get()
            ->groupBy('pengajuan_id')
            ->map(fn ($items) => $items->pluck('idDosenTpak')->toArray())
            ->toArray();

        // 3. Ambil Riwayat Penunjukan TPAK (Tampilkan Semua Termasuk Mandiri / ID 9999)
        // 3. Ambil Riwayat Penunjukan TPAK
        $penunjukanQuery = PenunjukanTPAKModel::with('creator')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $matchedDosenIds = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
                ->where('users.nama_lengkap', 'like', "%{$search}%")
                ->pluck('dosens.id');

            // Asumsi field nama_dosen ada, sesuaikan jika beda
            $matchedPengajuanIds = Pengajuan::where('nama_dosen', 'like', "%{$search}%")->pluck('id');

            $penunjukanQuery->where(function ($q) use ($matchedDosenIds, $matchedPengajuanIds) {
                $q->whereIn('idDosenTpak', $matchedDosenIds)
                    ->orWhereIn('pengajuan_id', $matchedPengajuanIds);
            });
        } else {
            // JIKA TIDAK ADA PENCARIAN: Sembunyikan dummy 9999 dari daftar riwayat
            $penunjukanQuery->where('pengajuan_id', '!=', 9999);
        }

        $penunjukanTpak = $penunjukanQuery->paginate(10);

        $pengajuanIds = $penunjukanTpak->pluck('pengajuan_id')->unique();
        $tpakDosenIds = $penunjukanTpak->pluck('idDosenTpak')->unique();

        // Tarik pengajuan data dengan model 'dosen' saja
        $pengajuansData = Pengajuan::with(['dosen'])->whereIn('id', $pengajuanIds)->get();

        // Gabungkan list users_id dari riwayat penunjukan ke pencarian nama lengkap
        $riwayatUserIds = $pengajuansData->pluck('dosen.users_id')->filter()->unique()->toArray();
        $allUserNames = DB::connection('mysql')
            ->table('users')
            ->whereIn('id', array_merge($pengajuUserIds, $riwayatUserIds))
            ->pluck('nama_lengkap', 'id');

        $tpakDosensData = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
            ->whereIn('dosens.id', $tpakDosenIds)
            ->select('dosens.id', 'users.nama_lengkap')
            ->get()
            ->pluck('nama_lengkap', 'id');

        // Hitung progress penilaian per pengajuan
        $detailCounts = DetailPengajuan::select('pengajuan_id', DB::raw('count(*) as total'))
            ->whereIn('pengajuan_id', $pengajuanIds)
            ->groupBy('pengajuan_id')
            ->pluck('total', 'pengajuan_id')
            ->toArray();

        $evaluatedCounts = HasilEvaluasi::select('detail_pengajuan.pengajuan_id', DB::raw('count(distinct hasil_evaluasi.detail_pengajuan_id) as total'))
            ->join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
            ->whereIn('detail_pengajuan.pengajuan_id', $pengajuanIds)
            ->groupBy('detail_pengajuan.pengajuan_id')
            ->pluck('total', 'pengajuan_id')
            ->toArray();

        $penunjukanTpak->getCollection()->transform(function ($item) use ($pengajuansData, $tpakDosensData, $detailCounts, $evaluatedCounts, $jfaGlobalNames, $allUserNames) {
            // Penanganan Khusus Penunjukan Mandiri (Master ID 9999)
            if ($item->pengajuan_id == 9999) {
                $item->pengaju_nama = 'PENUNJUKAN MANDIRI';
                $item->pengaju_jabatan_asal = '-';
                $item->pengaju_jabatan_tujuan = '-';
            } else {
                $p = $pengajuansData->firstWhere('id', $item->pengajuan_id);

                $userId = $p->dosen->users_id ?? null;
                $item->pengaju_nama = $userId ? ($allUserNames[$userId] ?? 'N/A') : 'N/A';
                $item->pengaju_jabatan_asal = $jfaGlobalNames[$p->jfaAsal ?? null] ?? 'N/A';
                $item->pengaju_jabatan_tujuan = $jfaGlobalNames[$p->jfaTujuan ?? null] ?? 'Tidak Diketahui';
            }

            $item->tpak_nama_lengkap = $tpakDosensData[$item->idDosenTpak] ?? 'N/A';
            $item->created_at = Carbon::parse($item->created_at);

            $totalDetail = $detailCounts[$item->pengajuan_id] ?? 0;
            $evaluated = $evaluatedCounts[$item->pengajuan_id] ?? 0;

            $item->progress_total = $totalDetail;
            $item->progress_evaluated = $evaluated;
            $item->progress_percent = $totalDetail > 0 ? round(($evaluated / $totalDetail) * 100) : 0;

            return $item;
        });

        return view('dupak.penunjukan_tpak.index', compact(
            'dosens',
            'pengajuan',
            'penunjukanTpak',
            'tpakCounts',
            'dosenWorkload',
            'pengajuMap',
            'assignedMap',
            'tpakJfaNama',
            'jfaGlobalNames',
            'pengajuNames'
        ));
    }

    private function getJfaLevel($namaJfa)
    {
        $map = [
            'asisten ahli' => 1,
            'asisten_ahli' => 1,
            'lektor' => 2,
            'lektor kepala' => 3,
            'lektor_kepala' => 3,
            'guru besar' => 4,
            'guru_besar' => 4,
            'profesor' => 4,
            'professor' => 4,
        ];
        $nama = strtolower(trim($namaJfa ?? ''));

        return $map[$nama] ?? 0;
    }

    public function createNewTPAK()
    {
        // ...
    }

    public function store(Request $request)
    {
        // 1. Tentukan apakah TPAK Mandiri atau bukan
        $isMandiri = ! $request->filled('pengajuan_id');

        if ($isMandiri) {
            // Ambil ID Dosen dari User SYSTEM_MASTER yang absah dari Seeder
            $systemUser = DB::connection('mysql')
                ->table('users')
                ->where('nama_lengkap', 'SYSTEM_MASTER')
                ->first();

            $defaultDosenId = $systemUser
                ? DB::connection('mysql')->table('dosens')->where('users_id', $systemUser->id)->value('id')
                : DB::connection('mysql')->table('dosens')->value('id');

            // Ambil 1 ID JFA valid agar tidak error NOT NULL constraint
            $defaultJFAId = DB::connection('mysql')
                ->table('ref_jabatan_fungsional_akademiks')
                ->value('id');

            // AUTO-CREATE: Buat pengajuan dummy ID 9999 otomatis jika belum ada di DB
            $masterPengajuan = Pengajuan::firstOrCreate(
                ['id' => 9999],
                [
                    'idDosen' => $defaultDosenId,
                    'start' => '2020-01-01',
                    'end' => '2099-12-31',
                    'TahunAjaranAjuanAwal' => 'MASTER',
                    'TahunAjaranAjuanAkhir' => 'MASTER',
                    'semesterAjuan' => 'Ganjil',
                    'jfaAsal' => $defaultJFAId,
                    'jfaTujuan' => $defaultJFAId,
                    'status' => 'MASTER_TPAK',
                ]
            );

            $pengajuanId = $masterPengajuan->id;
        } else {
            $pengajuanId = $request->pengajuan_id;
        }

        // 2. Adjust Validation
        $request->validate([
            'pengajuan_id' => $isMandiri ? 'nullable' : 'required|exists:dupak.pengajuan,id',
            'idDosenTpak' => 'required|exists:dosens,id',
            'bukti_penunjukan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        try {
            // Validasi khusus pengajuan (Hanya dijalankan jika BUKAN Mandiri)
            if (! $isMandiri) {
                $pengajuan = Pengajuan::findOrFail($pengajuanId);
                $finalStatuses = ['Diterima', 'Ditolak', 'Selesai'];

                if (in_array($pengajuan->status, $finalStatuses)) {
                    return redirect()->back()->with('error', 'Pengajuan sudah final ('.$pengajuan->status.'). Tidak dapat menambahkan TPAK lagi.');
                }

                if ($pengajuan->idDosen == $request->idDosenTpak) {
                    return redirect()->back()->with('error', 'Dosen tidak diperbolehkan menjadi penilai (TPAK) untuk pengajuannya sendiri.');
                }

                $tpakCount = PenunjukanTPAKModel::where('pengajuan_id', $pengajuanId)->count();
                if ($tpakCount >= 5) {
                    return redirect()->back()->with('error', 'Maksimal jumlah TPAK untuk satu pengajuan adalah 5 orang.');
                }
            }

            // Cek duplicate TPAK
            $isDuplicate = PenunjukanTPAKModel::where('pengajuan_id', $pengajuanId)
                ->where('idDosenTpak', $request->idDosenTpak)
                ->exists();

            if ($isDuplicate) {
                return redirect()->back()->with('error', 'Dosen tersebut sudah ditunjuk sebagai TPAK.');
            }

            // Cek JFA Aktif
            $tpakJfa = DB::connection('mysql')
                ->table('riwayat_jabatan_fungsional_akademiks')
                ->join('ref_jabatan_fungsional_akademiks', 'riwayat_jabatan_fungsional_akademiks.ref_jfa_id', '=', 'ref_jabatan_fungsional_akademiks.id')
                ->where('riwayat_jabatan_fungsional_akademiks.dosen_id', $request->idDosenTpak)
                ->whereNull('riwayat_jabatan_fungsional_akademiks.tmt_selesai')
                ->orderBy('riwayat_jabatan_fungsional_akademiks.tmt_mulai', 'desc')
                ->select('ref_jabatan_fungsional_akademiks.nama_jabatan')
                ->first();

            if (! $tpakJfa || empty($tpakJfa->nama_jabatan)) {
                return redirect()->back()->with('error', 'Dosen yang dipilih tidak memiliki Jabatan Fungsional Akademik (JFA) aktif. Penunjukan dibatalkan.');
            }

            // Pengecekan Level JFA (Hanya jika BUKAN Mandiri)
            if (! $isMandiri && isset($pengajuan) && $pengajuan->jfaTujuan) {
                $jfaTujuanPengajuNama = DB::connection('mysql')
                    ->table('ref_jabatan_fungsional_akademiks')
                    ->where('id', $pengajuan->jfaTujuan)
                    ->value('nama_jabatan');

                if ($jfaTujuanPengajuNama) {
                    $levelTpak = $this->getJfaLevel($tpakJfa->nama_jabatan);
                    $levelPengaju = $this->getJfaLevel($jfaTujuanPengajuNama);

                    if ($levelTpak > 0 && $levelPengaju > 0 && $levelTpak < $levelPengaju) {
                        return redirect()->back()->with(
                            'error',
                            "JFA TPAK ({$tpakJfa->nama_jabatan}) lebih rendah dari JFA Tujuan Pengaju ({$jfaTujuanPengajuNama}). Penunjukan dibatalkan demi keadilan penilaian."
                        );
                    }
                }
            }

            // Simpan
            PenunjukanTPAKModel::create([
                'pengajuan_id' => $pengajuanId,
                'idDosenTpak' => $request->idDosenTpak,
                'bukti_penunjukan' => $request->bukti_penunjukan,
                'catatan' => $request->catatan,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('dupak.penunjukan_tpak.index')->with('success', 'TPAK berhasil ditunjuk.');

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan penunjukan TPAK', [
                'message' => $e->getMessage(),
                'file' => $e->getFile().':'.$e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->only(['pengajuan_id', 'idDosenTpak']),
            ]);

            $debugMessage = config('app.debug') ? ' [DEBUG: '.$e->getMessage().' — '.get_class($e).']' : '';

            return redirect()->back()->with('error', 'Terjadi kesalahan teknis saat menyimpan penunjukan. Silakan coba lagi atau hubungi admin.'.$debugMessage);
        }
    }

    public function destroy($id)
    {
        try {
            $penunjukan = PenunjukanTPAKModel::find($id);
            if (! $penunjukan) {
                return redirect()->route('dupak.penunjukan_tpak.index')->with('error', 'Data penunjukan tidak ditemukan atau sudah dihapus.');
            }

            $penunjukan->delete();

            return redirect()->route('dupak.penunjukan_tpak.index')->with('success', 'Penugasan TPAK telah dibatalkan!');
        } catch (\Exception $e) {
            Log::error('Gagal membatalkan penunjukan TPAK', [
                'message' => $e->getMessage(),
                'penunjukan_id' => $id,
            ]);

            $debugMessage = config('app.debug') ? ' [DEBUG: '.$e->getMessage().']' : '';

            return redirect()->route('dupak.penunjukan_tpak.index')->with('error', 'Terjadi kesalahan teknis saat membatalkan penunjukan. Silakan coba lagi.'.$debugMessage);
        }
    }
}
