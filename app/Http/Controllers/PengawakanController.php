<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Formation;
use App\Models\Pengawakan;
use App\Models\SK;
use App\Models\Tpa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengawakanController extends Controller
{
    public function index()
    {
        // dd(session('account')['id']);
        if (!(isset(session('account')['role']['is_admin']) || isset(session('account')['role']['is_sdm']))) {
            // dd('masuk');
            $active_formasi = DB::select('
                SELECT k0.id as aktif_formasi
                FROM pengawakans p0
                JOIN users o0 ON o0.id = p0.users_id
                JOIN formations k0 ON k0.id = p0.formasi_id
                WHERE p0.users_id = ?
                AND (p0.tmt_selesai IS NULL OR p0.tmt_selesai >= CURDATE())
            ', [session('account')['id']]);
            // dd($active_formasi);

            $ids = collect($active_formasi)->pluck('aktif_formasi')->toArray();

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            // dd($plac)

            $active_pemetaan_user = DB::select("
                WITH RECURSIVE down AS (
                    SELECT
                        id as formasi_id, 0 AS level
                    FROM formations
                    WHERE id IN ($placeholders)

                    UNION ALL

                    SELECT
                        f.id as formasi_id, d.level + 1 as level
                    FROM formations f
                    JOIN down d ON f.atasan_formasi_id = d.formasi_id
                )
                SELECT * FROM down where down.level!=0;
            ", $ids);

            $ids = collect($active_pemetaan_user)->pluck('formasi_id')->toArray();
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
        }

        $pemetaans = Pengawakan::with(['users', 'formasi.bagian', 'sk_ypt'])
            ->join('users', 'pengawakans.users_id', '=', 'users.id');


        if (!(isset(session('account')['role']['is_admin']) || isset(session('account')['role']['is_sdm']))) {
            $save = $pemetaans->join('formations', 'pengawakans.formasi_id', '=', 'formations.id')->whereIn('pengawakans.formasi_id', $ids);
            $pemetaans = $save;
        }
        $next = $pemetaans
            ->orderBy('users.nama_lengkap', 'asc')
            ->select('pengawakans.*')
            ->get()
            ->map(function ($item) {
                if ($item->tmt_selesai == null) {
                    // dd($item->tmt_selesai==null);
                    $item->status = 'aktif';

                    return $item;
                } else {
                    $tmt = null;

                    if ($item->tmt_selesai) {
                        try {
                            $tmt = Carbon::createFromFormat('Y/m/d', $item->tmt_selesai);
                        } catch (\Exception $e) {
                            $tmt = Carbon::parse($item->tmt_selesai); // fallback
                        }
                    }

                    $item->status =
                        is_null($tmt) || $tmt->gte(now())
                        ? 'aktif'
                        : 'tidak';

                    return $item;
                }
            });

        $pemetaans = $next;
        return view('kelola_data.sotk-pengawakan.list', compact('pemetaans'));
    }

    public function new()
    {
        $users = \App\Models\User::all()->sortBy('nama_lengkap');
        $formations = \App\Models\Formation::all()->sortBy('nama_formasi');
        $sk_ypts = SK::Sk_Ypt()->sortBy('no_sk');

        // dd($sk_ypts);

        $route = view('kelola_data.sotk-pengawakan.input', compact('users', 'formations', 'sk_ypts'));

        return $this->CekReview($route, '1P4', 'MELIHAT LIST DATA PENGAWAKAN/PEMETAAN');

    }

    public function create(Request $request)
    {
        // dd($request->all());
        // dd($request->file('file_sk'));
        $validated = $request->validate(
            $this->validation()[0],
            $this->validation()[1],
            $this->validation()[2]
        );

        if ($validated['no_sk'] != null) {
            $validated['sk_ypt_id'] = null;
        }
        DB::beginTransaction();
        // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {

            if ($validated['sk_ypt_id'] == null) {
                $validated['tipe_sk'] = 'Pengakuan YPT';
                $validated['keperluan'] = 'Pemetaan';
                $validated['file_sk'] = $request->file('file_sk');
                $validated['keterangan'] = 'Pemetaan Pegawai';
                // dd($validated);
                $response = (new SKController)->new(new Request($validated), 'Ypt', false);
                $sk_data = $response->getData();
                // dd($sk_data);

                if ($response->getStatusCode() != 200) {
                    throw new \Exception('Gagal save SK: '.$sk_data->error);
                }
                $sk = $sk_data->data;
                $validated['sk_ypt_id'] = $sk->id;
            }
            // $level = Formation::create($validated);
            $save = Pengawakan::create($validated);
            $bagian = Formation::where('id', $save->formasi_id)->first()['work_position_id'];
            // dd($bagian);
            // dd($save);

            $user = User::where('id', $validated['users_id'])->first();
            // dd($user);

            // apakah ini divisi utama pegawai iya?
            $message_if_false = null;
            if ($validated['is_main_position'] == '1') {
                $update = Dosen::where('users_id', $validated['users_id'])->first() ?? Tpa::where('users_id', $validated['users_id'])->first();

                // if()
                // dd($update, $validated);
                //  = ;
                // dd($bagian);

                $bagian_type_pegawai = DB::table('work_positions as a')
                    // ->join('work_positions as b', 'b.id', '=', 'a.work_position_id')
                    ->where('a.id', $bagian)
                    ->select('a.type_pekerja')
                    ->first();
                // dd($bagian_type_pegawai->type_pekerja);
                $is_same_with_own_type_pegawai_and_bagian_type_pegawai = null;
                // Apakah tipe pegawai diperbolehkan menjadikan ini divisi utama berdasarkan Rolenya?
                // dd('Both',$bagian_type_pegawai->type_pekerja,$bagian_type_pegawai->type_pekerja == 'Both');
                if ($bagian_type_pegawai->type_pekerja == 'Both') {
                    $is_same_with_own_type_pegawai_and_bagian_type_pegawai = true;
                } else {
                    $is_same_with_own_type_pegawai_and_bagian_type_pegawai = strtoupper($bagian_type_pegawai->type_pekerja) == strtoupper($user['tipe_pegawai']);
                }
                // dd($is_same_with_own_type_pegawai_and_bagian_type_pegawai);
                if ($is_same_with_own_type_pegawai_and_bagian_type_pegawai) {

                    $update_is_main_position = Pengawakan::where('users_id', $user->id)
                        ->whereNot('id', $save->id)
                        ->update([
                            'is_main_position' => false,
                        ]);
                    // dd($update_is_main_position);

                    if ($user['tipe_pegawai'] == 'Dosen') {
                        // dd('masuk sini');
                        $update['prodi_id'] = $bagian;
                    } else {
                        // dump($update);

                        if ($update == null) {
                            $save = Tpa::create(
                                [
                                    'users_id' => $validated['users_id'],
                                    'nitk' => null,
                                    'bagian_id' => $bagian,
                                ]
                            );
                            if (! $save) {
                                throw new \Exception('Gagal membuat data TPA!.');
                            }
                            $update = $save;
                        } else {
                            $update['bagian_id'] = $bagian;
                        }
                        // dump($update);
                    }
                    $update->save();
                    // dd($update->save(), $update);
                } else {
                    $message_if_false = 'Tipe pegawai '.$user['nama_lengkap'].' adalah '.$user['tipe_pegawai'].', sedangkan pemetaan ini dalam lingkup '.$bagian_type_pegawai->type_pekerja.'. Jadi divisi utama pegawai tetap seperti sebelumnya.';
                }
            }

            DB::commit();
            // dd('done');
            $default = null;
            if ($validated['is_main_position'] == '1' && ($is_same_with_own_type_pegawai_and_bagian_type_pegawai == false)) {
                $default = redirect(route('manage.pengawakan.list'))->with('success', 'Pemetaan berhasil dibuat.'.' Namun '.$message_if_false);
            } elseif ($validated['is_main_position'] == '1' && ($is_same_with_own_type_pegawai_and_bagian_type_pegawai == true)) {
                $default = redirect(route('manage.pengawakan.list'))->with('success', 'Pemetaan berhasil dibuat. User juga berhasil dipetakan pada Divisi utamanya!.');

            } else {
                $default = redirect(route('manage.pengawakan.list'))->with('success', 'Pemetaan berhasil dibuat.');
            }

            return $this->CekReview($default, '1P1', 'MENAMBAH DATA PENGAWAKAN/PEMETAAN');

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->handleRedirectBack()
                ->withInput($request->all())
                ->withErrors(['error_alert' => $e->getMessage()]);
        }

        // dd('masuk');

        // return redirect()->route('pengawakan.list')->with('success', 'Data pengawakan berhasil ditambahkan.');
    }

    public function create_by_import(Request $request)
    {
        try {
            // dd($request->all());
            // dd($request);
            $validated = $request->validate(
                $this->validation()[0],
                $this->validation()[1],
                $this->validation()[2]
            );
            $validated['sk_ypt_id'] = null;

            // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
            // $level = Formation::create($validated);
            $make = Pengawakan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Memetakan User',
                'success' => $make,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat memetakan user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function validation()
    {
        return [[
            'users_id' => ['required','exists:users,id'],
            'formasi_id' => ['required','exists:formations,id'],
            'is_main_position' => ['required'],
            'sk_ypt_id' => ['nullable', 'required_without_all:file_sk,no_sk','exists:sks,id'],
            'tmt_mulai' => ['required', 'date'],
            'file_sk' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'required_without:sk_ypt_id'],
            'no_sk' => ['nullable', 'string', 'max:50', 'required_without:sk_ypt_id'],
            'tipe_dokumen' => ['nullable', 'string', 'max:50', 'required_with:file_sk','in:SK,AMANDEMEN'],

        ], [
            'required' => ':attribute wajib diisi.',
            'date' => ':attribute harus berupa tanggal yang valid.',

            // pakai :values, bukan :other
            'required_without' => ':attribute wajib diisi jika :values tidak ada.',
            'required_without_all' => ':attribute wajib diisi jika :values tidak ada semuanya.',
            'exists' => ':attribute Tidak Terdaftar!.',
        ], [
            // optional: ganti nama attribute biar rapi
            'users_id' => 'Pilihan Pegawai',
            'formasi_id' => 'Pilihan Formasi',
            'sk_ypt_id' => 'Pilihan SK YPT',
            'file_sk' => 'file SK',
            'no_sk' => 'nomor SK',
            'is_main_position' => 'Apakah Divisi Utama Pegawai',
            'tipe_dokumen' => 'Tipe Dokumen',

        ]];
    }

    public function update($idPemetaan)
    {
        try {
            $Pemetaan = null;
            try {
                $Pemetaan = Pengawakan::findOrFail($idPemetaan);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Pemetaan ini tidak terdaftar!.');
            }

            $users = \App\Models\User::all()->sortBy('nama_lengkap');
            $formations = \App\Models\Formation::all()->sortBy('nama_formasi');
            $sk_ypts = SK::Sk_Ypt()->sortBy('no_sk');
            // dd($Pemetaan);

            $route = view('kelola_data.sotk-pengawakan.update', compact('users', 'formations', 'sk_ypts', 'Pemetaan'));

            return $this->CekReview($route, '1P2', 'MELIHAT DATA PENGAWAKAN/PEMETAAN');

        } catch (\Exception $e) {
            return $this->handleRedirectBack()->with('error_alert', $e->getMessage());
        }
    }

    public function update_data(Request $request, $idPemetaan)
    {
        try {

            // dd($request->all());
            $validated = $request->validate(
                $this->validation()[0],
                $this->validation()[1],
                $this->validation()[2]
            );

            if ($validated['no_sk'] != null) {
                $validated['sk_ypt_id'] = null;
            }
            // dd('masukkhdf');
            if ($validated['sk_ypt_id'] == null) {
                $validated['tipe_sk'] = 'Pengakuan YPT';
                $validated['keperluan'] = 'Pemetaan';
                $validated['file_sk'] = $request->file('file_sk');
                $validated['keterangan'] = 'Pemetaan Pegawai';
                $response = (new SKController)->new(new Request($validated), 'Ypt', false);
                $sk_data = $response->getData();
                if ($response->getStatusCode() != 200) {
                    throw new \Exception('Gagal save SK: '.$sk_data->error);
                }
                $sk = $sk_data->data;
                $validated['sk_ypt_id'] = $sk->id;
            }

            $Pemetaan = null;
            try {
                $Pemetaan = Pengawakan::findOrFail($idPemetaan);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Pemetaan ini tidak terdaftar!.');
            }
            $Pemetaan->update($validated);

            $route = redirect()->route('manage.pengawakan.list')->with('success', 'Data pengawakan berhasil diperbarui.');

            return $this->CekReview($route, '1P3', 'MENGUBAH DATA PENGAWAKAN/PEMETAAN');

        } catch (\Exception $e) {
            DB::rollBack();

            $validated = $request->validate(
                $this->validation()[0],
                $this->validation()[1],
                $this->validation()[2]
            );

            return $this->handleRedirectBack()
                ->withInput($request->all())
                ->withErrors($validated);
        }
    }

    public function history_pemetaan($id_user)
    {
        if ($this->onlyOwnerAdminAndSdm($id_user) == true) {
            $user = (new ProfileController)->based_user_data($id_user);
            $user['pengawakans'] = Pengawakan::with(['formasi.bagian', 'formasi.level_id', 'sk_ypt'])
                ->where('users_id', $id_user)
                ->orderBy('tmt_mulai', 'desc')
                ->get();
            $user['pengawakans_aktif'] = Pengawakan::with(['formasi.bagian', 'formasi.level_id', 'sk_ypt'])
                ->where('users_id', $id_user)
                ->where(function ($q) {
                    $q->whereNull('tmt_selesai')
                        ->orWhere('tmt_selesai', '>', now()->subDay());
                })
                ->orderBy('tmt_mulai', 'desc')
                ->get();
            $route = view('kelola_data.pegawai.view.riwayat-jabatan', compact('user'));

            return $this->CekReview($route, '1P5', 'MELIHAT RIWAYAT DATA PENGAWAKAN/PEMETAAN BERDASARKAN PEMETAAN', true);
        }

        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');
    }

    public function end_pemetaan(Request $request)
    {
        DB::beginTransaction();

        try {
            $pemetaan = null;
            try {
                $pemetaan = Pengawakan::with('formasi')->findOrFail($request->id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception('Pemetaan ini tidak terdaftar!.');
            }
            $pemetaan->tmt_selesai = now()->format('Y-m-d H:i:s');
            $pemetaan->save();

            $user = User::find($pemetaan->users_id);
            $user->flash = 'Pemetaan anda dengan Jabatan '.$pemetaan->formasi->nama_formasi.' sudah berakhir!.';
            $user->save();

            DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

            DB::commit();

            return redirect()->route('manage.pengawakan.list')->with('success', 'Pemetaan berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Menonaktifkan Pemetaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function struktur(Request $request)
    {
        $validated = $request->validate([
            'filter_date' => ['nullable', 'date'],
        ]);

        $bindings = [];

        if ($request->filter_date) {
            $aktifDate = 'AND (a.tmt_selesai IS NULL OR a.tmt_selesai >= ?)';
            $bindings[] = $request->filter_date;
        } else {
            $aktifDate = 'AND (a.tmt_selesai IS NULL OR a.tmt_selesai >= NOW())';
        }

        //tes aja
        // $rawData = DB::select("
        //     SELECT
        //         ob.nama_formasi AS formasi,
        //         oa.urut AS urut_formasi,
        //         atasan.nama_formasi AS atasan_formasi
        //     FROM levels oa
        //     JOIN formations ob ON ob.level_id = oa.id
        //     LEFT JOIN formations atasan ON atasan.id = ob.atasan_formasi_id
        // ", $bindings);

        // dd($rawData);

        $rawData = DB::select("
        SELECT
            ob.nama_formasi AS formasi,
            oa.urut AS urut_formasi,
            atasan.nama_formasi AS atasan_formasi,

            (
                SELECT
                    CASE
                        WHEN COUNT(c.id) = 0 THEN NULL
                        ELSE CONCAT(
                            '[',
                            GROUP_CONCAT(
                                JSON_OBJECT(
                                    'user_id', c.id,
                                    'user_nama', c.nama_lengkap,
                                    'pengawakan_id', a.id,
                                    'formasi_id', b.id,
                                    'nama_formasi', b.nama_formasi,
                                    'tmt_mulai', a.tmt_mulai,
                                    'tmt_selesai', a.tmt_selesai
                                )
                            ),
                            ']'
                        )
                    END
                FROM formations b
                LEFT JOIN pengawakans a
                    ON b.id = a.formasi_id
                LEFT JOIN users c
                    ON c.id = a.users_id
                WHERE b.id = ob.id
                $aktifDate
            ) AS members

        FROM levels oa
        JOIN formations ob ON ob.level_id = oa.id
        LEFT JOIN formations atasan ON atasan.id = ob.atasan_formasi_id
    ", $bindings);

        $route = view('kelola_data.sotk-pengawakan.struktur', compact('rawData'));

        return $this->CekReview($route, '1P8', 'MELIHAT STRUKTUR PEMETAAN YANG SEDANG AKTIF', true);
    }
}
