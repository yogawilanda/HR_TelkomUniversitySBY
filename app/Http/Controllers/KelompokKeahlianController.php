<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\KelompokKeahlian;
use App\Models\Work_Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KelompokKeahlianController extends Controller
{
    public string $aksi = 'Kelompok Keahlian';

    public function index()
    {
        $kelompok_keahlian = KelompokKeahlian::withCount('sub_kk')->with('fakultas')->get();
        // dd($kelompok_keahlian, 'amsuk');
        return view('kelola_data.kelompok_keahlian.list2',compact('kelompok_keahlian'));
    }

    public function create()
    {
        $fakultas = Work_Position::query()
    ->where('type_work_position', 'Fakultas')
    ->orderBy('position_name')
    ->get();
        $this->MakeLog('User Mengakses halaman tambah Data '.$this->aksi);

        $route = view('kelola_data.kelompok_keahlian.input', compact('fakultas'));
        return $this->CekReview($route, '1D1', 'MELIHAT DATA KELOMPOK KEAHLIAN');

    }

    public function store(Request $request)
    {
        // dd($request);
        $validated = $request->validate($this->validation()[0], $this->validation()[1], $this->validation()[2]);
        try {
            DB::beginTransaction();
            $cek_exist_code = KelompokKeahlian::where('kode', $request->kode)->first();



            if ($cek_exist_code) {
                throw new \Exception('Kode Kelompok Keahlian ini sudah terdaftar, mohon coba yang lain!.');
            }
            $cek_exist_fakultas = Work_Position::where([
                ['id', '=', $request->fakultas_id],
                ['type_work_position', '=', 'Fakultas'],
            ])->first();

            if (! $cek_exist_fakultas) {
                throw new \Exception('Fakultas tidak terdaftar di sistem, mohon coba yang lain!.');
                }
                $cek_exist_nama_with_the_same_fakultas = KelompokKeahlian::where('nama', $request->nama)->where('fakultas_id', $request->fakultas_id)->first();
            if($cek_exist_nama_with_the_same_fakultas){
                throw new \Exception('Kelompok Keahlian dengan nama dan fakultas ini sudah terdaftar!.');

            }


            $save = KelompokKeahlian::create($validated);
            if (! $save) {
                throw new \Exception('Terjadi masalah saat menyimpan data, mohon coba lagi dalam beberapa saat!.');
            }
            DB::commit();
            $this->MakeLog('User Berhasil Menambahkan Data '.$this->aksi, ['data' => $save]);

            $route = redirect()->route('manage.kelompok-keahlian.list')->with('success', 'Kelompok Keahlian berhasil ditambahkan');
            return $this->CekReview($route, '1D2', 'MANAMBAH DATA KELOMPOK KEAHLIAN');

        } catch (\Exception $e) {
            DB::rollback();
            $this->MakeLog('User Gagal Menambahkan Data '.$this->aksi, ['alasan' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error_alert' => $e->getMessage()]);
        }

    }

    public function show($id)
    {
        try {
            $kelompokKeahlian = null;
            try {
                $kelompokKeahlian = KelompokKeahlian::with('dosen.pegawai')->findOrFail($id);
                // $cek_kode = RefJenjangPendidikan::findOrFail($request->id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Kelompok Keahlian ini tidak terdaftar!.');
            }

            // Ambil semua dosen yang belum tergabung di KK ini
            $allDosen = \App\Models\Dosen::with('pegawai')
                ->whereDoesntHave('kelompokKeahlian', function ($q) use ($id) {
                    $q->where('kelompok_keahlian.id', $id);
                })->get();

            // Dosen nonaktif: opsional, misal dosen yang pernah tergabung lalu di-nonaktifkan (detach)
            // Jika ingin menampilkan dosen yang tidak lagi tergabung, perlu histori atau soft delete pada pivot
            // Untuk sementara, kosongkan saja jika belum ada logika nonaktif sebenarnya
            $nonaktifDosen = [];
            $this->MakeLog('User Berhasil Melihat Data '.$this->aksi, ['data' => $kelompokKeahlian]);

            return view('kelola_data.kelompok_keahlian.view', compact('kelompokKeahlian', 'allDosen', 'nonaktifDosen'));
        } catch (\Exception $e) {
            $this->MakeLog('User Gagal Mengakses Halaman Lihat Data '.$this->aksi, ['alasan' => $e->getMessage()]);

            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $kelompokKeahlian = null;
            try {
                $kelompokKeahlian = KelompokKeahlian::with('dosen.pegawai')->findOrFail($id);
                // $cek_kode = RefJenjangPendidikan::findOrFail($request->id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Kelompok Keahlian ini tidak terdaftar!.');
            }
            $this->MakeLog('User Berhasil Mengakses Halaman '.$this->aksi);

            return view('kelola_data.kelompok_keahlian.edit', compact('kelompokKeahlian'));
        } catch (\Exception $e) {
            $this->MakeLog('User Gagal Mengakses Halaman Ubah Data '.$this->aksi, ['alasan' => $e->getMessage()]);

            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate($this->validation()[0], $this->validation()[1], $this->validation()[2]);
        try {
            DB::beginTransaction();
            try {
                $cek_kk = KelompokKeahlian::findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Kode Kelompok Keahlian ini tidak terdaftar!.');
            }
            $cek_exist_fakultas = Work_Position::where([
                ['id', '=', $request->fakultas_id],
                ['type_work_position', '=', 'Fakultas'],
            ])->first();

            if (! $cek_exist_fakultas) {
                throw new \Exception('Fakultas tidak terdaftar di sistem, mohon coba yang lain!.');
            }

            $save = $cek_kk->update($validated);
            if (! $save) {
                throw new \Exception('Terjadi masalah saat menyimpan data, mohon coba lagi dalam beberapa saat!.');
            }
            DB::commit();
            $this->MakeLog('User Berhasil Mengubah Data '.$this->aksi, ['data' => $save]);

            return $this->handleRedirectBack()->with('success', 'Kelompok Keahlian berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            $this->MakeLog('User Gagal Mengubah Data '.$this->aksi, ['alasan' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error_alert' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        // try {
        //     $kelompokKeahlian = null;
        //     try {
        //         $kelompokKeahlian = KelompokKeahlian::with('dosen.pegawai')->findOrFail($id);
        //         $kelompokKeahlian->delete();
        //         // $cek_kode = RefJenjangPendidikan::findOrFail($request->id);
        //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        //         throw new \Exception('Kelompok Keahlian ini tidak terdaftar!.');
        //     }

        //     return redirect()->route('manage.kelompok-keahlian.list')->with('success', 'Kelompok Keahlian berhasil dihapus');
        // } catch (\Exception $e) {

        //     return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        // }
    }

    public function nonaktifkan(Request $request, $id)
    {
        try {

            $validated = $request->validate([
                'dosen_id' => 'required|exists:dosens,id',
            ]);

            $kelompokKeahlian = null;
            try {
                $kelompokKeahlian = KelompokKeahlian::with('dosen.pegawai')->findOrFail($id);
                // $cek_kode = RefJenjangPendidikan::findOrFail($request->id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Kelompok Keahlian ini tidak terdaftar!.');
            }
            $save = $kelompokKeahlian->dosen()->detach($validated['dosen_id']);
            $this->MakeLog('User Berhasil Menonaktifkan Dosen '.$this->aksi, ['data' => $save]);

            return $this->handleRedirectBack()->with('success', 'Dosen berhasil dinonaktifkan dari kelompok keahlian');
        } catch (\Exception $e) {
            $this->MakeLog('User Gagal Menonaktifkan Data '.$this->aksi, ['alasan' => $e->getMessage()]);
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function assignDosen(Request $request, $id)
    {
        try {

            $validated = $request->validate([
                'dosen_id' => 'required|array',
                'dosen_id.*' => 'exists:dosens,id',
            ]);

            $kelompokKeahlian = null;
            try {
                $kelompokKeahlian = KelompokKeahlian::with('dosen.pegawai')->findOrFail($id);
                // $cek_kode = RefJenjangPendidikan::findOrFail($request->id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Kelompok Keahlian ini tidak terdaftar!.');
            }
            $save = $kelompokKeahlian->dosen()->syncWithoutDetaching($validated['dosen_id']);
            $this->MakeLog('User Berhasil Menambahkan Dosen Ke Data '.$this->aksi, ['data' => $save]);

            return $this->handleRedirectBack()->with('success', 'Dosen berhasil ditambahkan ke kelompok keahlian');
        } catch (\Exception $e) {
            $this->MakeLog('User Gagal Menambahkan Dosen ke Data '.$this->aksi, ['alasan' => $e->getMessage()]);

            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function pegawaiList()
    {
        $dosen = Dosen::with('kelompokKeahlian', 'pegawai')->get();
        // dd($dosen);
        $this->MakeLog('User Mengakses Halaman Pegawai List '.$this->aksi);

        return view('kelola_data.kelompok_keahlian.pegawai_list', compact('dosen'));
    }

    public function validation()
    {
        return [
            [
                'nama' => 'required|string|max:255',
                'kode' => 'required|string|max:50',
                'deskripsi' => 'required|string|max:255',
                'fakultas_id' => ['required', 'string', 'max:100',
                    Rule::exists('work_positions', 'id')->where(function ($query) {
                        $query->where('type_work_position', 'Fakultas');
                    }),
                ],
            ], [
                '*.required' => ':attribute Wajib Diisi',
                '*.exists' => ':attribute tidak valid!',
            ], [
                'nama' => 'Nama Kelompok Keahlian',
                'kode' => 'Singkatan Kelompok Keahlian',
                'fakultas_id' => 'Fakultas Kelompok Keahlian',
                'deskripsi' => 'Deskripsi Kelompok Keahlian',
            ],
        ];

        return [
            [
                'nama' => 'required|string|max:255',
                'kode' => 'required|string|max:50|unique:ref_sub_kelompok_keahlians,kode',
                'deskripsi' => 'required|string|max:255',
                'kk_id' => 'required|string|max:100',
            ],
            [
                '*.required' => ':attribute wajib diisi!',
                '*.unique' => ':attribute sudah terdaftar, silahkan coba yang lain!',
            ],
            [
                'nama' => 'Nama Sub Kelompok Keahlian',
                'kode' => 'Singkatan Sub Kelompok Keahlian',
                'kk_id' => 'Kelompok Keahlian Sub',
                'deskripsi' => 'Deskripsi Sub Kelompok Keahlian',
            ],
        ];
    }
}
