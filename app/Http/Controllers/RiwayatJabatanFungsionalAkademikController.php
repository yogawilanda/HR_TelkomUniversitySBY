<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\RefJabatanFungsionalAkademik;
use App\Models\riwayatJabatanFungsionalAkademik;
use App\Models\SK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatJabatanFungsionalAkademikController extends Controller
{
    public function index()
    {
        $jfas = riwayatJabatanFungsionalAkademik::all();
        // dd(method_exists($jfas,'sk_ypt'));

        // dd($jfas[0]->dosen->pegawai->nama_lengkap,$jfas);

        return view('kelola_data.jfa.list',compact('jfas'));
    }

    public function new()
    {
        $dosens = Dosen::with('pegawai')
                ->get()
                ->sortBy('pegawai.nama_lengkap')
                ->values(); // reset index
                // dd($dosens);

        $jfas = RefJabatanFungsionalAkademik::all()->sortBy('nama_jabatan')->values();
        $sk_diktis = SK::Sk_Dikti();
        $sk_ypts = SK::Sk_Ypt();
        return view('kelola_data.jfa.input', compact('dosens', 'jfas', 'sk_diktis', 'sk_ypts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Dosen & JFA
            'dosen_id'      => ['required'],
            'ref_jfa_id'    => ['required'],
            'tmt_mulai'     => ['required','date'],

            /* ===============================
            VALIDASI SK LLKDIKTI
            =============================== */
            // Jika pilih existing: harus ada sk_llkdikti_id
            'sk_llkdikti_id' => ['nullable', 'required_without_all:file_sk_dikti,no_sk_dikti'],

            // Jika input baru: file & nomor wajib bila tidak memilih existing
            'file_sk_dikti'  => ['nullable','file','mimes:pdf,png,jpg,jpeg','required_without:sk_llkdikti_id'],
            'no_sk_dikti'    => ['nullable','string','max:50','required_without:sk_llkdikti_id','required_with:file_sk_dikti',],

            /* ===============================
            VALIDASI SK YPT
            (Boleh kosong semua)
            =============================== */
            'sk_pengakuan_ypt_id' => ['nullable'],

            'file_sk_ypt'   => ['nullable','file','mimes:pdf,png,jpg,jpeg'],
            'no_sk_ypt'     => ['nullable','string','max:50','required_with:file_sk_ypt',],

        ], [

            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

            'required_without'     => ':attribute wajib diisi jika :values tidak ada.',
            'required_without_all' => ':attribute wajib diisi jika :values tidak ada semuanya.',

        ], [
            // rename attributes biar rapi
            'sk_llkdikti_id'        => 'SK LLKDIKTI',
            'file_sk_dikti'         => 'file SK LLKDIKTI',
            'no_sk_dikti'           => 'Nomor SK LLKDIKTI',

            'sk_pengakuan_ypt_id'   => 'SK YPT',
            'file_sk_ypt'           => 'file SK YPT',
            'no_sk_ypt'             => 'Nomor SK YPT',
        ]);

        // DD('MASUK');

        // DD(isset($validated['sk_llkdikti_id']));
        DB::beginTransaction();
        // // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {



            if($validated['no_sk_dikti']!=null){
                $validated['sk_llkdikti_id'] = null;
            }
            if((!isset($validated['sk_llkdikti_id']))){
                // dd('masuk');

                try {
                    $validated['no_sk'] = $validated['no_sk_dikti'];
                    $validated['tipe_sk'] = 'LLDIKTI';
                    // DB::commit();

                    $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                    $sk = SK::create($validated);
                    $validated['sk_llkdikti_id'] = $sk->id;

                } catch (\Exception $e) {
                    // DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat SK LLDIKTI',
                        'error' => $e->getMessage()
                    ], 500);
                }

            }
            // dd($isset_ypt);
            if(isset($validated['sk_pengakuan_ypt_id'])||isset($validated['no_sk_ypt'])){
                if($validated['no_sk_ypt']!=null){
                    $validated['sk_pengakuan_ypt_id'] = null;
                }
                if((!isset($validated['sk_pengakuan_ypt_id']))){
                    // dd('masuk');

                    try {
                        $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'Pengakuan YPT';
                        // DB::commit();

                        $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                        $sk = SK::create($validated);
                        $validated['sk_pengakuan_ypt_id'] = $sk->id;

                    } catch (\Exception $e) {
                        // DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal membuat SK LLDIKTI',
                            'error' => $e->getMessage()
                        ], 500);
                    }

                }
            }else{
                $validated['sk_pengakuan_ypt_id'] = null;
            }


            // if(!isset($validated['sk_pengakuan_ypt_id'])){

            // }
            // dd($validated['sk_pengakuan_ypt_id']);

            $old_jfa = riwayatJabatanFungsionalAkademik::where('dosen_id', $validated['dosen_id'])
                ->whereNull('tmt_selesai')
                ->first();
            $oldesst = $old_jfa;
            $old_jfa?->update(['tmt_selesai' => now()]);
            // dd($old_jfa);
            riwayatJabatanFungsionalAkademik::create($validated);



            DB::commit();
            // dD($old_jfa,$oldesst);
            // dd('ypt',$validated['sk_pengakuan_ypt_id'],'dikti',$validated['sk_llkdikti_id']);
            // DD('DONE');
            // dd('done');
            return redirect(route('manage.jfa.list'))->with('success', 'JFA berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat JFA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id_jfa)
    {
        $jfa_data = riwayatJabatanFungsionalAkademik::find($id_jfa);
        // dd($jfa_data->dosen->pegawai);

        $dosens = Dosen::with('pegawai')
                ->get()
                ->sortBy('pegawai.nama_lengkap')
                ->values(); // reset index
                // dd($dosens);

        $jfas = refJabatanFungsionalAkademik::all()->sortBy('nama_jabatan')->values();
        $sk_diktis = SK::Sk_Dikti();
        $sk_ypts = SK::Sk_Ypt();
        return view('kelola_data.jfa.update', compact('jfa_data', 'dosens', 'jfas', 'sk_diktis', 'sk_ypts'));
    }

    public function update_data(Request $request, $id_jfa)
    {
        $validated = $request->validate([
            // Dosen & JFA
            'dosen_id'      => ['required'],
            'ref_jfa_id'    => ['required'],
            'tmt_mulai'     => ['required','date'],

            /* ===============================
            VALIDASI SK LLKDIKTI
            =============================== */
            // Jika pilih existing: harus ada sk_llkdikti_id
            'sk_llkdikti_id' => ['nullable', 'required_without_all:file_sk_dikti,no_sk_dikti'],

            // Jika input baru: file & nomor wajib bila tidak memilih existing
            'file_sk_dikti'  => ['nullable','file','mimes:pdf,png,jpg,jpeg','required_without:sk_llkdikti_id'],
            'no_sk_dikti'    => ['nullable','string','max:50','required_without:sk_llkdikti_id','required_with:file_sk_dikti',],

            /* ===============================
            VALIDASI SK YPT
            (Boleh kosong semua)
            =============================== */
            'sk_pengakuan_ypt_id' => ['nullable'],

            'file_sk_ypt'   => ['nullable','file','mimes:pdf,png,jpg,jpeg'],
            'no_sk_ypt'     => ['nullable','string','max:50','required_with:file_sk_ypt',],

        ], [

            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

            'required_without'     => ':attribute wajib diisi jika :values tidak ada.',
            'required_without_all' => ':attribute wajib diisi jika :values tidak ada semuanya.',

        ], [
            // rename attributes biar rapi
            'sk_llkdikti_id'        => 'SK LLKDIKTI',
            'file_sk_dikti'         => 'file SK LLKDIKTI',
            'no_sk_dikti'           => 'Nomor SK LLKDIKTI',

            'sk_pengakuan_ypt_id'   => 'SK YPT',
            'file_sk_ypt'           => 'file SK YPT',
            'no_sk_ypt'             => 'Nomor SK YPT',
        ]);

        // DD('MASUK');

        // DD(isset($validated['sk_llkdikti_id']));
        DB::beginTransaction();
        // // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {



            if($validated['no_sk_dikti']!=null){
                $validated['sk_llkdikti_id'] = null;
            }
            if((!isset($validated['sk_llkdikti_id']))){
                // dd('masuk');

                try {
                    $validated['no_sk'] = $validated['no_sk_dikti'];
                    $validated['tipe_sk'] = 'LLDIKTI';
                    // DB::commit();

                    $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                    $sk = SK::create($validated);
                    $validated['sk_llkdikti_id'] = $sk->id;

                } catch (\Exception $e) {
                    // DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat SK LLDIKTI',
                        'error' => $e->getMessage()
                    ], 500);
                }

            }
            // dd($isset_ypt);
            if(isset($validated['sk_pengakuan_ypt_id'])||isset($validated['no_sk_ypt'])){
                if($validated['no_sk_ypt']!=null){
                    $validated['sk_pengakuan_ypt_id'] = null;
                }
                if((!isset($validated['sk_pengakuan_ypt_id']))){
                    // dd('masuk');

                    try {
                        $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'Pengakuan YPT';
                        // DB::commit();

                        $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                        $sk = SK::create($validated);
                        $validated['sk_pengakuan_ypt_id'] = $sk->id;

                    } catch (\Exception $e) {
                        // DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal membuat SK LLDIKTI',
                            'error' => $e->getMessage()
                        ], 500);
                    }

                }
            }else{
                $validated['sk_pengakuan_ypt_id'] = null;
            }


            // if(!isset($validated['sk_pengakuan_ypt_id'])){

            // }
            // dd($validated['sk_pengakuan_ypt_id']);
            // riwayatJabatanFungsionalAkademik::create($validated);
            $jfa_update = riwayatJabatanFungsionalAkademik::findOrFail($id_jfa);
            $jfa_update->update($validated);

            DB::commit();
            // dd('ypt',$validated['sk_pengakuan_ypt_id'],'dikti',$validated['sk_llkdikti_id']);
            // DD('DONE');
            // dd('done');
            return redirect(route('manage.jfa.list'))->with('success', 'JFA berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal upgrade JFA',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
