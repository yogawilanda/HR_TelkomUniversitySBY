<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\RefPangkatGolongan;
use App\Models\riwayatPangkatGolongan;
use App\Models\SK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatPangkatGolonganController extends Controller
{
    public function index()
    {
        $jpgs = riwayatPangkatGolongan::all()->sortBy('dosen.pegawai.nama_lengkap');
        return view('kelola_data.pangkat-golongan.list', compact('jpgs'));
    }

    public function new()
    {

        $dosens = Dosen::with('pegawai')
            ->get()
            ->sortBy('pegawai.nama_lengkap')
            ->values(); // reset index
        // dd($dosens);

        $jpgs = RefPangkatGolongan::orderBy('pangkat', 'desc')->get();

        $sk_diktis = SK::Sk_Dikti()->sortBy('no_sk');
        return view('kelola_data.pangkat-golongan.input', compact('dosens', 'jpgs', 'sk_diktis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Dosen & JFA
            'dosen_id'      => ['required'],
            'pangkat_golongan_id'    => ['required'],
            'tmt_mulai'     => ['required', 'date'],

            'sk_llkdikti_id' => ['nullable'],

            'file_sk'   => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg'],
            'no_sk'     => ['nullable', 'string', 'max:50', 'required_with:file_sk',],

        ], [

            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

        ], [

            'sk_llkdikti_id'   => 'SK LLDIKTI',
            'file_sk'           => 'file SK LLDIKTI',
            'no_sk'             => 'Nomor SK LLDIKTI',
        ]);

        // DD('MASUK');

        // DD(isset($validated['sk_llkdikti_id']));
        DB::beginTransaction();
        // // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {


            // dd($isset_ypt);
            if (isset($validated['sk_llkdikti_id']) || isset($validated['no_sk'])) {
                if ($validated['no_sk'] != null) {
                    $validated['sk_llkdikti_id'] = null;
                }
                if ((!isset($validated['sk_llkdikti_id']))) {
                    // dd('masuk');

                    try {
                        // $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'LLKDIKTI';
                        // DB::commit();

                        $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                        $validated['keterangan'] = 'Pangkat & Golongan Pegawai';
                        $validated['keperluan'] = 'Pangkat Golongan';

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
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }


            // if(!isset($validated['sk_pengakuan_ypt_id'])){

            // }
            // dd($validated['sk_pengakuan_ypt_id']);


            riwayatPangkatGolongan::create($validated);



            DB::commit();
            // dD($old_jfa,$oldesst);
            // dd('ypt',$validated['sk_pengakuan_ypt_id'],'dikti',$validated['sk_llkdikti_id']);
            // DD('DONE');
            // dd('done');
            return redirect(route('manage.pangkat-golongan.list'))->with('success', 'Pangkat & Golongan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Pangkat & Golongan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id_pg)
    {
        $pg_data = riwayatPangkatGolongan::find($id_pg);
        $dosens = Dosen::with('pegawai')
            ->get()
            ->sortBy('pegawai.nama_lengkap')
            ->values(); // reset index
        // dd($dosens);

        $jpgs = RefPangkatGolongan::orderBy('pangkat', 'desc')->get();

        $sk_diktis = SK::Sk_Dikti()->sortBy('no_sk');
        return view('kelola_data.pangkat-golongan.update', compact('pg_data', 'dosens', 'jpgs', 'sk_diktis'));
    }

    public function update_data(Request $request, $id_pg)
    {
        $validated = $request->validate([
            // Dosen & JFA
            'dosen_id'      => ['required'],
            'pangkat_golongan_id'    => ['required'],
            'tmt_mulai'     => ['required', 'date'],

            'sk_llkdikti_id' => ['nullable'],

            'file_sk'   => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg'],
            'no_sk'     => ['nullable', 'string', 'max:50', 'required_with:file_sk',],

        ], [

            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

        ], [

            'sk_llkdikti_id'   => 'SK LLDIKTI',
            'file_sk'           => 'file SK LLDIKTI',
            'no_sk'             => 'Nomor SK LLDIKTI',
        ]);

        // DD('MASUK');

        // DD(isset($validated['sk_llkdikti_id']));
        DB::beginTransaction();
        // // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {
            if (isset($validated['sk_llkdikti_id']) || isset($validated['no_sk'])) {
                if ($validated['no_sk'] != null) {
                    $validated['sk_llkdikti_id'] = null;
                }
                if ((!isset($validated['sk_llkdikti_id']))) {
                    // dd('masuk');

                    try {
                        // $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'LLKDIKTI';
                        // DB::commit();

                        $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                        $validated['keterangan'] = 'Pangkat & Golongan Pegawai';
                        $validated['keperluan'] = 'Pangkat Golongan';

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
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }


            // if(!isset($validated['sk_pengakuan_ypt_id'])){

            // }
            // dd($validated['sk_pengakuan_ypt_id']);


            // riwayatPangkatGolongan::create($validated);
            $jfa_update = riwayatPangkatGolongan::findOrFail($id_pg);
            $jfa_update->update($validated);



            DB::commit();
            // dD($old_jfa,$oldesst);
            // dd('ypt',$validated['sk_pengakuan_ypt_id'],'dikti',$validated['sk_llkdikti_id']);
            // DD('DONE');
            // dd('done');
            return redirect(route('manage.pangkat-golongan.list'))->with('success', 'Pangkat & Golongan berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Pangkat & Golongan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
