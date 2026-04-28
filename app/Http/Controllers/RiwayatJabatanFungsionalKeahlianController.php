<?php

namespace App\Http\Controllers;

use App\Models\refJabatanFungsionalKeahlian;
use App\Models\riwayatJabatanFungsionalKeahlian;
use App\Models\SK;
use App\Models\Tpa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatJabatanFungsionalKeahlianController extends Controller
{
    public function index()
    {
        $jfks = riwayatJabatanFungsionalKeahlian::all();

        return view('kelola_data.jfk.list', compact('jfks'));
    }
    public function new()
    {
        $jfks = refJabatanFungsionalKeahlian::all()->sortBy('nama_jfk')->values();
        $tpas = Tpa::with('pegawai')->get()->sortBy('pegawai.nama_lengkap')->values();
        $sk_ypts = SK::all()->sortBy('nomor_sk')->values();

        return view('kelola_data.jfk.input', compact('jfks', 'tpas', 'sk_ypts'));
    }

    public function update($id_jfk)
    {
        $jfk_data = riwayatJabatanFungsionalKeahlian::findOrFail($id_jfk);
        $jfks = refJabatanFungsionalKeahlian::all()->sortBy('nama_jfk')->values();
        $tpas = Tpa::with('pegawai')->get()->sortBy('pegawai.nama_lengkap')->values();
        $sk_ypts = SK::all()->sortBy('nomor_sk')->values();

        return view('kelola_data.jfk.update', compact('jfk_data', 'jfks', 'tpas', 'sk_ypts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Dosen & JFA
            'tpa_id'      => ['required'],
            'ref_jfk_id'    => ['required'],
            'tmt_mulai'     => ['required', 'date'],

            'sk_pengakuan_ypt_id' => ['nullable'],

            'file_sk_ypt'   => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg'],
            'no_sk_ypt'     => ['nullable', 'string', 'max:50', 'required_with:file_sk_ypt',],

        ], [

            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

            'required_without'     => ':attribute wajib diisi jika :values tidak ada.',
            'required_without_all' => ':attribute wajib diisi jika :values tidak ada semuanya.',

        ], [

            'sk_pengakuan_ypt_id'   => 'SK YPT',
            'file_sk_ypt'           => 'file SK YPT',
            'no_sk_ypt'             => 'Nomor SK YPT',
        ]);

        // DD('MASUK');

        // DD(isset($validated['sk_llkdikti_id']));
        DB::beginTransaction();
        // // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {


            // dd($isset_ypt);
            if (isset($validated['sk_pengakuan_ypt_id']) || isset($validated['no_sk_ypt'])) {
                if ($validated['no_sk_ypt'] != null) {
                    $validated['sk_pengakuan_ypt_id'] = null;
                }
                if ((!isset($validated['sk_pengakuan_ypt_id']))) {
                    // dd('masuk');

                    try {
                        $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'Pengakuan YPT';
                        // DB::commit();

                        $validated['users_id'] = Tpa::find($validated['tpa_id'])->users_id;
                        $validated['keterangan'] = 'Jabatan Fungsional Pegawai';
                        $validated['keperluan'] = 'JFK';

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
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }


            // if(!isset($validated['sk_pengakuan_ypt_id'])){

            // }
            // dd($validated['sk_pengakuan_ypt_id']);

            $old_jfk = riwayatJabatanFungsionalKeahlian::where('tpa_id', $validated['tpa_id'])
                ->whereNull('tmt_selesai')
                ->first();
            $oldesst = $old_jfk;
            $old_jfk?->update(['tmt_selesai' => now()]);
            // dd($old_jfk);
            riwayatJabatanFungsionalKeahlian::create($validated);



            DB::commit();
            // dD($old_jfa,$oldesst);
            // dd('ypt',$validated['sk_pengakuan_ypt_id'],'dikti',$validated['sk_llkdikti_id']);
            // DD('DONE');
            // dd('done');
            return redirect(route('manage.jfk.list'))->with('success', 'JFK berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat JFK',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function isi_sk_ypt(Request $request, $id_jfk)
    {
        // $id_user = SK::with("user_data")->where('id', $id_sk)->first();
        $sk_ypt = (new SKController())->new($request, 'YPT', 'fromRiwayatJabatanFungsionalKeahlian');
        $jfk_update = riwayatJabatanFungsionalKeahlian::findOrFail($id_jfk);
        $jfk_update?->update(['sk_pengakuan_ypt_id' => $sk_ypt]);

        return redirect()->back()->with('success', 'Surat Keputusan Pengakuan YPT Untuk Jabatan Fungsional Keahlian karyawan berhasil ditambahkan');
        // dd($validated['sk_pengakuan_ypt_id']);
        // dd($id_user);
        // dd(riwayatJabatanFungsionalKeahlian::where());
    }


    public function update_data(Request $request, $id_jfk)
    {
        $validated = $request->validate([
            // Dosen & JFA
            'tpa_id'      => ['required'],
            'ref_jfk_id'    => ['required'],
            'tmt_mulai'     => ['required', 'date'],

            'sk_pengakuan_ypt_id' => ['nullable'],

            'file_sk_ypt'   => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg'],
            'no_sk_ypt'     => ['nullable', 'string', 'max:50', 'required_with:file_sk_ypt',],

        ], [

            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

            'required_without'     => ':attribute wajib diisi jika :values tidak ada.',
            'required_without_all' => ':attribute wajib diisi jika :values tidak ada semuanya.',

        ], [

            'sk_pengakuan_ypt_id'   => 'SK YPT',
            'file_sk_ypt'           => 'file SK YPT',
            'no_sk_ypt'             => 'Nomor SK YPT',
        ]);

        // DD('MASUK');

        // DD(isset($validated['sk_llkdikti_id']));
        DB::beginTransaction();
        // // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {


            // dd($isset_ypt);
            if (isset($validated['sk_pengakuan_ypt_id']) || isset($validated['no_sk_ypt'])) {
                if ($validated['no_sk_ypt'] != null) {
                    $validated['sk_pengakuan_ypt_id'] = null;
                }
                if ((!isset($validated['sk_pengakuan_ypt_id']))) {
                    // dd('masuk');

                    try {
                        $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'Pengakuan YPT';
                        // DB::commit();

                        $validated['users_id'] = Tpa::find($validated['tpa_id'])->users_id;
                        $validated['keterangan'] = 'Jabatan Fungsional Pegawai';
                        $validated['keperluan'] = 'JFK';

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
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }


            // if(!isset($validated['sk_pengakuan_ypt_id'])){

            // }
            // dd($validated['sk_pengakuan_ypt_id']);

            // $old_jfk = riwayatJabatanFungsionalKeahlian::where('id', $id_jfk)
            //     ->whereNull('tmt_selesai')
            //     ->first();
            // $oldesst = $old_jfk;
            // $old_jfk?->update(['tmt_selesai' => now()]);
            // dd($old_jfk);
            // riwayatJabatanFungsionalKeahlian::create($validated);
            $jfk_update = riwayatJabatanFungsionalKeahlian::findOrFail($id_jfk);
            $jfk_update->update($validated);



            DB::commit();
            // dD($old_jfa,$oldesst);
            // dd('ypt',$validated['sk_pengakuan_ypt_id'],'dikti',$validated['sk_llkdikti_id']);
            // DD('DONE');
            // dd('done');
            return redirect(route('manage.jfk.list'))->with('success', 'JFK berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat JFK',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
