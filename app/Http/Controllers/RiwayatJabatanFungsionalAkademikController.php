<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\RefJabatanFungsionalAkademik;
use App\Models\RiwayatJabatanFungsionalAkademik;
use App\Models\SK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatJabatanFungsionalAkademikController extends Controller
{
    public function index()
    {
        $jfas = riwayatJabatanFungsionalAkademik::with(['jfa', 'dosen.pegawai', 'sk_dikti', 'sk_ypt'])->get();

        // dd($jfas);
        $route = view('kelola_data.jfa.list', compact('jfas'));

        return $this->CekReview($route, '1O4', 'MELIHAT LIST DATA ENTRY LEVEL- DOSEN', true);

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

    public function store_data(Request $request)
    {
        // DD($request);
        $validated = $request->validate($this->validation()[0], $this->validation()[1], $this->validation()[2]);
        // dd($validated);
        // dd($validated);
        try {
            DB::beginTransaction();
            $cek_exist = RiwayatJabatanFungsionalAkademik::where('dosen_id', $request->dosen_id)->where('ref_jfa_id', $request->ref_jfa_id)->first();
            if ($cek_exist) {
                throw new \Exception('Jabatan Fungsional Dosen ini dengan Jabatan ini sudah terdaftar!.');
            }

            if ($validated['no_sk_lldikti'] != null) {
                $validated['sk_llkdikti_id'] = null;
            }

            if ($validated['sk_llkdikti_id'] == null) {
                $save_sk_llkdikti = [];
                $save_sk_llkdikti['tipe_sk'] = 'LLDIKTI';
                $save_sk_llkdikti['keperluan'] = 'Jabatan Fungsional Akademik ';
                $save_sk_llkdikti['file_sk'] = $request->file('file_sk_lldikti');
                $save_sk_llkdikti['no_sk'] = $request->no_sk_lldikti;
                $save_sk_llkdikti['keterangan'] = $request->keterangan_sk_lldikti;
                $save_sk_llkdikti['tipe_dokumen'] = $request->tipe_dokumen_sk_lldikti;
                $save_sk_llkdikti['tmt_mulai'] = $request->tmt_mulai;
                $save_sk_llkdikti['tmt_selesai'] = $request->tmt_selesai;
                // dd($save_sk_llkdikti);
                $response = (new SKController)->new(new Request($save_sk_llkdikti), false, false);
                $sk_data = $response->getData();
                // dd($sk_data);

                if ($response->getStatusCode() != 200) {
                    throw new \Exception('Gagal save SK DIKTI: '.$sk_data->error);
                }
                $sk = $sk_data->data;
                $validated['sk_llkdikti_id'] = $sk->id;
            }

            if (((isset($validated['file_sk_ypt']) && $validated['file_sk_ypt'] != null) || (isset($validated['no_sk_ypt']) && $validated['no_sk_ypt'] != null)) || (isset($validated['sk_pengakuan_ypt_id']) && $validated['sk_pengakuan_ypt_id'] != null)) {
                if ($validated['no_sk_ypt'] != null) {
                    $validated['sk_pengakuan_ypt_id'] = null;
                }
                if ($validated['sk_pengakuan_ypt_id'] == null) {
                    $save_sk_ypt = [];
                    $save_sk_ypt['tipe_sk'] = 'Pengakuan YPT';
                    $save_sk_ypt['keperluan'] = 'Jabatan Fungsional Akademik ';
                    $save_sk_ypt['file_sk'] = $request->file('file_sk_ypt');
                    $save_sk_ypt['no_sk'] = $request->no_sk_ypt;

                    $save_sk_ypt['keterangan'] = $request->keterangan_sk_ypt;
                    $save_sk_ypt['tipe_dokumen'] = $request->tipe_dokumen_sk_ypt;
                    $save_sk_ypt['tmt_mulai'] = $request->tmt_mulai;
                    $save_sk_ypt['tmt_selesai'] = $request->tmt_selesai;
                    // dd($validated);
                    $response = (new SKController)->new(new Request($save_sk_ypt), false, false);
                    $sk_data = $response->getData();
                    // dd($sk_data);

                    if ($response->getStatusCode() != 200) {
                        throw new \Exception('Gagal save SK YPT: '.$sk_data->error);
                    }
                    $sk = $sk_data->data;
                    $validated['sk_pengakuan_ypt_id'] = $sk->id;
                }
            }

            $save = RiwayatJabatanFungsionalAkademik::create($validated);
            if (! $save) {

                throw new \Exception('Gagal menyimpan data!.');
            }
            DB::commit();

            $route = redirect(route('manage.jfa.list'))->with('success', 'Berhasil menyimpan data!.');

            return $this->CekReview($route, '1O1', 'MENAMBAH DATA ENTRY LEVEL- DOSEN');

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->handleRedirectBack()->withInput($request->all())->with('error_alert', $e->getMessage());
        }

    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validation()[0], $this->validation()[1], $this->validation()[2]);

        try {
            DB::beginTransaction();

            if ($validated['no_sk_lldikti'] != null) {
                $validated['sk_llkdikti_id'] = null;
            }
            if ((!isset($validated['sk_llkdikti_id']))) {
                // dd('masuk');

                try {
                    $validated['no_sk'] = $validated['no_sk_lldikti'];
                    $validated['tipe_sk'] = 'LLDIKTI';
                    // DB::commit();

                    $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                    $validated['keterangan'] = 'Jabatan Fungsional Pegawai';
                    $validated['keperluan'] = 'JFP';

                    $sk = SK::create($validated);
                    $validated['sk_llkdikti_id'] = $sk->id;
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $f) {
                    throw new \Exception('Terjadi masalah saat menyimpan SK LLKDIKTI!.');
                }
            }
            // dd($isset_ypt);
            if (isset($validated['sk_pengakuan_ypt_id']) || isset($validated['no_sk_ypt'])) {
                if ($validated['no_sk_ypt'] != null) {
                    $validated['sk_pengakuan_ypt_id'] = null;
                }
                if ((! isset($validated['sk_pengakuan_ypt_id']))) {
                    // dd('masuk');

                    try {
                        $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'Pengakuan YPT';
                        // DB::commit();

                        $validated['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                        $validated['keterangan'] = 'Jabatan Fungsional Pegawai';
                        $validated['keperluan'] = 'JFA';

                        $sk = SK::create($validated);
                        $response = (new SKController())->new(new Request($validated), 'Ypt', false);

                        $validated['sk_pengakuan_ypt_id'] = $sk->id;
                    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $f) {
                        throw new \Exception('Terjadi masalah saat menyimpan SK YPT!.');
                    }

                }
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }

            // if(!isset($validated['sk_pengakuan_ypt_id'])){

            // }
            // dd($validated['sk_pengakuan_ypt_id']);
            $old_jfa = riwayatJabatanFungsionalAkademik::where('dosen_id', $validated['dosen_id'])
                ->where(function ($q) {
                    $q->whereNull('tmt_selesai')
                    ->orWhere('tmt_selesai', '>=', now());
                })
                ->first();
            $oldesst = $old_jfa;
            $old_jfa?->update(['tmt_selesai' => now()]);
            // dd($old_jfa);
            riwayatJabatanFungsionalAkademik::create($validated);

            DB::commit();
            $route = redirect(route('manage.jfa.list'))->with('success', 'JFA berhasil dibuat.');

            return $this->CekReview($route, '1O1', 'MENAMBAH DATA ENTRY LEVEL- DOSEN');

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->handleRedirectBack()->withInput($request->all())->with('error_alert', $e->getMessage());
        }
    }

    public function update($id_jfa)
    {
        $jfa_data = riwayatJabatanFungsionalAkademik::find($id_jfa);
        if (! $jfa_data) {
            return $this->handleRedirectBack()->with('error_alert', 'Data JFA Tidak Ditemukan!.');
        }

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
        $jfa_update = null;
        try {
            $jfa_update = riwayatJabatanFungsionalAkademik::findOrFail($id_jfa);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->MakeLog('Mencoba Mengubah Data JFA');

            return $this->handleRedirectBack()->with('Riwayat Jabatan Fungsional Akademik (JFA) ini tidak terdaftar!.');
        }

        $validated = $request->validate($this->validation()[0], $this->validation()[1], $this->validation()[2]);
        // dd($validated);
        try {
            DB::beginTransaction();
            // dd(isset($validated['no_sk_lldikti']),$validated['no_sk_lldikti'] != null,isset($validated['sk_llkdikti_id']),$validated['sk_llkdikti_id'] == null);
            if (isset($validated['no_sk_lldikti']) && $validated['no_sk_lldikti'] != null) {
                $validated['sk_llkdikti_id'] = null;
                dump('masuk1');
                }
                dump('masuk12', $validated['sk_llkdikti_id'],isset($validated['sk_llkdikti_id']));

            if ((!isset($validated['sk_llkdikti_id']) && $validated['sk_llkdikti_id'] == null)) {
                dump('masuk2');

                // try {
                    $Sk_dikti['no_sk'] =$validated['no_sk_lldikti'];
                    $Sk_dikti['tipe_sk'] = 'LLDIKTI';
                    $Sk_dikti['tipe_dokumen'] = $validated['tipe_dokumen_sk_lldikti'];
                    $Sk_dikti['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                    $Sk_dikti['keterangan'] = 'Jabatan Fungsional Pegawai';
                    $Sk_dikti['keperluan'] = 'JFA';
                    $Sk_dikti['file_sk'] = $validated['file_sk_lldikti'];
                    $Sk_dikti['tmt_mulai'] = $validated['tmt_mulai'];


                    $response = (new SKController)->new(new Request($Sk_dikti), 'LLDIKTI', false);
                    $sk_data = $response->getData();
                    // dd($sk_data);
                    //tes

                    if ($response->getStatusCode() != 200) {
                        throw new \Exception('Gagal save SK: '.$sk_data->error);
                    }
                    $sk_llkdikti = $sk_data->data;
                    // dd($sk_llkdikti);
                    $validated['sk_llkdikti_id'] = $sk_llkdikti->id;
                // } catch (\Exception $f) {
                // dump('masuk2k');

                //     return redirect()->back()->withInput($validated)->with('error_alert', $f->getMessage());
                // }
            }

            if (isset($validated['sk_pengakuan_ypt_id']) || isset($validated['no_sk_ypt'])) {
                if (isset($validated['no_sk_ypt']) && $validated['no_sk_ypt'] != null) {
                    $validated['sk_pengakuan_ypt_id'] = null;
                }
                if ((!isset($validated['sk_pengakuan_ypt_id']))) {
                    // try {
                        $Sk_Ypt['no_sk'] = $validated['no_sk_ypt'];
                        $Sk_Ypt['tipe_sk'] = 'Pengakuan YPT';
                        $Sk_Ypt['users_id'] = Dosen::find($validated['dosen_id'])->users_id;
                        $Sk_Ypt['keterangan'] = 'Jabatan Fungsional Pegawai';
                        $Sk_Ypt['keperluan'] = 'JFA';
                        $Sk_Ypt['tipe_dokumen'] = $validated['tipe_dokumen_sk_ypt'];

                        $Sk_Ypt['tmt_mulai'] = $validated['tmt_mulai'];
                        $Sk_Ypt['file_sk'] = $validated['file_sk_ypt'];


                        $response = (new SKController)->new(new Request($Sk_Ypt), 'Ypt', false);
                $sk_data = $response->getData();
                // dd($sk_data);

                if ($response->getStatusCode() != 200) {
                    throw new \Exception('Gagal save SK: '.$sk_data->error);
                }
                $sk_ypt = $sk_data->data;
                        $validated['sk_pengakuan_ypt_id'] = $sk_ypt->id;
                    // } catch (\Exception $t) {
                    //     return redirect()->back()->withInput($request->all())->with('error_alert', $t->getMessage());
                    // }
                }
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }
            // dump($validated['sk_pengakuan_ypt_id']);


            $jfa_update->update($validated);

            DB::commit();
            // dd($jfa_update, $sk_llkdikti);
            $route = redirect(route('manage.jfa.list'))->with('success', 'JFA berhasil diupdate.');

            return $this->CekReview($route, '1O3', 'MENGUBAH DATA ENTRY LEVEL- DOSEN');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->MakeLog('Gagal mengubah data',['alasan' => $e->getMessage()]);
            return redirect()->back()->withInput($validated)->with('error_alert', $e->getMessage());
        }
    }

    public function validation()
    {
        return [
            [
                'dosen_id' => ['required', 'exists:dosens,id'],
                'ref_jfa_id' => ['required', 'exists:ref_jabatan_fungsional_akademiks,id'],
                'tmt_mulai' => 'required|date',
                'tmt_selesai' => 'nullable|date|after_or_equal:tmt_mulai',

                // Validasi SK LLDIKTI (WAJIB: harus ada file baru ATAU id existing)
                'sk_llkdikti_id' => 'required_without:file_sk_lldikti|nullable|exists:sks,id',
                'file_sk_lldikti' => 'required_without:sk_llkdikti_id|nullable|file|mimes:pdf|max:2048',
                'keterangan_sk_lldikti' => 'required_without:sk_llkdikti_id|nullable|string|max:200',
                'tipe_dokumen_sk_lldikti' => 'required_without:sk_llkdikti_id|nullable|string|in:SK,AMANDEMEN|max:100',
                'no_sk_lldikti' => 'required_with:file_sk_lldikti|nullable|string|max:100|unique:sks,no_sk',

                // Validasi SK YPT (OPSIONAL: tapi jika salah satu diisi, pasangannya harus valid)
                'sk_pengakuan_ypt_id' => 'nullable|exists:sks,id',
                'file_sk_ypt' => 'nullable|file|mimes:pdf|max:2048',
                'keterangan_sk_ypt' => 'required_with:file_sk_ypt|nullable|string|max:200',
                'tipe_dokumen_sk_ypt' => 'required_with:file_sk_ypt|nullable|string|in:SK,AMANDEMEN|max:100',
                'no_sk_ypt' => 'required_with:file_sk_ypt|nullable|string|max:100|unique:sks,no_sk',
            ], [
                'required' => ':attribute Wajib Diisi',
                // Custom Error Messages
                'sk_llkdikti_id.required_without' => 'Pilih SK LLDIKTI yang tersedia atau upload file baru.',
                'file_sk_lldikti.required_without' => 'File SK LLDIKTI wajib diunggah jika tidak memilih SK yang sudah ada.',
                'no_sk_lldikti.required_with' => 'Nomor SK LLDIKTI wajib diisi untuk file yang diupload.',
                'no_sk_ypt.required_with' => 'Nomor SK YPT wajib diisi jika Anda mengupload file SK YPT baru.',
                'exists' => ':attribute Tidak Ditemukan!.',
            ], [
                'dosen_id' => 'Dosen',
                'ref_jfa_id' => 'Jabatan Fungsional Akademik',
                'tmt_mulai' => 'Terakui Mulai Tanggal',
                'tmt_selesai' => 'Akhir Tanggal Selesai',
                'sk_llkdikti_id' => 'Pilihan SK LLKDIKTI',
                'file_sk_lldikti' => 'Dokumen File SK LLKDIKTI Baru',
                'no_sk_lldikti' => 'Nomor SK LLKDIKTI Baru',
                'sk_pengakuan_ypt_id' => 'Pilihan SK YPT',
                'file_sk_ypt' => 'Dokumen File SK YPT Baru',
                'no_sk_ypt' => 'Nomor SK YPT Baru',
            ],
        ];
    }

    public function history($id_user){
        if ($this->onlyOwnerAdminAndSdm($id_user) == true) {
            $dosen = Dosen::where('users_id', $id_user)->first();
            if (! $dosen) {
                return $this->handleRedirectBack()->with('error_alert', 'Data Dosen Tidak Ditemukan!.');
            }
            $user = (new ProfileController)->based_user_data($id_user);
            $history = RiwayatJabatanFungsionalAkademik::with([
                'sk_dikti',
                'sk_ypt',
                'dosen',
                'jfa'
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
            $this->MakeLog('User Berhasil Mengakses halaman Riwayat JFA dari Dosen Terkait', ['dosen terkait' => $user->nama_lengkap]);

            $route = view('kelola_data.pegawai.view.history.jfa', compact('user', 'history'));
            return $route;
        }

        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');
    }
}
