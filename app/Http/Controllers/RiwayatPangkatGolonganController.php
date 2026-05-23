<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\RefPangkatGolongan;
use App\Models\RiwayatPangkatGolongan;
use App\Models\SK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Validators\ValidationException;

class RiwayatPangkatGolonganController extends Controller
{
    public function index()
    {
        $this->MakeLog('User Mangakses halaman list pangkat & golongan karyawan');
        $jpgs = RiwayatPangkatGolongan::all()->sortBy('dosen.pegawai.nama_lengkap');
        return view('kelola_data.pangkat-golongan.list', compact('jpgs'));
    }

    public function new()
    {
        $this->MakeLog('User Mangakses halaman tambah pangkat karyawan');
        $dosens = Dosen::with('pegawai')
            ->get()
            ->sortBy('pegawai.nama_lengkap')
            ->values(); // reset index
        // dd($dosens);

        $jpgs = RefPangkatGolongan::orderBy('pangkat', 'desc')->get();
        $sk_diktis = SK::Sk_Dikti()->sortBy('no_sk');
        $route =  view('kelola_data.pangkat-golongan.input', compact('dosens', 'jpgs', 'sk_diktis'));
        return $this->CekReview($route, '1N4', 'MELIHAT LIST DATA PANGKAT - GOLONGAN');
    }

    public function store(Request $request)
    {


        $this->MakeLog('User Submit Data Pangkat & Golongan Baru milik Pegawai');
        $validated = $request->validate($this->validation()[0],$this->validation()[1],$this->validation()[2]);
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

                    if (isset($validated['sk_llkdikti_id']) || isset($validated['no_sk'])) {
                        if ($validated['no_sk'] != null) {
                            $validated['sk_llkdikti_id'] = null;
                        }

                        if ((!isset($validated['sk_llkdikti_id']))) {
                            $validated['tipe_sk'] = 'LLDIKTI';
                            $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                            $validated['keterangan'] = 'Pangkat & Golongan Pegawai';
                            $validated['keperluan'] = 'Pangkat Golongan';

                            // $sk = SK::create($validated);
                            $response = (new SKController())->new(new Request($validated), $validated['tipe_sk'], false);
                            $sk_data = $response->getData();
                            // dd($sk_data);

                            if ($response->getStatusCode() != 200) {
                                throw new \Exception('Gagal save SK: ' . $sk_data->error);
                            }
                            $sk = $sk_data->data;
                            $validated['sk_llkdikti_id'] = $sk->id;
                        }
                    } else {
                        $validated['sk_llkdikti_id'] = null;
                    }
                }
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }

            RiwayatPangkatGolongan::create($validated);
            DB::commit();
            $route =  redirect(route('manage.pangkat-golongan.list'))->with('success', 'Pangkat & Golongan berhasil dibuat.');
            $this->MakeLog('User Berhasil Submit Data Pangkat & Golongan Baru milik Pegawai');
            return $this->CekReview($route, '1N1', 'MENAMBAH DATA PANGKAT - GOLONGAN');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->MakeLog('User Gagal Submit Data Pangkat & Golongan Baru milik Pegawai', ['alasan' => $e->getMessage()]);

            return $this->handleRedirectBack()
                ->withInput()
                ->withErrors(['error_alert' => $e->getMessage()]);
        }
    }

    public function update($id_pg)
    {
        try{
            $pg_data = RiwayatPangkatGolongan::findOrFail($id_pg);
        }catch(\Exception $e){
            return $this->handleRedirectBack()->with('error_alert','Data Riwayat Pangkat Golongan Tidak Ditemukan!.');
        }
        $dosens = Dosen::with('pegawai')
            ->get()
            ->sortBy('pegawai.nama_lengkap')
            ->values(); // reset index
        // dd($dosens);

        $jpgs = RefPangkatGolongan::orderBy('pangkat', 'desc')->get();

        $sk_diktis = SK::Sk_Dikti()->sortBy('no_sk');
        // dd($sk_diktis);
        return view('kelola_data.pangkat-golongan.update', compact('pg_data', 'dosens', 'jpgs', 'sk_diktis'));
    }

    public function update_data(Request $request, $id_pg)
    {
        $pg_update = null;
        try {
            $pg_update = RiwayatPangkatGolongan::findOrFail($id_pg);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->handleRedirectBack()->with('error_alert','Data Pangkat & Golongan Pegawai ini Tidak Ditemukan!.');
        }
        $validated = $request->validate($this->validation()[0],$this->validation()[1],$this->validation()[2]);

        DB::beginTransaction();

        $old = RiwayatPangkatGolongan::where('id', $id_pg)->first();
        // // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {
            if (isset($validated['sk_llkdikti_id']) || isset($validated['no_sk'])) {
                if ($validated['no_sk'] != null) {
                    $validated['sk_llkdikti_id'] = null;
                }

                if ((!isset($validated['sk_llkdikti_id']))) {
                    $validated['tipe_sk'] = 'LLKDIKTI';
                    $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                    $validated['keterangan'] = 'Pangkat & Golongan Pegawai';
                    $validated['keperluan'] = 'Pangkat Golongan';

                    // $sk = SK::create($validated);
                    $response = (new SKController())->new(new Request($validated), 'LLKDIKTI', false);
                    // dd($response);
                    $sk_data = $response->getData();
                    // dd($sk_data);

                    if ($response->getStatusCode() != 200) {
                        throw new \Exception('Gagal save SK: ' . $sk_data->error);
                    }
                    $sk = $sk_data->data;
                    $validated['sk_llkdikti_id'] = $sk->id;
                }
            } else {
                $validated['sk_llkdikti_id'] = null;
            }

            // RiwayatPangkatGolongan::create($validated);

            $save = $pg_update->update($validated);

            DB::commit();
            $route =  redirect(route('manage.pangkat-golongan.list'))->with('success', 'Pangkat & Golongan berhasil diupdate.');
            $this->MakeLog('User Berhasil Submit Ubah Data Pangkat & Golongan milik Pegawai', ['data_lama' => $old, 'data_baru' => $pg_update]);
            return $this->CekReview($route, '1N3', 'MENGUBAH DATA PANGKAT - GOLONGAN');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->MakeLog('User Gagal Submit Ubah Data Pangkat & Golongan milik Pegawai', ['alasan' => $e->getMessage()]);

            // return $this->handleRedirectBack()->with('error', 'Pangkat & Golongan berhasil diupdate.');
            return $this->handleRedirectBack()
                ->withInput()
                ->withErrors(['error_alert' => $e->getMessage()]);
        }
    }

    public function validation($id=null){
        $id = $id==null?'':','.$id;
        return [
        [
            // Dosen & JFA
            'dosen_id'      => ['required','exists:dosens,id'],
            'pangkat_golongan_id'    => ['required','exists:ref_pangkat_golongans,id'],
            'tmt_mulai'     => ['required', 'date'],
            'sk_llkdikti_id' => ['nullable','exists:sks,id'],
            'tipe_dokumen'     => ['nullable', 'string', 'max:50', 'required_with:file_sk','in:SK,AMANDEMEN'],


            'file_sk'   => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg'],
            'no_sk'     => ['nullable', 'string', 'max:50', 'required_with:file_sk'],

        ], [

            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',
            'exists' => ':attribute Tidak Terdaftar!.',
            'unique' => ':attribute Sudah Terdaftar!.',

        ], [

            'sk_llkdikti_id'   => 'SK LLDIKTI',
            'file_sk'           => 'file SK LLDIKTI',
            'no_sk'             => 'Nomor SK LLDIKTI',
            'dosen_id'          => 'Pilihan Dosen',
            'pangkat_golongan_id' => 'Pilihan Pangkat Golongan',
            'tipe_dokumen'      => 'Tipe Dokumen SK atau AMANDEMEN'
        ]
        ];
    }

    public function history($id_user){
        if ($this->onlyOwnerAdminAndSdm($id_user) == true) {
            $dosen = Dosen::where('users_id', $id_user)->first();
            if (! $dosen) {
                return $this->handleRedirectBack()->with('error_alert', 'Data Dosen Tidak Ditemukan!.');
            }
            $user = (new ProfileController)->based_user_data($id_user);
            $history = RiwayatPangkatGolongan::with([
                'skLlDikti',
                'dosen',
                'refPangkatGolongan'
                ])
                ->where('dosen_id', $dosen->id)
                ->get()
                ->map(function ($item) {
                    $item->is_active = is_null($item->tmt_selesai)
                        || $item->tmt_selesai >= today();

                    return $item;
                })
                ->sortByDesc('created_at');
            // dd($history);
            $this->MakeLog('User Berhasil Mengakses halaman Riwayat Pangkat Golongan dari Dosen Terkait', ['dosen terkait' => $user->nama_lengkap]);

            $route = view('kelola_data.pegawai.view.history.pangkat-golongan', compact('user', 'history'));
            return $route;
        }

        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');
    }
}
