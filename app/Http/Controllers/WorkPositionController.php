<?php

namespace App\Http\Controllers;

use App\Models\Work_Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkPositionController extends Controller
{
    public function list()
    {
        $work_positions = Work_Position::all()->sortBy('position_name');
        return view('kelola_data.bagian.list', compact('work_positions'));
    }

    public function new()
    {
        $route = view('kelola_data.bagian.input');
            return $this->CekReview($route, '1J1', 'MELIHAT DATA BAGIAN KERJA');

    }

    public function edit(Request $request, $id_wp)
    {
        $wp = Work_Position::where('id', $id_wp)->first();
        if (!$wp) {
            return $this->handleRedirectBack()->with('error_alert', 'Data Bagian Kerja Tidak Ditemukan!');
        }
        $route = view('kelola_data.bagian.update', compact('wp'));
            return $this->CekReview($route, '1J1', 'MELIHAT DATA BAGIAN KERJA');

    }

    public function create(Request $request)
    {
        $validation = $this->validation();
        $validated = $request->validate($validation[0],$validation[1],$validation[2]);
        // dd($request, $validated);

        try {
            DB::beginTransaction();

            $save = Work_Position::create($request->all());
            if (!$save) {
                throw new \Exception('Terjadi masalah ketika melakukan proses simpan bagian!.');
            }
            DB::commit();
            $route = redirect(route('manage.bagian.list'))->with('success', 'Data bagian berhasil ditambahkan!');
            return $this->CekReview($route, '1J2', 'MENAMBAH DATA BAGIAN KERJA');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function update(Request $request, $id_wp)
    {
        $cek_wp = null;

        try {
            $cek_wp = Work_Position::findOrFail($id_wp);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->handleRedirectBack()->with('error_alert','Bagian ini tidak terdaftar!.');
        }
        // dd($request->all());
        $validation = $this->validation($id_wp);
        $validated = $request->validate($validation[0],$validation[1],$validation[2]);
        // dd($validated);
        try {
            DB::beginTransaction();


            // dd($validated);
            $save = $cek_wp->update($request->all());
            if (!$save) {
                throw new \Exception('Terjadi masalah ketika melakukan proses simpan bagian!.');
            }
            // dd($save, $cek_wp, $val);
            DB::commit();
            $route = redirect(route('manage.bagian.list'))->with('success', 'Data bagian berhasil diperbaharui!');
            return $this->CekReview($route, '1J3', 'MENGUBAH DATA BAGIAN KERJA');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function validation($id=null)
    {
        if($id==null){
            $id='';
        }
        else{
            $id = ','.$id;
        }
        return [
            [
                'position_name' => [
                        'required',
                        'string',
                        'max:150',
                        'unique:work_positions,position_name'.$id,
                        'regex:/^[A-Za-z\s]+$/'
                    ],
                'type_work_position'   => 'required|exists:Ref_work_positions,position_name',
                'type_pekerja'  => 'required|in:Dosen,Tpa,Both',
                'kode'     => 'required|string|max:20|unique:work_positions,kode'.$id,
            ],
            [
                'required' => ':attribute tidak boleh kosong!',
                'in'       => 'Pilihan pada :attribute tidak tersedia.',
                'exists' => 'Pilihan :attribute ini tidak tersedia!.',
                'unique' => ':attribute ini sudah terdaftar!.'
            ],
            [
                'position_name' => 'Nama Bagian',
                'type_work_position' => 'Tipe Bagian',
                'type_pekerja' => 'Tipe Pekerja',
                'kode' => 'Singkatan Bagian',
            ]
        ];
    }
}
