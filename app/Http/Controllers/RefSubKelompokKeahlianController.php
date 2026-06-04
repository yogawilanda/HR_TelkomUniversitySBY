<?php

namespace App\Http\Controllers;

use App\Models\KelompokKeahlian;
use App\Models\RefSubKelompokKeahlian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RefSubKelompokKeahlianController extends Controller
{
    public function index()
    {
        if (request('destination')=='all') {
            $sub_kk = RefSubKelompokKeahlian::withCount('dosenDipetakan')->with('KK')->get();
        } else {
            $sub_kk = RefSubKelompokKeahlian::has('dosenDipetakan')
            ->withCount('dosenDipetakan')
            ->with('KK')
            ->get();
        }
        return view('kelola_data.kelompok_keahlian.sub.list', compact('sub_kk'));
    }

    public function create()
    {
        $kk = KelompokKeahlian::all()->sortBy('nama');

        return view('kelola_data.kelompok_keahlian.sub.input', compact('kk'));
    }

    public function store(Request $request)
    {
        $validation = $this->validation($request);
        $validated = $request->validate($validation[0], $validation[1], $validation[2]);
        try {
            DB::beginTransaction();
            $cek_exist_code = RefSubKelompokKeahlian::where('kode', $request->kode)->first();
            $cek_exist_kk = KelompokKeahlian::where('id', $request->kk_id)->first();

            if ($cek_exist_code) {
                throw new \Exception('Kode Sub Kelompok Keahlian ini sudah terdaftar, mohon coba yang lain!.');
            }

            if (! $cek_exist_kk) {
                throw new \Exception('Kelompok Keahlian tidak terdaftar di sistem, mohon coba yang lain!.');
            }

            $save = RefSubKelompokKeahlian::create($validated);
            if (! $save) {
                throw new \Exception('Terjadi masalah saat menyimpan data, mohon coba lagi dalam beberapa saat!.');
            }
            DB::commit();

            $route = redirect(route('manage.kelompok-keahlian.sub.list',['destination'=>'all']))->with('success', 'Sub Kelompok Keahlian berhasil ditambahkan');

            return $this->CekReview($route, '1D3', 'MANAMBAH DATA SUB KELOMPOK KEAHLIAN', true);

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->withInput($validated)
                ->withErrors(['error_alert' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cek_sub_kk = RefSubKelompokKeahlian::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->handleRedirectBack()->with('error_alert','Kode Sub Kelompok Keahlian ini tidak terdaftar!.');
        }
        $validation = $this->validation($request,$id);
        $validated = $request->validate($validation[0], $validation[1], $validation[2]);
        try {
            DB::beginTransaction();

            $cek_exist_kk = KelompokKeahlian::where('id', $request->kk_id)->first();

            if (! $cek_exist_kk) {
                throw new \Exception('Kelompok Keahlian tidak terdaftar di sistem, mohon coba yang lain!.');
            }

            $save = $cek_sub_kk->update($validated);
            if (! $save) {
                throw new \Exception('Terjadi masalah saat menyimpan data, mohon coba lagi dalam beberapa saat!.');
            }
            DB::commit();

            return $this->handleRedirectBack()->with('success', 'Sub Kelompok Keahlian berhasil diperbarui!.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error_alert' => $e->getMessage()]);
        }
    }

    public function validation($request, $id=null)
    {
        return [
            [
                'nama' => [
                    'required',
                    'string',
                    'max:200',
                    Rule::unique('ref_sub_kelompok_keahlians', 'nama')
                        ->where('kk_id', $request->kk_id)
                        ->ignore($id),
                ],
                'kode' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('ref_sub_kelompok_keahlians', 'kode')->ignore($id),
                ],
                'deskripsi' => 'required|string|max:255',
                'kk_id' => 'required|string|max:100|exists:kelompok_keahlian,id',
            ],
            [
                'nama.unique' => ':attribute Nama Sub dengan Kelompok Keahlian ini sudah terdaftar!.',
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
