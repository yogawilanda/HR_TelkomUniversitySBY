<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\RefStatusPegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RefStatusPegawaiController extends Controller
{
    public function list()
    {
        $data = RefStatusPegawai::all()->sortBy('status_pegawai');
        // dd(route('manage.status-pegawai.update'));
        $route = view('kelola_data.status_pegawai.list', compact('data'));
        return $this->CekReview($route, '1W3', 'MELIHAT DATA REFERENSI STATUS KEPEGAWAIAN', true);


        }

    public function update(Request $request)
    {
        // dd($validated);
        try {
            $validation = $this->validation($request->id);
            $validated = $request->validate($validation[0],$validation[1],$validation[2]);
            DB::beginTransaction();
            $capitalize = strtoupper($request->status_pegawai);
            $request['status_pegawai'] = $capitalize;


            $cek_sg = null;
            try {
                $cek_sp = RefStatusPegawai::findOrFail($request->id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Referensi Status Pegawai ini tidak terdaftar!.');
            }

            if (!$cek_sp) {
                throw new \Exception('Bagian Tidak Ditemukan!.');
            }
            // dd($validated);
            $save = $cek_sp->update($request->all());
            if (!$save) {
                throw new \Exception('Terjadi masalah ketika melakukan proses simpan bagian!.');
            }
            // dd($save, $cek_wp, $val);
            DB::commit();
            $route = redirect(route('manage.status-pegawai.list'))->with('success', 'Data Status Pegawai berhasil diperbaharui!');
        return $this->CekReview($route, '1W2', 'MENGUBAH DATA REFERENSI STATUS KEPEGAWAIAN');

            } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        // dd($validated);
        try {
            $validation = $this->validation();
            $validated = $request->validate($validation[0],$validation[1],$validation[2]);
            DB::beginTransaction();
            $capitalize = strtoupper($request->status_pegawai);
            $request['status_pegawai'] = $capitalize;


            // dd($validated);
            $save = RefStatusPegawai::create($request->all());
            if (!$save) {
                throw new \Exception('Terjadi masalah ketika melakukan proses simpan status pegawai!.');
            }
            DB::commit();
            $route = redirect(route('manage.status-pegawai.list'))->with('success', 'Data Status Pegawai berhasil ditambahkan!');
        return $this->CekReview($route, '1W1', 'MENAMBAH DATA REFERENSI STATUS KEPEGAWAIAN');

            } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function validation($id=null)
    {
        $id = $id==null?'':','.$id;
        return [
            [
                'id'   => 'nullable|string|max:100|exists:ref_status_pegawais,id',
                'status_pegawai' => [
                        'required',
                        'regex:/^[A-Za-z\s]+$/',
                        'unique:ref_status_pegawais,status_pegawai'.$id,
                    ]
            ],
            [
                'required' => ':attribute tidak boleh kosong!',
                'in'       => 'Pilihan pada :attribute tidak tersedia.',
                'status_pegawai.required' => 'Status pegawai wajib diisi.',
                'status_pegawai.in'       => 'Status pegawai hanya boleh Bagian, Direktorat, Program Studi, atau Fakultas.',
                'status_pegawai.regex'    => 'Status pegawai tidak boleh berupa angka saja dan tidak boleh mengandung karakter khusus.',
                'unique' => ':attribute Sudah Terdaftar!.'
            ],
            [
                'id' => 'Referensi Status Pegawai',
                'status_pegawai' => 'Nama Status Pegawai',
            ]
        ];
    }
}
