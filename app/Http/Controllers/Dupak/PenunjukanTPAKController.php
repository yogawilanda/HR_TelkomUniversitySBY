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

        // 2. Ambil data Pengajuan DUPAK
        $antreanSearch = $request->input('antrean_search');

        // Tarik data pengajuan dan relasi dosen saja (tanpa user)
        $pengajuanQuery = Pengajuan::with(['dosen'])
            ->whereIn('status', ['Pending', 'Submitted'])
            ->where(function ($q) {
                $q->selectRaw('count(*)')
                    ->from('penunjukan_tpak')
                    ->whereColumn('penunjukan_tpak.pengajuan_id', 'pengajuan.id');
            }, '<', 5);
            
        if ($antreanSearch) {
            // Pencarian antrean manual via join tabel user sdm_tus
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

        // Ambil semua users_id dosen pengaju secara massal untuk mapping manual nama pengaju di antrean
        $pengajuUserIds = $pengajuan->pluck('dosen.users_id')->filter()->unique()->toArray();
        $pengajuNames = DB::connection('mysql')
            ->table('users')
            ->whereIn('id', $pengajuUserIds)
            ->pluck('nama_lengkap', 'id'); // Jadikan users_id sebagai key array

        // Hitung jumlah TPAK yang sudah ditunjuk per pengajuan
        $tpakCounts = PenunjukanTPAKModel::select('pengajuan_id', DB::raw('count(*) as total'))
            ->groupBy('pengajuan_id')
            ->pluck('total', 'pengajuan_id')
            ->toArray();

        // Hitung beban kerja TPAK
        $dosenWorkload = PenunjukanTPAKModel::select('idDosenTpak', DB::raw('count(*) as total'))
            ->groupBy('idDosenTpak')
            ->pluck('total', 'idDosenTpak')
            ->toArray();

        // Mapping pengajuan_id -> idDosen pengaju
        $pengajuMap = $pengajuan->pluck('idDosen', 'id')->toArray();

        // Mapping pengajuan_id -> array idDosenTpak yang sudah ditunjuk
        $assignedMap = PenunjukanTPAKModel::select('pengajuan_id', 'idDosenTpak')
            ->get()
            ->groupBy('pengajuan_id')
            ->map(fn ($items) => $items->pluck('idDosenTpak')->toArray())
            ->toArray();

        // 3. Ambil Riwayat Penunjukan TPAK
        $penunjukanQuery = PenunjukanTPAKModel::with('creator')->orderBy('created_at', 'desc');

        if ($search) {
            $matchedDosenIds = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
                ->where('users.nama_lengkap', 'like', "%{$search}%")
                ->pluck('dosens.id');

            $matchedPengajuanIds = Pengajuan::where('nama_dosen', 'like', "%{$search}%")->pluck('id');

            $penunjukanQuery->where(function ($q) use ($matchedDosenIds, $matchedPengajuanIds) {
                $q->whereIn('idDosenTpak', $matchedDosenIds)
                    ->orWhereIn('pengajuan_id', $matchedPengajuanIds);
            });
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
            $p = $pengajuansData->firstWhere('id', $item->pengajuan_id);
            
            // Map nama pengaju secara manual tanpa memicu relasi 'user' di model Dosen
            $userId = $p->dosen->users_id ?? null;
            $item->pengaju_nama = $userId ? ($allUserNames[$userId] ?? 'N/A') : 'N/A';
            
            $item->pengaju_jabatan_asal = $jfaGlobalNames[$p->jfaAsal] ?? 'N/A';
            $item->pengaju_jabatan_tujuan = $jfaGlobalNames[$p->jfaTujuan] ?? 'Tidak Diketahui';
            $item->tpak_nama_lengkap = $tpakDosensData[$item->idDosenTpak] ?? 'N/A';
            $item->created_at = Carbon::parse($item->created_at);

            $totalDetail = $detailCounts[$item->pengajuan_id] ?? 0;
            $evaluated = $evaluatedCounts[$item->pengajuan_id] ?? 0;
            $item->progress_total = $totalDetail;
            $item->progress_evaluated = $evaluated;
            $item->progress_percent = $totalDetail > 0 ? round(($evaluated / $totalDetail) * 100) : 0;

            return $item;
        });

        // Bagikan variabel pengajuNames ke view agar bisa dipakai di bagian list antrean pengaju
        return view('dupak.penunjukan_tpak.index', compact('dosens', 'pengajuan', 'penunjukanTpak', 'tpakCounts', 'dosenWorkload', 'pengajuMap', 'assignedMap', 'tpakJfaNama', 'jfaGlobalNames', 'pengajuNames'));
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

    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|exists:dupak.pengajuan,id',
            'idDosenTpak' => 'required|exists:dosens,id',
            'catatan' => 'nullable|string',
        ]);

        try {
            $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);
            $finalStatuses = ['Diterima', 'Ditolak', 'Selesai'];
            if (in_array($pengajuan->status, $finalStatuses)) {
                return redirect()->back()->with('error', 'Pengajuan sudah final ('.$pengajuan->status.'). Tidak dapat menambahkan TPAK lagi.');
            }

            if ($pengajuan->idDosen == $request->idDosenTpak) {
                return redirect()->back()->with('error', 'Dosen tidak diperbolehkan menjadi penilai (TPAK) untuk pengajuannya sendiri.');
            }

            $isDuplicate = PenunjukanTPAKModel::where('pengajuan_id', $request->pengajuan_id)
                ->where('idDosenTpak', $request->idDosenTpak)
                ->exists();
            if ($isDuplicate) {
                return redirect()->back()->with('error', 'Dosen tersebut sudah ditunjuk sebagai TPAK untuk pengajuan ini.');
            }

            $tpakCount = PenunjukanTPAKModel::where('pengajuan_id', $request->pengajuan_id)->count();
            if ($tpakCount >= 5) {
                return redirect()->back()->with('error', 'Maksimal jumlah TPAK untuk satu pengajuan adalah 5 orang.');
            }

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

            $jfaTujuanPengajuNama = null;
            if ($pengajuan->jfaTujuan) {
                $jfaTujuanPengajuNama = DB::connection('mysql')
                    ->table('ref_jabatan_fungsional_akademiks')
                    ->where('id', $pengajuan->jfaTujuan)
                    ->value('nama_jabatan');
            }

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

            PenunjukanTPAKModel::create([
                'pengajuan_id' => $request->pengajuan_id,
                'idDosenTpak' => $request->idDosenTpak,
                'catatan' => $request->catatan,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('dupak.penunjukan_tpak.index')->with('success', 'TPAK berhasil ditunjuk dan ditugaskan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan penunjukan TPAK', [
                'message' => $e->getMessage(),
                'file' => $e->getFile().':'.$e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->only(['pengajuan_id', 'idDosenTpak']),
            ]);

            $debugMessage = config('app.debug')
                ? ' [DEBUG: '.$e->getMessage().' — '.get_class($e).']'
                : '';

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

            $debugMessage = config('app.debug')
                ? ' [DEBUG: '.$e->getMessage().']'
                : '';

            return redirect()->route('dupak.penunjukan_tpak.index')->with('error', 'Terjadi kesalahan teknis saat membatalkan penunjukan. Silakan coba lagi.'.$debugMessage);
        }
    }
}