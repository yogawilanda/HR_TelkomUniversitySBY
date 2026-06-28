<?php

namespace App\Http\Controllers;

use App\Models\RiwayatNip;
use App\Models\SK;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
                use Illuminate\Support\Facades\Storage;


// use Illuminate\Support\Facades\DB;

class SKController extends Controller
{
    public function index()
    {
        $sk_all = SK::all();

        return view('kelola_data.sk.list', compact('sk_all'));
    }

    public function view($id_sk_or_sk_number)
    {

        // dd($id_sk_or_sk_number);
        $sk = SK::where('id', $id_sk_or_sk_number)->first();

        if ($sk == null) {
            $sk_no = str_replace('|', '/', $id_sk_or_sk_number);
            // dd($sk_no);
            $sk = SK::where('no_sk', $sk_no)->first();
        }

        if ($sk == null) {
            return $this->handleRedirectBack()->with('message', 'SK Tidak Ditemukan');
        }

        $sksId = $sk->id;

        $query = DB::select("
                SELECT
                    sks_id,
                    CONCAT('[', GROUP_CONCAT(
                        CONCAT(
                            '{\"user_id\":\"', user_id, '\",\"user_nama\":\"', user_nama, '\",\"kategori\":[\"', kategori_list, '\"]}'
                        )
                    ), ']') AS users_json
                FROM (
                    SELECT
                        sks_id,
                        user_id, user_nama,
                        GROUP_CONCAT(kategori ORDER BY kategori SEPARATOR '\",\"') AS kategori_list
                    FROM (
                        -- gabungkan semua user + kategori
                        SELECT
                            b.sk_llkdikti_id AS sks_id,u.id as user_id, u.nama_lengkap as user_nama,
                            'Pangkat_dan_Golongan' AS kategori
                        FROM riwayat_pangkat_golongans b
                        JOIN dosens d ON b.dosen_id = d.users_id
                        JOIN users u ON u.id = d.users_id

                        UNION ALL

                        SELECT
                            c.sk_pengakuan_ypt_id,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Jabatan_Fungsional_KEahlian'
                        FROM riwayat_jabatan_fungsional_keahlians c
                        JOIN tpas t ON c.tpa_id = t.id
                        JOIN users u ON u.id = t.users_id

                        UNION ALL

                        SELECT
                            d.sk_llkdikti_id,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Jabatan_Fungsional_Akademik(LLKDIKTI)'
                        FROM riwayat_jabatan_fungsional_akademiks d
                        JOIN dosens dos ON dos.id = d.dosen_id
                        JOIN users u ON u.id = dos.users_id

                        UNION ALL

                        SELECT
                            e.sk_pengakuan_ypt_id,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Jabatan_Fungsional_Akademik(YPT)'
                        FROM riwayat_jabatan_fungsional_akademiks e
                        JOIN dosens dos ON dos.id = e.dosen_id
                        JOIN users u ON u.id = dos.users_id

                        UNION ALL

                        SELECT
                            f.sk_ypt_id,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Pemetaan'
                        FROM pengawakans f
                        JOIN users u ON u.id = f.users_id


                        UNION ALL

                        SELECT
                            rn.sk_ypt_or_amandemen,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Nomor Induk Pegawai'
                        FROM riwayat_nips rn
                        JOIN users u ON u.id = rn.users_id

                        UNION ALL

                        SELECT
                            z1.sk_llkdikti_id,
                            z3.id as user_id,
                            z3.nama_lengkap as user_nama,
                            'Pangkat_Golongan' from riwayat_pangkat_golongans z1
                        join dosens z2 on  z1.dosen_id=z2.id
                        join users z3 on z2.users_id=z3.id


                    ) x
                    WHERE sks_id = :sksId
                    GROUP BY sks_id, user_id, user_nama
                ) y
                GROUP BY sks_id
            ", ['sksId' => $sksId]);
        // dd($query==null);
        // $user_terkait = [];
        // if($user_terkait){
        $user_terkait = [];
        if ($query != null) {
            $user_terkait = (json_decode($query[0]->users_json, true));
        }
        // dd($user_terkait);
        $all_id_user = array_column($user_terkait, 'user_id');
        $cek = in_array(session('account')['id'], $all_id_user);


        // dump($this->isAdminOrSdm());
        if($this->isAdminOrSdm()==true || in_array(session('account')['id'], $all_id_user)){
            if ($sk != null) {
                $blade_view = 'kelola_data.sk.view';
                $user = null;
                if (explode('/', Route::current()->uri)[0] == 'profile') {
                    // $user = (ProfileController::class)->based_user_data(session('account')['id']);
                    $user = (new ProfileController)->based_user_data(session('account')['id']);
                    $blade_view = 'kelola_data.pegawai.view.history.sk.view';
                    // return view($blade_view, compact('sk', 'user_terkait','user'));
                }
                // dd($sk);
                $route = view($blade_view, compact('sk', 'user_terkait', 'user'));
                return $this->CekReview($route, '1S2', 'MELIHAT LIST SK/AMANDEMEN');

            }
            return $this->handleRedirectBack()->with('error_alert', 'SK Tidak Ditemukan!.');
        }
        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');;

    }

    public function new(Request $request, $YptOrDikti= null, $fromWhere = null)
    {
        // $cek1 = $fromWhere;
        $validated = $request->validate([
            'tmt_mulai' => ['required', 'date'],
            'tmt_selesai' => ['nullable', 'date'],
            'file_sk' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg'],
            'no_sk' => ['required', 'string', 'max:50'],
            'tipe_sk' => ['nullable', 'string', 'max:50', 'in:Pengakuan YPT,LLDIKTI'],
            'keperluan' => ['required', 'string', 'max:50'],
            'keterangan' => ['required', 'string', 'max:200'],
            'tipe_dokumen' => ['required', 'string', 'max:200'],
            // 'file_name'     => ['required', 'string', 'max:50'],

        ], [

            'required' => ':attribute wajib diisi.',
            'date' => ':attribute harus berupa tanggal yang valid.',

        ], [

            'file_sk' => 'file SK',
            'no_sk' => 'Nomor SK',
        ]);

        DB::beginTransaction();

        try {
            $cek_exist_no = SK::where('no_sk', $validated['no_sk'])->first();
            if ($cek_exist_no) {
                throw new \Exception('Nomor SK Sudah Terdaftar sebelumnya!.');
            }
            if($validated['tipe_sk']==null){
                $validated['tipe_sk'] = $YptOrDikti == 'YPT' ? 'Pengakuan YPT' : 'LLDIKTI';
            }
            $nama_file = $validated['keperluan'].'_'.$validated['tipe_sk'].'_'.pathinfo($validated['file_sk']->getClientOriginalName(), PATHINFO_FILENAME);
            // DB::commit();
            $ekstension = $validated['file_sk']->getClientOriginalExtension();
            $file_to_save = $validated['file_sk'];
            $validated['file_sk'] = null;
            $validated['file_sk'] = $nama_file.'.'.$ekstension;
            $save = $file_to_save->storeAs(
                'SK/'.$this->formatStringToURL($validated['keperluan']),
                $validated['file_sk'],
                'public'
            );
            $validated['file_sk'] = $save;
            $sk = SK::create($validated);
            $validated['sk_pengakuan_ypt_id'] = $sk->id;

            if ($save == null) {
                throw new \Exception('Terjadi masalah ketika melakukan proses simpan foto, foto mungkin terlalu besar atau format tidak sesuai');
            }

            DB::commit();
            $route = null;
            if ($fromWhere === null) {
                // dd('masuk',$cek1,$cek2,$fromWhere==null);
                $route = $this->handleRedirectBack()->with('success', 'SK '.$YptOrDikti.' Berhasil Ditambahkan');
            } else {
                // return $sk->id;
                $route = response()->json([
                    'success' => true,
                    'message' => 'Berhasil membuat SK',
                    'data' => $sk,
                ], 200);
            }

            return $this->CekReview($route, '1S3', 'MENAMBAH DATA SK');

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat SK',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function formatStringToURL($text)
    {
        // Hilangkan spasi depan dan belakang
        $text = trim($text);

        // Ganti satu atau lebih spasi di tengah menjadi _
        $text = preg_replace('/\s+/', '_', $text);

        return $text;
    }

    public function getFile($id_sk, $file_path = null)
    {
        $query = DB::select("
                SELECT
                    sks_id,
                    CONCAT('[', GROUP_CONCAT(
                        CONCAT(
                            '{\"user_id\":\"', user_id, '\",\"user_nama\":\"', user_nama, '\",\"kategori\":[\"', kategori_list, '\"]}'
                        )
                    ), ']') AS users_json
                FROM (
                    SELECT
                        sks_id,
                        user_id, user_nama,
                        GROUP_CONCAT(kategori ORDER BY kategori SEPARATOR '\",\"') AS kategori_list
                    FROM (
                        -- gabungkan semua user + kategori
                        SELECT
                            b.sk_llkdikti_id AS sks_id,u.id as user_id, u.nama_lengkap as user_nama,
                            'Pangkat_dan_Golongan' AS kategori
                        FROM riwayat_pangkat_golongans b
                        JOIN dosens d ON b.dosen_id = d.users_id
                        JOIN users u ON u.id = d.users_id

                        UNION ALL

                        SELECT
                            c.sk_pengakuan_ypt_id,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Jabatan_Fungsional_KEahlian'
                        FROM riwayat_jabatan_fungsional_keahlians c
                        JOIN tpas t ON c.tpa_id = t.id
                        JOIN users u ON u.id = t.users_id

                        UNION ALL

                        SELECT
                            d.sk_llkdikti_id,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Jabatan_Fungsional_Akademik(LLKDIKTI)'
                        FROM riwayat_jabatan_fungsional_akademiks d
                        JOIN dosens dos ON dos.id = d.dosen_id
                        JOIN users u ON u.id = dos.users_id

                        UNION ALL

                        SELECT
                            e.sk_pengakuan_ypt_id,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Jabatan_Fungsional_Akademik(YPT)'
                        FROM riwayat_jabatan_fungsional_akademiks e
                        JOIN dosens dos ON dos.id = e.dosen_id
                        JOIN users u ON u.id = dos.users_id

                        UNION ALL

                        SELECT
                            f.sk_ypt_id,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Pemetaan'
                        FROM pengawakans f
                        JOIN users u ON u.id = f.users_id


                        UNION ALL

                        SELECT
                            rn.sk_ypt_or_amandemen,
                            u.id as user_id, u.nama_lengkap as user_nama,
                            'Nomor Induk Pegawai'
                        FROM riwayat_nips rn
                        JOIN users u ON u.id = rn.users_id


                    ) x
                    WHERE sks_id = :sksId
                    GROUP BY sks_id, user_id, user_nama
                ) y
                GROUP BY sks_id
            ", ['sksId' => $id_sk]);
            // dd($query);
        // dd($query==null);
        // $user_terkait = [];
        // if($user_terkait){
        $user_terkait = [];
        if ($query != null) {
            $user_terkait = (json_decode($query[0]->users_json, true));
        }
        // dd($user_terkait);
        $all_id_user = array_column($user_terkait, 'user_id');
        $cek = in_array(session('account')['id'], $all_id_user);
        if($this->isAdminOrSdm()==true || in_array(session('account')['id'], $all_id_user)){



        // sjgdfhjsk
            // dd($id_sk);
            $sk = SK::where('id', $id_sk)->first();
            if ($file_path != null) {
                if (! ($sk->file_sk == $file_path)) {
                    abort(404, "File tidak ditemukan: $file_path");
                }
            }
            $storagePath = storage_path('app/public/SK/'.explode('_', $sk->file_sk)[0].'/'.$file_path);
            $storagePathByDatabase = storage_path('app/public/'.$sk->file_sk);
            // dd($storagePath,$sk->keperluan,$sk,explode("_", $sk->file_sk)[0]);
            $publicPath = public_path($file_path);
            // dd($sk->file_sk == $file_path,!($sk->file_sk == $file_path),$file_path,$sk->file_sk);

            if (file_exists($storagePath)) {
                $path = $storagePath;
            } elseif (file_exists($storagePathByDatabase)) {
                $path = $storagePathByDatabase;
            } elseif (file_exists($publicPath)) {
                $path = $publicPath;
            } else {
                // dd('masuk');
                abort(404, "File tidak ditemukan: $file_path");
            }
            // dd($path);

            return response()->file($path);
        }
        return $this->handleRedirectBack()->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');

    }

    public function history_sk($id_user)
    {
        $user = User::where('id', $id_user)->first();
            // if (! $user) {
            //     // dd('skdjfsedfsd');
            //     return $this->handleRedirectBack()->with('error_alert'.'Pegawai Tidak Ditemukan');
            // }
            // $cek_user = User::where('id', $id_User)->first();
        // dd($cek_user);
        if (! $user) {
            return $this->handleRedirectBack()->with('error_alert', 'User tidak ditemukan!.');
        }

        // dd($this->onlyOwnerAdminAndSdm($id_user));
        if ($this->onlyOwnerAdminAndSdm($id_user)==true) {

            // $user = ProfileController()->base
            $user = (new ProfileController)->based_user_data($id_user);

            $query = DB::select('
                SELECT DISTINCT sk.*
                FROM sks sk
                JOIN (
                    SELECT b.sk_llkdikti_id AS sks_id, u.id as user_id
                    FROM riwayat_pangkat_golongans b
                    JOIN dosens d ON b.dosen_id = d.users_id
                    JOIN users u ON u.id = d.users_id

                    UNION ALL

                    SELECT c.sk_pengakuan_ypt_id, u.id
                    FROM riwayat_jabatan_fungsional_keahlians c
                    JOIN tpas t ON c.tpa_id = t.id
                    JOIN users u ON u.id = t.users_id

                    UNION ALL

                    SELECT d.sk_llkdikti_id, u.id
                    FROM riwayat_jabatan_fungsional_akademiks d
                    JOIN dosens dos ON dos.id = d.dosen_id
                    JOIN users u ON u.id = dos.users_id

                    UNION ALL

                    SELECT e.sk_pengakuan_ypt_id, u.id
                    FROM riwayat_jabatan_fungsional_akademiks e
                    JOIN dosens dos ON dos.id = e.dosen_id
                    JOIN users u ON u.id = dos.users_id

                    UNION ALL

                    SELECT f.sk_ypt_id, u.id
                    FROM pengawakans f
                    JOIN users u ON u.id = f.users_id

                    UNION ALL

                    SELECT rn.sk_ypt_or_amandemen, u.id
                    FROM riwayat_nips rn
                    JOIN users u ON u.id = rn.users_id

                ) x ON sk.id = x.sks_id
                WHERE x.user_id = :userId
                order by sk.tmt_mulai
            ', [
                'userId' => $id_user,
            ]);

            $all_sk = $query;
            // dd($all_sk);

            $route = view('kelola_data.pegawai.view.history.sk', compact('user', 'all_sk'));
                return $this->CekReview($route, '1S5', 'MELIHAT HISTORY SK BY PEGAWAI TERKAIT');
        }
        return $this->handleRedirectBack()->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');
    }

    public function input_blade()
    {
        return view('kelola_data.sk.input');
    }

    public function edit($id)
    {
        $cek_exist = SK::where('id', $id)->first();
        if (! $cek_exist) {
            return $this->handleRedirectBack()->with('error_alert', 'SK Tidak Terdaftar');
        }

        $sk = $cek_exist;
        // dd($sk);
        return view('kelola_data.sk.update', compact('sk'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validation()[0], $this->validation()[1], $this->validation()[2]);

        try {
            DB::beginTransaction();
            $cek_exist_number = SK::where('no_sk', $request->id)->first();
            if ($cek_exist_number) {
                throw new \Exception('SK atau Amandemen dengan Nomor ini sudah terdaftar!.');
            }

            $file_to_save = $validated['file_sk'];
            $extension = $file_to_save->getClientOriginalExtension();
            $namaFile = time().'_'.'file_sk.'.$extension;

            $save = $file_to_save->storeAs(
                'SK/General',
                $namaFile,
                'public'
            );
            $validated['file_sk'] = $save;
            $sk = SK::create($validated);

            if ($save == null) {
                throw new \Exception('Terjadi masalah ketika melakukan proses simpan foto, foto mungkin terlalu besar atau format tidak sesuai');
            }

            DB::commit();

            $route = redirect(route('manage.sk.view', ['id_sk_or_sk_number' => $sk->id]))->with('success', 'SK Berhasil Ditambahkan!.');
            return $this->CekReview($route, '1S3', 'MENAMBAH DATA SK');

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->handleRedirectBack()
                ->withInput()
                ->withErrors($e->getMessage())
                ->with('error_alert', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // $validation = $this->validation($id);
        $validated = $request->validate($this->validation('need')[0], $this->validation('need')[1], $this->validation('need')[2]);
        // dd($validated);
        try {
            DB::beginTransaction();
            $sk_update = null;
            try {
                $sk_update = SK::findOrFail($id);
                // dump($sk_update->file_sk);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('SK ini tidak terdaftar!.');
            }

            // $file_sk = $sk_update->file_sk;

            if ($request->file_sk != null) {
                $file_to_save = $validated['file_sk'];
                $extension = $file_to_save->getClientOriginalExtension();
                $namaFile = time().'_'.'file_sk.'.$extension;

                $save = $file_to_save->storeAs(
                    'SK/General',
                    $namaFile,
                    'public'
                );


                $delete = Storage::delete(storage_path('app/public/'.$sk_update->file_sk));
                // dump($delete);
                $validated['file_sk'] = $save;
            }
            else{
                $validated['file_sk'] = $sk_update->file_sk;
            }
            // dd($validated);
            $save = $sk_update->update($validated);
            // dd($sk_update);

            if (!$save) {
                throw new \Exception('Terjadi masalah ketika melakukan proses simpan foto, foto mungkin terlalu besar atau format tidak sesuai');
                }


                DB::commit();
                // dump($sk_update->file_sk);
            $route = redirect(route('manage.sk.view', ['id_sk_or_sk_number' => $id]))->with('success', 'SK Berhasil Diperbarui!.');
            return $this->CekReview($route, '1S4', 'MENGUBAH DATA SK');

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->handleRedirectBack()
                ->withInput()
                ->withErrors($e->getMessage())
                ->with('error_alert', $e->getMessage());
        }
    }

    public function validation($wht = null)
    {
        if ($wht != null) {
            $file_sk = 'nullable';
        } else {
            $file_sk = 'required';
        }


        return [
            [
                'tipe_dokumen' => 'required|in:SK,AMANDEMEN',
                'no_sk' => 'required|string|max:50',
                'tipe_sk' => 'required_if:tipe_dokumen,SK|nullable|in:Pengakuan YPT,LLDIKTI',
                'keterangan' => 'required|string|max:200',
                'tmt_mulai' => 'required|date',
                'tmt_selesai' => 'nullable|date|after_or_equal:tmt_mulai',
                'file_sk' => $file_sk.'|file|mimes:pdf,png,jpg,jpeg|max:2048', // Max 2MB PDF
            ], [
                'tipe_sk.required_if' => 'Tipe SK wajib diisi jika dokumen berupa SK.',
                'tmt_selesai.after_or_equal' => 'TMT Selesai tidak boleh mendahului TMT Mulai.',
                'file_sk.mimes' => 'File harus berupa format PDF.',
            ], [
                'tipe_dokumen' => 'Tipe Dokumen SK atau Amandemen',
                'no_sk' => 'Nomor SK atau Amandemen',
                'tipe_sk' => 'Tipe SK atau Amandemen',
                'keterangan' => 'Keterangan Singkat SK atau Amandemen',
                'tmt_mulai' => 'SK atau Amandemen Terakui Mulai Tanggal',
                'tmt_selesai' => 'SK atau Amandemen Selesai Pada Tanggal',
                'file_sk' => 'File Dokumen SK atau Amandemen', // Max 2MB PDF
            ],
        ];
    }
}
