<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\formation;
use App\Models\Level;
use App\Models\Prodi;
use App\Models\ref_work_position;
use App\Models\RefBagian;
use App\Models\work_position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class FormationController extends Controller
{
    
    public function index()
    {   
        $formations = json_decode(Formation::with(['bagian','level_id','atasan_formation'])
                                    ->orderBy('atasan_formasi_id')
                                    ->get());
        
        // dd($formations);
        // dd('masuk');
        
            // return view('kelola_data.fakultas.list',compact('send'));
            return view('kelola_data.sotk-formasi.list', compact('formations'));
    }

    public function new()
    {
        $levels = Level::all()->sortBy('nama_level');
        // $bagians = work_position::all()->sortBy('position_name');
        $bagians = work_position::all()->sortBy(['type_work_position','position_name']);

        $formations = Formation::all()->sortBy('nama_formasi');

        return view('kelola_data.sotk-formasi.input', compact('levels', 'bagians', 'formations'));
    }

    public function create(Request $request)
    {
        // dd($request->all());
        // dd($request);
        $validated = $request->validate([
            'nama_formasi' => ['required', 'string', 'max:100'],
            'kuota' => ['required', 'integer'],
            'level_id' => ['required'],
            'atasan_formasi_id' => ['nullable'],

            'work_position_id' => ['required',],
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'integer' => ':attribute harus berupa angka.',
        ]);

        DB::beginTransaction();
        
        // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {
            // $validated['work_position_id']=$validated['bagian'];
            $level = Formation::create($validated);
            DB::commit();
            // dd('done');
            return redirect(route('manage.formasi.list'))->with('success', 'Formasi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Formasi',
                'error' => $e->getMessage()
            ], 500);
        }
        // return redirect()->route('manage.formasi.list')->with('success', 'Data Formasi Berhasil Ditambahkan');
    }

    public function update(Request $request, $idFormasi)
    {
        $levels = Level::all()->sortBy('nama_level');
        $work_position = work_position::all()->sortBy(['type_work_position','position_name']);
        $ref_bagian = ref_work_position::all()->sortBy('position_Name');
        $formations = Formation::all()->sortBy('nama_formasi');
        // $formation_target = Formation::find($idFormasi);
        $formation_target = Formation::with([
            'level_id',
            'atasan_formation',
            'bagian',
        ])->findOrFail($idFormasi);
        $formation_target['atasan'] = $formation_target->atasan_formation()->first();
        $formation_target['level_data'] = $formation_target->level_id()->first();
        $formation_target['bagian_data'] = $formation_target->bagian()->first();
        // dd($fakultas);



        return view('kelola_data.sotk-formasi.edit', compact('levels', 'work_position', 'formations', 'formation_target','ref_bagian'));
    }

    public function update_data(Request $request, $idFormasi)
    {
        // dd($request->all());
        $validated = $request->validate([
            'nama_formasi' => ['required', 'string', 'max:100'],
            'kuota' => ['required', 'integer'],
            'level_id' => ['required'],
            'atasan_formasi_id' => ['nullable'],
            'work_position_id' => ['required'],
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'integer' => ':attribute harus berupa angka.',
            'required_without_all' => 'Minimal salah satu dari bagian / prodi / fakultas harus diisi.',
        ]);

        DB::beginTransaction();
        try {
            $formation = Formation::where('id', $idFormasi)->update($validated);
            DB::commit();
            return redirect(route('manage.formasi.list'))->with('success', 'Formasi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Formasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_structure_top($id_pemetaan){

    }

    public function get_structure_down($id_pemetaan){
        
    }

    public function get_structure_top_down($id_pemetaan){

    }
}
