<?php

namespace App\Http\Controllers;

use App\Models\RefJabatanFungsionalKeahlian;
use App\Models\RiwayatJabatanFungsionalKeahlian;
use App\Models\SK;
use App\Models\Tpa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatJabatanFungsionalKeahlianController extends Controller
{
    public function index()
    {
        $today = Carbon ::today();
        $jfks = riwayatJabatanFungsionalKeahlian::with('data_jfk', 'data_tpa', 'sk_ypt')
            ->get()
            ->map(function ($item) use ($today) {
                // Logika check aktif
                $item->is_active = (is_null($item->tmt_selesai) || Carbon::parse($item->tmt_selesai)->greaterThanOrEqualTo($today)) ? 1 : 0;

                return $item;
            });

        $route = view('kelola_data.jfk.list', compact('jfks'));
        return $route;
    }

    public function new()
    {
        $jfks = RefJabatanFungsionalKeahlian::all()->sortBy('nama_jfk')->values();
        $tpas = Tpa::with('pegawai')->get()->sortBy('pegawai.nama_lengkap')->values();
        $sk_ypts = SK::all()->sortBy('nomor_sk')->values();

        $route = view('kelola_data.jfk.input', compact('jfks', 'tpas', 'sk_ypts'));
        return $this->CekReview($route, '1M4', 'MELIHAT LIST DATA ENTRY LEVEL- TPA', true);

    }

    public function update($id_jfk)
    {
        try {

            $jfk_data = null;

            try {
                $jfk_data = RiwayatJabatanFungsionalKeahlian::findOrFail($id_jfk);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Riwayat Jabatan Fungsional Keahlian (JFK) ini tidak terdaftar!.');
            }

            $jfks = RefJabatanFungsionalKeahlian::all()->sortBy('nama_jfk')->values();
            $tpas = Tpa::with('pegawai')->get()->sortBy('pegawai.nama_lengkap')->values();
            $sk_ypts = SK::all()->sortBy('nomor_sk')->values();

            $route = view('kelola_data.jfk.update', compact('jfk_data', 'jfks', 'tpas', 'sk_ypts'));
            return $this->CekReview($route, '1M4', 'MELIHAT LIST DATA ENTRY LEVEL- TPA');

        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validation()[0], $this->validation()[1], $this->validation()[2]);

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
                if ((! isset($validated['sk_pengakuan_ypt_id']))) {
                    // dd('masuk');

                    try {
                        $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'Pengakuan YPT';
                        // DB::commit();

                        $validated['users_id'] = Tpa::find($validated['tpa_id'])->users_id;
                        $validated['keterangan'] = 'Jabatan Fungsional Pegawai';
                        $validated['keperluan'] = 'JFK';
                        $validated['file_sk'] = $validated['file_sk_ypt'];

                        $response = (new SKController())->new(new Request($validated), 'Ypt', false);
                        // dump($response);
                                                $sk_data = $response->getData();
                        // dd($sk_data);

                        if ($response->getStatusCode() != 200) {
                            throw new \Exception('Gagal save SK: ' . $sk_data->error);
                        }
                        $sk = $sk_data->data;
                        $validated['sk_pengakuan_ypt_id'] = $sk->id;
                    } catch (\Exception $e) {
                        // DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal membuat SK LLDIKTI',
                            'error' => $e->getMessage(),
                        ], 500);
                    }
                }
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }

            $old_jfk = RiwayatJabatanFungsionalKeahlian::where('tpa_id', $validated['tpa_id'])
                ->whereNull('tmt_selesai')
                ->first();
            $oldesst = $old_jfk;
            $old_jfk?->update(['tmt_selesai' => now()]);
            // dd($old_jfk);
            $new = riwayatJabatanFungsionalKeahlian::create($validated);

            DB::commit();
            // dd($old_jfk, $new);

            $route = redirect(route('manage.jfk.list'))->with('success', 'JFK berhasil dibuat.');
        return $this->CekReview($route, '1M1', 'MENAMBAH DATA ENTRY LEVEL- TPA');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput($validated)->with('error_alert', 'Gagal Menyimpan data, '.$e->getMessage());
        }
    }

    public function isi_sk_ypt(Request $request, $id_jfk)
    {
        try {

            // $id_user = SK::with("user_data")->where('id', $id_sk)->first();
            $sk_ypt = (new SKController)->new($request, 'YPT', 'fromRiwayatJabatanFungsionalKeahlian');

            $jfk_update = null;
            try {
                $jfk_update = RiwayatJabatanFungsionalKeahlian::findOrFail($id_jfk);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Riwayat Jabatan Fungsional Keahlian (JFK) ini tidak terdaftar!.');
            }

            $jfk_update?->update(['sk_pengakuan_ypt_id' => $sk_ypt]);

            return $this->handleRedirectBack()->with('success', 'Surat Keputusan Pengakuan YPT Untuk Jabatan Fungsional Keahlian karyawan berhasil ditambahkan');
        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function update_data(Request $request, $id_jfk)
    {
        $validated = $request->validate($this->validation()[0], $this->validation()[1], $this->validation()[2]);

        // DD('MASUK');

        // DD(isset($validated['sk_llkdikti_id']));
        DB::beginTransaction();
        // // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {

            // dd($isset_ypt);
            // dd('tes',isset($validated['sk_pengakuan_ypt_id']), isset($validated['no_sk_ypt']), (isset($validated['sk_pengakuan_ypt_id']) && $validated['sk_pengakuan_ypt_id'] == null));
            if (isset($validated['sk_pengakuan_ypt_id']) || isset($validated['no_sk_ypt'])) {
                if ($validated['no_sk_ypt'] != null) {
                    $validated['sk_pengakuan_ypt_id'] = null;
                }
                if ((isset($validated['sk_pengakuan_ypt_id']) && $validated['sk_pengakuan_ypt_id'] == null)) {
                    // dd('masuk');

                    try {
                        $validated['no_sk'] = $validated['no_sk_ypt'];
                        $validated['tipe_sk'] = 'Pengakuan YPT';
                        // DB::commit();

                        $validated['users_id'] = Tpa::find($validated['tpa_id'])->users_id;
                        $validated['keterangan'] = 'Jabatan Fungsional Pegawai';
                        $validated['keperluan'] = 'JFK';
                        $validated['file_sk'] = $validated['file_sk_ypt'];

                        $response = (new SKController())->new(new Request($validated), 'Ypt', false);
                        $sk_data = $response->getData();
                        // dd($sk_data);

                        if ($response->getStatusCode() != 200) {
                            throw new \Exception('Gagal save SK: ' . $sk_data->error);
                        }
                        $sk = $sk_data->data;
                        $validated['sk_pengakuan_ypt_id'] = $sk->id;
                    } catch (\Exception $e) {
                        // DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal membuat SK LLDIKTI',
                            'error' => $e->getMessage(),
                        ], 500);
                    }
                }
            } else {
                $validated['sk_pengakuan_ypt_id'] = null;
            }

            $jfk_update = null;
            try {
                $jfk_update = RiwayatJabatanFungsionalKeahlian::findOrFail($id_jfk);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Riwayat Jabatan Fungsional Keahlian (JFK) ini tidak terdaftar!.');
            }
            $jfk_update->update($validated);

            DB::commit();

            $route = redirect(route('manage.jfk.list'))->with('success', 'JFK berhasil diperbaharui.');
            return $this->CekReview($route, '1M1', 'MENGUBAH DATA ENTRY LEVEL- TPA');

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat JFK',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function validation($id=null)
    {
        $id=$id==null?'':','.$id;
        return [
            [
                // Dosen & JFA
                'tpa_id' => ['required', 'exists:tpas,id'],
                'ref_jfk_id' => ['required','exists:ref_jabatan_fungsional_keahlians,id'],
                'tmt_mulai' => ['required', 'date'],
                'tmt_selesai' => ['nullable', 'date', 'after_or_equal:tmt_mulai'],

                'sk_pengakuan_ypt_id' => ['nullable','exists:sks,id'],

                'file_sk_ypt' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg'],
                'no_sk_ypt' => ['nullable', 'string', 'max:50', 'required_with:file_sk_ypt', 'unique:sks,no_sk'],
                'keterangan' => ['nullable', 'string', 'max:200', 'required_with:file_sk_ypt'],
                'tipe_dokumen' => ['nullable', 'string', 'max:50', 'required_with:file_sk_ypt','in:SK,Amandemen'],

            ],
            [

                'required' => ':attribute wajib diisi.',
                'date' => ':attribute harus berupa tanggal yang valid.',

                'required_without' => ':attribute wajib diisi jika :values tidak ada.',
                'required_without_all' => ':attribute wajib diisi jika :values tidak ada semuanya.',

            ],
            [

                'sk_pengakuan_ypt_id' => 'SK YPT JKF (Entry Level - TPA)',
                'file_sk_ypt' => 'file SK YPT JKF (Entry Level - TPA)',
                'no_sk_ypt' => 'Nomor SK YPT JKF (Entry Level - TPA)',
                'tmt_mulai' => 'Terakui Mulai Tanggal JKF (Entry Level - TPA)',
                'tmt_selesai' => 'Selesai Pada Tanggal JKF (Entry Level - TPA)',
                'keterangan' => 'Keterangan SK',
                'tipe_dokumen' => 'Tipe Dokumen SK',
            ],
        ];
    }

    public function history($id_user){
        if ($this->onlyOwnerAdminAndSdm($id_user) == true) {
            $tpa = Tpa::where('users_id', $id_user)->first();
            if (! $tpa) {
                return $this->handleRedirectBack()->with('error_alert', 'Data TPA Tidak Ditemukan!.');
            }
            $user = (new ProfileController)->based_user_data($id_user);
            $history = RiwayatJabatanFungsionalKeahlian::with([
                'sk_ypt',
                'data_jfk',
                'data_tpa'
                ])
                ->where('tpa_id', $tpa->id)
                ->get()
                ->map(function ($item) {
                    $item->is_active = is_null($item->tmt_selesai)
                        || $item->tmt_selesai >= today();

                    return $item;
                })
                ->sortByDesc('created_at');
            // dd($history);
            $this->MakeLog('User Berhasil Mengakses halaman Riwayat JFK dari TPA Terkait', ['tpa terkait' => $user->nama_lengkap]);

            $route = view('kelola_data.pegawai.view.history.jfk', compact('user', 'history'));
            return $route;
        }

        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');
    }
}
