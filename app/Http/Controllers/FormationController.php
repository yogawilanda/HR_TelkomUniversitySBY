<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Formation;
use App\Models\Level;
use App\Models\Prodi;
use App\Models\RefWorkPosition;
use App\Models\RefBagian;
use App\Models\Work_Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class FormationController extends Controller
{
    public string $aksi = 'Formasi';

    public function index()
    {
        $formations = json_decode(Formation::with(['bagian','level_data','atasan_formation'])
                                    ->orderBy('atasan_formasi_id')
                                    ->get());
                $this->MakeLog('User Mengakses Halaman List Data '.$this->aksi);

            return view('kelola_data.sotk-formasi.list', compact('formations'));
    }

    public function new()
    {
        $levels = Level::all()->sortBy('nama_level');
        // $bagians = Work_Position::all()->sortBy('position_name');
        $bagians = Work_Position::all()->sortBy(['type_work_position','position_name']);

        $formations = Formation::all()->sortBy('nama_formasi');
                $this->MakeLog('User Mengakses Halaman Tambah Data '.$this->aksi);

        $route = view('kelola_data.sotk-formasi.input', compact('levels', 'bagians', 'formations'));
        return $this->CekReview($route, '1L2', 'MELIHAT DATA FORMASI');

    }

    public function create(Request $request)
    {
        // dd($request->all());
        // dd($request);
        $validation = $this->validation($request);
        $validated = $request->validate($validation[0],$validation[1],$validation[2]);

        DB::beginTransaction();

        // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {
            // $validated['work_position_id']=$validated['bagian'];
            $level = Formation::create($validated);
            DB::commit();
            // dd('done');
                $this->MakeLog('User Menambahkan Data '.$this->aksi,['data' => $level]);

            $route = redirect(route('manage.formasi.list'))->with('success', 'Formasi berhasil dibuat.');
            return $this->CekReview($route, '1L1', 'MENAMBAH DATA FORMASI');

        } catch (\Exception $e) {
            DB::rollBack();
                $this->MakeLog('User Gagal Menambahkan Data '.$this->aksi,['alasan' => $e->getMessage()]);
            return redirect()->back()->withInput($request->all())->with('error_alert', $e->getMessage());
        }
        // return redirect()->route('manage.formasi.list')->with('success', 'Data Formasi Berhasil Ditambahkan');
    }

    public function update($idFormasi)
    {
        $formation_target = null;
        try{
            $formation_target = Formation::with([
                'level_data',
                'atasan_formation',
                'bagian',
            ])->findOrFail($idFormasi);
        }catch(\Exception $e){
            return $this->handleRedirectBack()->with('error_alert', 'Data Formasi Tidak Ditemukan!.');
        }


        $levels = Level::all()->sortBy('nama_level');
        $work_position = Work_Position::all()->sortBy(['type_work_position','position_name']);
        $ref_bagian = RefWorkPosition::all()->sortBy('position_Name');
        $formations = Formation::all()->sortBy('nama_formasi');
        // $formation_target = Formation::find($idFormasi);
        $formation_target['atasan'] = $formation_target->atasan_formation()->first();
        $formation_target['level_data'] = $formation_target->level_data()->first();
        $formation_target['bagian_data'] = $formation_target->bagian()->first();
        // dd($fakultas);


                $this->MakeLog('User Mengakses Halaman Ubah Data '.$this->aksi,['data' => $formation_target]);

        $route = view('kelola_data.sotk-formasi.edit', compact('levels', 'work_position', 'formations', 'formation_target','ref_bagian'));
        return $this->CekReview($route, '1L2', 'MELIHAT DATA FORMASI');

    }

    public function update_data(Request $request, $idFormasi)
    {
        // $formation = null;
        // try{
            // dump('cek');
            $formation = Formation::where('id', $idFormasi)->first();
            if(!$formation){
                return $this->handleRedirectBack()->with('error_alert', 'Data Formasi Tidak Ditemukan!.');
            }
        // }catch(\Exception $e){
        // }

        $validation = $this->validation($request, $idFormasi);
        $validated = $request->validate($validation[0],$validation[1],$validation[2]);

        DB::beginTransaction();
        try {

            $old = $formation;
            $save = $formation->update($validated);

                $this->MakeLog('User Mengubah Data '.$this->aksi,['data lama' => $old,'data baru' => $formation]);
            DB::commit();
            $route = redirect(route('manage.formasi.list'))->with('success', 'Formasi berhasil diperbarui.');
            return $this->CekReview($route, '1L3', 'MENGUBAH DATA FORMASI');

        } catch (\Exception $e) {
            DB::rollBack();
                $this->MakeLog('User Gagal Mengubah Data '.$this->aksi,['alasan' => $e->getMessage()]);

            return redirect()->back()->withInput($request->all())->with('error_alert', $e->getMessage());
       }
    }

    public function validation($request, $id=null){
        $id = $id==null?',null':','.$id;
        return [[
            'level_id' => ['required','exists:levels,id','uuid'],
            'nama_formasi' => ['required', 'string','regex:/^(?=.*[A-Za-z]).+$/', 'max:100','unique:formations,nama_formasi' . $id . ',id,work_position_id,' . $request->work_position_id.',work_position_id,' . $request->work_position_id],
            'kuota' => ['required', 'integer'],
            'atasan_formasi_id' => ['nullable','exists:formations,id','uuid'],
            'work_position_id' => ['required','exists:work_positions,id','uuid'],
        ], [
            'nama_formasi.unique' => 'Nama Formasi Dengan Level juga Bagian ini sudah terdaftar!.',
            'unique' => ':attribute Sudah Terdaftar',
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'integer' => ':attribute harus berupa angka.',
            'exists' => ':attribute belum terdaftar atau tidak ditemukan',
            'uuid' => ':attribute Belum Terdaftar'
        ],[
            'nama_formasi' => 'Nama Formasi',
            'kuota' => 'Batas Pengisian atau Kuota',
            'level_id' => 'Level',
            'atasan_formasi_id' => 'Formasi Atasan',
            'work_position_id' => 'Bagian'
        ]];
    }
}
