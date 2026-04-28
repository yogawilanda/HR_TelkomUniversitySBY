<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Formation;
use App\Models\Pengawakan;
use App\Models\SK;
use App\Models\Tpa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PengawakanController extends Controller
{
    public function index()
    {
        // $formations = json_decode(Formation::with(['bagian', 'prodi', 'fakultas','level_id','atasan_formation'])
        //                             ->orderBy('atasan_formasi_id')
        //                             ->get());
        $pemetaans = json_decode(Pengawakan::with(['users', 'formasi', 'sk_ypt'])
            ->join('users', 'pengawakans.users_id', '=', 'users.id')
            ->where('tmt_selesai', null)

            ->orderBy('users.nama_lengkap', 'asc')
            ->select('pengawakans.*')
            ->get());
        // dd($pemetaans);

        // dd('masuk');

        // return view('kelola_data.fakultas.list',compact('send'));
        return view('kelola_data.sotk-pengawakan.list', compact('pemetaans'));
    }

    public function new()
    {
        $users = \App\Models\User::all()->sortBy('nama_lengkap');
        $formations = \App\Models\formation::all()->sortBy('nama_formasi');
        $sk_ypts = \App\Models\SK::all()->where('tipe_sk', 'Pengakuan YPT')->sortBy('no_sk');
        // dd($sk_ypts);

        return view('kelola_data.sotk-pengawakan.input', compact('users', 'formations', 'sk_ypts'));
    }

    public function create(Request $request)
    {
        // dd($request->all());
        // dd($request->file('file_sk'));
        $validated = $this->validation($request);

        if ($validated['no_sk'] != null) {
            $validated['sk_ypt_id'] = null;
        }
        DB::beginTransaction();
        // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
        try {

            if ($validated['sk_ypt_id'] == null) {
                // dd('masuk');

                try {

                    $validated['tipe_sk'] = 'Pengakuan YPT';
                    $validated['keperluan'] = 'Pemetaan';
                    $validated['file_sk'] = $request->file('file_sk');
                    $validated['keterangan'] = 'Pemetaan Pegawai';
                    // dd($validated);
                    $response = (new SKController())->new(new Request($validated), 'Ypt', false);
                    $sk = $response->getData()->data;
                    // dd($sk->id);
                    // $cek = $sk->getData(true);
                    // dd($cek['message']);
                    // dd($sk->getData(true),'cek');
                    // DB::commit();


                    $validated['sk_ypt_id'] = $sk->id;
                } catch (\Exception $e) {
                    // DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat SK YPT',
                        'error' => $e->getMessage()
                    ], 500);
                }
                // $validated['users_id'] = $request->users_id;

            }
            // $level = Formation::create($validated);
            $save = Pengawakan::create($validated);
            $bagian = Formation::where('id', $save->formasi_id)->first()['work_position_id'];
            // dd($bagian);
            // dd($save);

            $user = User::where('id', $validated['users_id'])->first();
            // dd($user);

            //apakah ini divisi utama pegawai iya?
            $message_if_false = null;
            if ($validated['is_main_position'] == '1') {
                $update =  Dosen::where('users_id', $validated['users_id'])->first() ?? Tpa::where('users_id', $validated['users_id'])->first();
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
                    if ($user['tipe_pegawai'] == 'Dosen') {
                        // dd('masuk sini');
                        $update->prodi_id = $bagian;
                    } else {
                        $update->bagian_id = $bagian;
                    }
                    $update->save();
                    // dd($update->save(), $update);
                } else {
                    $message_if_false = 'Tipe pegawai ' . $user['nama_lengkap'] . ' adalah ' . $user['tipe_pegawai'] . ', sedangkan pemetaan ini dalam lingkup ' . $bagian_type_pegawai->type_pekerja . '. Jadi divisi utama pegawai tetap seperti sebelumnya.';
                }
            }

            DB::commit();
            // dd('done');
            if ($validated['is_main_position'] == '1' && ($is_same_with_own_type_pegawai_and_bagian_type_pegawai == false)) {
                return redirect(route('manage.pengawakan.list'))->with('success', 'Pemetaan berhasil dibuat.' . ' Namun ' . $message_if_false);
            } else {
                return redirect(route('manage.pengawakan.list'))->with('success', 'Pemetaan berhasil dibuat.');
            };
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Formasi',
                'error' => $e->getMessage()
            ], 500);
        }

        // dd('masuk');


        // return redirect()->route('pengawakan.list')->with('success', 'Data pengawakan berhasil ditambahkan.');
    }

    public function create_by_import(Request $request)
    {
        try {
            // dd($request->all());
            // dd($request);
            $validated = $this->validation($request);
            $validated['sk_ypt_id'] = null;


            // $validated['singkatan_level'] = strtoupper($validated['singkatan_level']);
            // $level = Formation::create($validated);
            $make = pengawakan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Memetakan User',
                'success' => $make
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat memetakan user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function validation($request)
    {
        return $validated = $request->validate([
            'users_id'   => ['required'],
            'formasi_id' => ['required'],
            'is_main_position' => ['required'],
            'sk_ypt_id'  => ['nullable', 'required_without_all:file_sk,no_sk'],
            'tmt_mulai'  => ['required', 'date'],
            'file_sk'    => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'required_without:sk_ypt_id'],
            'no_sk'      => ['nullable', 'string', 'max:50', 'required_without:sk_ypt_id'],
        ], [
            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

            // pakai :values, bukan :other
            'required_without'      => ':attribute wajib diisi jika :values tidak ada.',
            'required_without_all'  => ':attribute wajib diisi jika :values tidak ada semuanya.',
        ], [
            // optional: ganti nama attribute biar rapi
            'sk_ypt_id' => 'SK YPT',
            'file_sk'   => 'file SK',
            'no_sk'     => 'nomor SK',
            'is_main_position' => 'Apakah Divisi Utama Pegawai'
        ]);
    }

    public function update($idPemetaan)
    {
        $Pemetaan = pengawakan::findOrFail($idPemetaan);
        $users = \App\Models\User::all()->sortBy('nama_lengkap');
        $formations = \App\Models\formation::all()->sortBy('nama_formasi');
        $sk_ypts = \App\Models\SK::all()->where('tipe_sk', 'Pengakuan YPT')->sortBy('no_sk');
        // dd($Pemetaan);

        return view('kelola_data.sotk-pengawakan.update', compact('users', 'formations', 'sk_ypts', 'Pemetaan'));
    }

    public function update_data(Request $request, $idPemetaan)
    {
        // dd($request->all());
        $validated = $request->validate([
            'users_id'   => ['required'],
            'formasi_id' => ['required'],
            'sk_ypt_id'  => ['nullable', 'required_without_all:file_sk,no_sk'],
            'tmt_mulai'  => ['required', 'date'],
            'file_sk'    => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'required_without:sk_ypt_id'],
            'no_sk'      => ['nullable', 'string', 'max:50', 'required_without:sk_ypt_id'],
        ], [
            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

            // pakai :values, bukan :other
            'required_without'      => ':attribute wajib diisi jika :values tidak ada.',
            'required_without_all'  => ':attribute wajib diisi jika :values tidak ada semuanya.',
        ], [
            // optional: ganti nama attribute biar rapi
            'sk_ypt_id' => 'SK YPT',
            'file_sk'   => 'file SK',
            'no_sk'     => 'nomor SK',
        ]);

        if ($validated['no_sk'] != null) {
            $validated['sk_ypt_id'] = null;
        }

        $Pemetaan = pengawakan::findOrFail($idPemetaan);
        $Pemetaan->update($validated);

        return redirect()->route('manage.pengawakan.list')->with('success', 'Data pengawakan berhasil diperbarui.');
    }

    public function history_pemetaan($id_user)
    {
        $user = (new ProfileController)->based_user_data($id_user);
        $user['pengawakans'] = pengawakan::with(['formasi.bagian', 'formasi.level_data', 'sk_ypt'])
            ->where('users_id', $id_user)
            ->orderBy('tmt_mulai', 'desc')
            ->get();
        $user['pengawakans_aktif'] = pengawakan::with(['formasi.bagian', 'formasi.level_data', 'sk_ypt'])
            ->where('users_id', $id_user)
            ->whereNull('tmt_selesai')
            ->orderBy('tmt_mulai', 'desc')
            ->get();

        // dd($user['pengawakans_aktif']);
        return view('kelola_data.pegawai.view.riwayat-jabatan', compact('user'));
    }

    public function end_pemetaan(Request $request)
    {
        DB::beginTransaction();

        try {

            $pemetaan = pengawakan::findOrFail($request->id);
            $pemetaan->tmt_selesai = now()->format('Y-m-d H:i:s');
            $pemetaan->save();

            DB::commit();
            return redirect()->route('manage.pengawakan.list')->with('success', 'Pemetaan berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Menonaktifkan Pemetaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
}
