<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Dupak\Pengajuan;
use App\Models\Dupak\PenunjukanTPAKModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenunjukanTPAKController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // 1. Ambil data Dosen (Calon TPAK) dari database sdm_tus
        // Join dengan users untuk mendapatkan nama lengkap
        $dosens = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
            ->select('dosens.id', 'users.nama_lengkap')
            ->orderBy('users.nama_lengkap', 'asc')
            ->get();

        // 2. Ambil data Pengajuan DUPAK yang belum mencapai batas limit TPAK (5 orang)
        // Kita gunakan subquery untuk menghitung jumlah TPAK yang sudah ditunjuk per pengajuan
        $antreanSearch = $request->input('antrean_search');
        
        $pengajuanQuery = Pengajuan::with('dosen.user') // Eager load untuk pencarian nama dosen
            ->whereIn('status', ['Pending', 'Submitted'])
            ->where(function($q) {
                $q->selectRaw('count(*)')
                  ->from('penunjukan_tpak') // Use table name only; connection is inherited from parent query
                  ->whereColumn('penunjukan_tpak.pengajuan_id', 'pengajuan.id');
            }, '<', 5);

        if ($antreanSearch) {
            $pengajuanQuery->whereHas('dosen', function ($query) use ($antreanSearch) {
                $query->whereHas('user', function ($userQuery) use ($antreanSearch) {
                    $userQuery->where('nama_lengkap', 'like', "%{$antreanSearch}%")
                              ->orWhere('nama', 'like', "%{$antreanSearch}%"); // Fallback jika nama_lengkap null
                });
            });
        }

        $pengajuan = $pengajuanQuery->get();

        // Hitung jumlah TPAK yang sudah ditunjuk per pengajuan (untuk info di UI)
        $tpakCounts = PenunjukanTPAKModel::select('pengajuan_id', DB::raw('count(*) as total'))
            ->groupBy('pengajuan_id')
            ->pluck('total', 'pengajuan_id')
            ->toArray();

        // Hitung beban kerja TPAK (jumlah penugasan aktif per dosen)
        $dosenWorkload = PenunjukanTPAKModel::select('idDosenTpak', DB::raw('count(*) as total'))
            ->groupBy('idDosenTpak')
            ->pluck('total', 'idDosenTpak')
            ->toArray();

        // Mapping pengajuan_id -> idDosen pengaju (untuk filter self-assign di UI)
        $pengajuMap = $pengajuan->pluck('idDosen', 'id')->toArray();

        // Mapping pengajuan_id -> array idDosenTpak yang sudah ditunjuk (untuk filter duplikat di UI)
        $assignedMap = PenunjukanTPAKModel::select('pengajuan_id', 'idDosenTpak')
            ->get()
            ->groupBy('pengajuan_id')
            ->map(fn($items) => $items->pluck('idDosenTpak')->toArray())
            ->toArray();

        // 3. Ambil Riwayat Penunjukan TPAK dari database dupak
        $penunjukanQuery = PenunjukanTPAKModel::with('creator')->orderBy('created_at', 'desc');

        // Implementasi Fitur Pencarian (Nama Pengaju atau Nama TPAK)
        if ($search) {
            // Cari ID Dosen yang namanya cocok di database sdm_tus
            $matchedDosenIds = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
                ->where('users.nama_lengkap', 'like', "%{$search}%")
                ->pluck('dosens.id');

            // Cari ID Pengajuan yang nama pengajunya cocok
            $matchedPengajuanIds = Pengajuan::where('nama_dosen', 'like', "%{$search}%")->pluck('id');

            $penunjukanQuery->where(function($q) use ($matchedDosenIds, $matchedPengajuanIds) {
                $q->whereIn('idDosenTpak', $matchedDosenIds)
                  ->orWhereIn('pengajuan_id', $matchedPengajuanIds);
            });
        }

        // Pagination riwayat penunjukan
        $penunjukanTpak = $penunjukanQuery->paginate(10);

        // Transformasi koleksi untuk mendapatkan nama pengaju dan nama penilai (TPAK)
        // Karena database terpisah, kita lakukan mapping manual untuk efisiensi (menghindari N+1 query)
        $pengajuanIds = $penunjukanTpak->pluck('pengajuan_id')->unique();
        $tpakDosenIds = $penunjukanTpak->pluck('idDosenTpak')->unique();

        $pengajuansData = Pengajuan::with('dosen.user')->whereIn('id', $pengajuanIds)->get(); // Eager load dosen.user
        $tpakDosensData = Dosen::join('users', 'dosens.users_id', '=', 'users.id')
            ->whereIn('dosens.id', $tpakDosenIds)
            ->select('dosens.id', 'users.nama_lengkap')
            ->get()
            ->pluck('nama_lengkap', 'id');

        // Hitung progress penilaian per pengajuan (total detail yang punya evaluasi / total detail)
        $detailCounts = \App\Models\Dupak\DetailPengajuan::select('pengajuan_id', DB::raw('count(*) as total'))
            ->whereIn('pengajuan_id', $pengajuanIds)
            ->groupBy('pengajuan_id')
            ->pluck('total', 'pengajuan_id')
            ->toArray();

        $evaluatedCounts = \App\Models\Dupak\HasilEvaluasi::select('detail_pengajuan.pengajuan_id', DB::raw('count(distinct hasil_evaluasi.detail_pengajuan_id) as total'))
            ->join('detail_pengajuan', 'hasil_evaluasi.detail_pengajuan_id', '=', 'detail_pengajuan.id')
            ->whereIn('detail_pengajuan.pengajuan_id', $pengajuanIds)
            ->groupBy('detail_pengajuan.pengajuan_id')
            ->pluck('total', 'detail_pengajuan.pengajuan_id')
            ->toArray();

        $penunjukanTpak->getCollection()->transform(function ($item) use ($pengajuansData, $tpakDosensData, $detailCounts, $evaluatedCounts) { // Menggunakan $pengajuansData yang sudah eager loaded
            $p = $pengajuansData->firstWhere('id', $item->pengajuan_id);
            $item->pengaju_nama = $p->dosen->user->nama_lengkap ?? 'N/A'; // Akses nama melalui relasi
            $item->tpak_nama_lengkap = $tpakDosensData[$item->idDosenTpak] ?? 'N/A';
            $item->created_at = Carbon::parse($item->created_at);

            $totalDetail = $detailCounts[$item->pengajuan_id] ?? 0;
            $evaluated = $evaluatedCounts[$item->pengajuan_id] ?? 0;
            $item->progress_total = $totalDetail;
            $item->progress_evaluated = $evaluated;
            $item->progress_percent = $totalDetail > 0 ? round(($evaluated / $totalDetail) * 100) : 0;

            return $item;
        });

        return view('dupak.penunjukan_tpak.index', compact('dosens', 'pengajuan', 'penunjukanTpak', 'tpakCounts', 'dosenWorkload', 'pengajuMap', 'assignedMap'));
    }

    /**
     * Mapping level JFA untuk validasi.
     * Semakin tinggi angka, semakin tinggi jabatannya.
     */
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
        return $map[$nama] ?? 0; // 0 = tidak dikenali
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|exists:dupak.pengajuan,id',
            'idDosenTpak' => 'required|exists:dosens,id',
            'catatan' => 'nullable|string',
        ]);

        try {
            // 1. Ambil dan cek status pengajuan
            $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);
            $finalStatuses = ['Diterima', 'Ditolak', 'Selesai'];
            if (in_array($pengajuan->status, $finalStatuses)) {
                return redirect()->back()->with('error', 'Pengajuan sudah final (' . $pengajuan->status . '). Tidak dapat menambahkan TPAK lagi.');
            }

            // 2. Validasi: Dosen tidak boleh menilai pengajuannya sendiri
            if ($pengajuan->idDosen == $request->idDosenTpak) {
                return redirect()->back()->with('error', 'Dosen tidak diperbolehkan menjadi penilai (TPAK) untuk pengajuannya sendiri.');
            }

            // 2. Validasi: Cek apakah sudah pernah ditunjuk (duplikasi)
            $isDuplicate = PenunjukanTPAKModel::where('pengajuan_id', $request->pengajuan_id)
                ->where('idDosenTpak', $request->idDosenTpak)
                ->exists();
            if ($isDuplicate) {
                return redirect()->back()->with('error', 'Dosen tersebut sudah ditunjuk sebagai TPAK untuk pengajuan ini.');
            }

            // 3. Validasi: Maksimal 5 TPAK (Sesuai kebijakan)
            $tpakCount = PenunjukanTPAKModel::where('pengajuan_id', $request->pengajuan_id)->count();
            if ($tpakCount >= 5) {
                return redirect()->back()->with('error', 'Maksimal jumlah TPAK untuk satu pengajuan adalah 5 orang.');
            }

            // 4. Validasi: JFA TPAK harus >= JFA Tujuan Pengaju
            // Ambil JFA aktif TPAK dari database sdm_tus
            $tpakJfa = DB::connection('mysql')
                ->table('riwayat_jabatan_fungsional_akademiks')
                ->join('ref_jabatan_fungsional_akademiks', 'riwayat_jabatan_fungsional_akademiks.ref_jfa_id', '=', 'ref_jabatan_fungsional_akademiks.id')
                ->where('riwayat_jabatan_fungsional_akademiks.dosen_id', $request->idDosenTpak)
                ->whereNull('riwayat_jabatan_fungsional_akademiks.tmt_selesai')
                ->orderBy('riwayat_jabatan_fungsional_akademiks.tmt_mulai', 'desc')
                ->select('ref_jabatan_fungsional_akademiks.nama_jabatan')
                ->first();

            if (!$tpakJfa || empty($tpakJfa->nama_jabatan)) {
                return redirect()->back()->with('error', 'Dosen yang dipilih tidak memiliki Jabatan Fungsional Akademik (JFA) aktif. Penunjukan dibatalkan.');
            }

            // Ambil JFA Tujuan pengaju
            $jfaTujuanPengaju = null;
            if ($pengajuan->jfaTujuan) {
                $jfaTujuanPengaju = DB::connection('mysql')
                    ->table('ref_jabatan_fungsional_akademiks')
                    ->where('id', $pengajuan->jfaTujuan)
                    ->value('nama_jabatan');
            }

            // Bandingkan level JFA (hanya jika keduanya terdeteksi)
            if ($jfaTujuanPengaju) {
                $levelTpak = $this->getJfaLevel($tpakJfa->nama_jabatan);
                $levelPengaju = $this->getJfaLevel($jfaTujuanPengaju);

                if ($levelTpak > 0 && $levelPengaju > 0 && $levelTpak < $levelPengaju) {
                    return redirect()->back()->with(
                        'error',
                        "JFA TPAK ({$tpakJfa->nama_jabatan}) lebih rendah dari JFA Tujuan Pengaju ({$jfaTujuanPengaju}). Penunjukan dibatalkan demi keadilan penilaian."
                    );
                }
            }

            // Simpan menggunakan Model
            PenunjukanTPAKModel::create([
                'pengajuan_id' => $request->pengajuan_id,
                'idDosenTpak' => $request->idDosenTpak,
                'catatan' => $request->catatan,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('dupak.penunjukan_tpak.index')->with('success', 'TPAK berhasil ditunjuk dan ditugaskan.');
        } catch (\Exception $e) {
            // Log exception lengkap untuk debugging
            Log::error('Gagal menyimpan penunjukan TPAK', [
                'message' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->only(['pengajuan_id', 'idDosenTpak']),
            ]);

            $debugMessage = config('app.debug')
                ? ' [DEBUG: ' . $e->getMessage() . ' — ' . get_class($e) . ']'
                : '';

            return redirect()->back()->with('error', 'Terjadi kesalahan teknis saat menyimpan penunjukan. Silakan coba lagi atau hubungi admin.' . $debugMessage);
        }
    }

    public function destroy($id)
    {
        try {
            $penunjukan = PenunjukanTPAKModel::find($id);
            if (!$penunjukan) {
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
                ? ' [DEBUG: ' . $e->getMessage() . ']'
                : '';

            return redirect()->route('dupak.penunjukan_tpak.index')->with('error', 'Terjadi kesalahan teknis saat membatalkan penunjukan. Silakan coba lagi.' . $debugMessage);
        }
    }
}
