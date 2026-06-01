<?php

namespace App\Http\Controllers;

use App\Models\Coe;
use App\Models\RefResearchCoe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoeController extends Controller
{
    public function index()
    {
        $data = Coe::with(['research'])
            ->withCount([
                'dosenCoe as active_dosen_coe' => function ($q) {
                    $q->where(function ($q) {
                        $q->where('tmt_selesai', '>=', today())
                            ->orWhereNull('tmt_selesai');
                    });
                },
            ])
            ->get()
            ->sortBy('nama_coe');

        // dd($data);
        // $data = Coe::with(['research','active_dosen_coe'])->get()->sortBy('nama_coe');
        $this->MakeLog('User mengakses halaman list data Coe');

        $route = view('kelola_data.coe.list', compact('data'));

        return $this->CekReview($route, '', '');

    }

    public function new()
    {
        $this->MakeLog('User mengakses halaman tambah data Coe');
        $research = RefResearchCoe::all()->sortBy('nama');

        $route = view('kelola_data.coe.input', compact('research'));
        return $this->CekReview($route, '1Q4', 'MELIHAT LIST DATA COE');

    }

    public function create(Request $request)
    {
        $validation= $this->validation($request);
        // dd($validation[0],$validation[1],$validation[2]);
        $validated = $request->validate($validation[0],$validation[1],$validation[2]);
        try {
            DB::beginTransaction();

            $cek_exist_kode = Coe::where('nama_coe', $request->nama_coe)->where('ref_research_id', $request->ref_research_id)->first();
            // dd($cek_exist_kode, $request->kode_coe,$request->ref_research_id);
            if ($cek_exist_kode) {
                throw new \Exception('CoE dengan Research Grub ini sudah terdaftar!.');
            }
            $validated['kode_coe'] = strtoupper($validated['kode_coe']);
            $save = Coe::create($validated);
            if (! $save) {
                throw new \Exception('Gagal menyimpan data!.');
            }
            DB::commit();

            $route = redirect(route('manage.coe.index'))->with('success', 'Data CoE Berhasil ditambahkan!.');

            return $this->CekReview($route, '1Q1', 'MENAMBAH DATA COE');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->MakeLog('User Gagal menambah data ', ['alasan' => $e->getMessage()]);

            return $this->handleRedirectBack()->withInput($request->all())->with('error_alert', $e->getMessage());
        }
    }

    public function show($id)
    {
        $coe = Coe::findOrFail($id);
        $this->MakeLog('User mengakses halaman lihat data Coe');

        return view('coe.show', compact('coe'));
    }

    public function edit($id_coe)
    {
        try {
            if ($id_coe == null) {
                throw new \Exception('Tidak ada research Rujukan!.');
            }
            $cek_exist_kode = null;
            try {
                $cek_exist_kode = Coe::findOrFail($id_coe);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $f) {
                throw new \Exception('CoE ini tidak terdaftar!.');
            }

            $coe = $cek_exist_kode;
            $research = RefResearchCoe::all()->sortBy('nama');

            $this->MakeLog('User mengakses halaman tambah data Coe');

            $route = view('kelola_data.coe.update', compact('coe', 'research'));
            return $this->CekReview($route, '1Q4', 'MELIHAT LIST DATA COE');

        } catch (\Exception $e) {
            $this->MakeLog('User Gagal mengakses halaman ubah data coe ', ['alasan' => $e->getMessage()]);
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function update(Request $request, $id_coe)
    {
        $validation=$this->validation($request, $id_coe);
        $validated = $request->validate($validation[0], $validation[1], $validation[2]);
        try {
            DB::beginTransaction();
            $cek_exist_kode = null;
            $old = null;
            try {
                $cek_exist_kode = Coe::findOrFail($id_coe);
                $old = Coe::where('id', $id_coe)->first();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $f) {
                throw new \Exception('CoE ini tidak terdaftar!.');
            }

            $cek_exist_kode = Coe::query()
                ->where('nama_coe', $request->nama_coe)
                ->where('ref_research_id', $request->ref_research_id)
                ->where('id', '!=', $id_coe)
                ->first();
            if ($cek_exist_kode) {
                throw new \Exception('CoE dengan Research Grub ini sudah terdaftar!.');
            }

            $validated['kode_coe'] = strtoupper($validated['kode_coe']);

            $save = $cek_exist_kode->update($validated);
            if (! $save) {
                throw new \Exception('Gagal memperbarui data!.');
            }
            DB::commit();
            // dd('lama',$old,'baru', $cek_exist_kode);
            $this->MakeLog('User Berhasil Perbarui Data COE', [
                'data lama' => $old,
                'data baru' => $cek_exist_kode,
            ]);

            $route =  redirect(route('manage.coe.index'))->with('success', 'Data CoE Berhasil diperbaharui!.');
            return $this->CekReview($route, '1Q3', 'MENGUBAH DATA COE');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->MakeLog('User Gagal mengubah data coe ', ['alasan' => $e->getMessage()]);
            return $this->handleRedirectBack()->withInput($request->all())->with('error_alert', $e->getMessage());
        }
    }

    public function validation($request, $id=null)
    {
        $id = $id==null?'':','.$id;
        return [
            [
                'kode_coe' => ['required', 'string', 'max:50', 'unique:coe,kode_coe'.$id,'regex:/^(?=.*[A-Za-z])[A-Za-z0-9\s]+$/'],
                'nama_coe' => ['required', 'string', 'max:200','regex:/^(?=.*[A-Za-z])[A-Za-z0-9@#$%^&*()_\-+=.,?!\s]+$/'],
                'ref_research_id' => ['required', 'string', 'max:100', 'exists:ref_research_coes,id'],
            ],
            [
                'required' => ':attribute wajib diisi',
                'string' => ':attribute harus berupa teks',
                'max' => ':attribute maksimal :max karakter',
                'exist' => ':attribute Tidak Terdaftar',
                'kode_coe.regex' => ':attribute harus mengandung huruf dan tidak boleh mengandung spasi maupun karakter khusus.',
                'nama_coe.regex' => ':attribute harus mengandung minimal satu huruf tidak boleh hanya angka saja atau karakter saja.',
            ],
            [
                'kode_coe' => 'Singkatan Research Group',
                'nama_coe' => 'Nama Research Group',
                'ref_research_id' => 'Research Group Pilihan',
            ],
        ];
    }
}
