<?php

namespace App\Http\Controllers;

use App\Models\RefJenjangPendidikan;
use App\Models\RiwayatJenjangPendidikan;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatJenjangPendidikanController extends Controller
{
    public function index()
    {
        $results = User::select('*')
            ->selectSub(function ($query) {
                $query->from('riwayat_jenjang_pendidikans as e')
                    ->join('users as f', 'f.id', '=', 'e.users_id')
                    ->join('ref_jenjang_pendidikans as d', 'd.id', '=', 'e.jenjang_pendidikan_id')
                    ->whereColumn('f.nama_lengkap', 'users.nama_lengkap')
                    ->orderBy('d.urutan', 'asc')
                    ->limit(1)
                    ->select('e.id');
            }, 'id_pendidikan_tertinggi')
            ->get();

        for ($i = 0; $i < count($results) - 1; $i++) {
            $results[$i]['pendidikan_data'] = RiwayatJenjangPendidikan::where('id', $results[$i]['id_pendidikan_tertinggi'])->first();
        }

        // dd($results,$results[0]['pendidikan_data']->refJenjangPendidikan,$results[0]['pendidikan_data']->bidang_pendidikan);
        return view('kelola_data.jenjang-pendidikan.list', compact('results'));
    }

    public function new()
    {

        if ($this->onlyOwnerAdminAndSdm(request()->id_User) == true) {
            $data_user = User::where('id', request()->id_User)->first();
            // dd($data_user);
            $jenjang_pendidikans = RefJenjangPendidikan::all()->sortBy('jenjang_pendidikan');
            $users = User::all()->sortBy('nama_lengkap');
            $secret = '';
            // dd('cek',request()->input('wht'));
            if (request()->input('wht') != null) {
                $secret = 'user';
            }

            $route = view('kelola_data.jenjang-pendidikan.input', compact('jenjang_pendidikans', 'users', 'data_user', 'secret'));

            return $this->CekReview($route, '1F2', 'MELIHAT DATA JENJANG PENDIDIKAN');
        }

        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');

    }

    public function store(Request $request)
    {
        $validation = $this->validation();
        $validated = $request->validate($validation[0], $validation[1], $validation[2]);

        DB::beginTransaction();
        try {
            RiwayatJenjangPendidikan::create($validated);

            DB::commit();
            $default = route('manage.jenjang-pendidikan.list');
            $default = route('manage.jenjang-pendidikan.list');
            if (request()->input('secret') != null) {
                $default = route('profile.history.pendidikan.index', ['idUser' => $request->users_id]);
            }

            $route = redirect($default)->with('success', 'Jenjang Pendidikan berhasil dibuat.');

            return $this->CekReview($route, '1F1', 'MENAMBAH DATA JENJANG PENDIDIKAN');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput($validated)->with('error_alert', $e->getMessage());

        }
    }

    public function update($id_jp)
    {
        $data_user = RiwayatJenjangPendidikan::where('id', $id_jp)->first();
        if(!$data_user){
            return $this->handleRedirectBack()->with('error_alert', 'Riwayat Jenjang Pendidikan Tidak Ditemukan!.');
        }

        if ($this->onlyOwnerAdminAndSdm($data_user->users_id) == true) {
            $jenjang_pendidikans = RefJenjangPendidikan::all()->sortBy('jenjang_pendidikan');
            $users = User::all()->sortBy('nama_lengkap');

            $secret = '';
            if (request()->input('wht') != null) {
                $secret = 'user';
            }

            $user_data = null;
            if (request()->input('id_user')) {
                $data = User::where('id', request()->input('id_user'))->first();
                // dd($data);
                if ($data) {
                    $user_data = $data;
                }
            }
            // dd($user_data);

            return view('kelola_data.jenjang-pendidikan.update', compact('jenjang_pendidikans', 'users', 'data_user', 'id_jp', 'secret', 'user_data'));
        }

        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');
        // dd($id_jp);
    }

    public function update_data(Request $request, $id_jp)
    {
        $validation = $this->validation($id_jp);
        $validated = $request->validate($validation[0], $validation[1], $validation[2]);

        if ($this->onlyOwnerAdminAndSdm($request->users_id) == true) {
            try {
                $jp = RiwayatJenjangPendidikan::findOrFail($id_jp);
            } catch (ModelNotFoundException $e) {
                $this->handleRedirectBack()->with('error_alert', 'Riwayat Jenjang Pendidikan ini tidak terdaftar!.');
            }

            $old_jp = RiwayatJenjangPendidikan::where('id', $id_jp)->first();
            if (! isset($validated['ijazah_file'])) {
                $validated['ijazah'] = $old_jp->ijazah;
            }

            DB::beginTransaction();
            try {
                // RiwayatJenjangPendidikan::create($validated);

                // $jp = null;

                $jp->update($validated);

                DB::commit();
                $default_route = route('manage.jenjang-pendidikan.list');
                if (request('secret') == 'yes') {
                    $default_route = route('profile.history.pendidikan.index', ['idUser' => $request->users_id]);
                }
                $route = redirect($default_route)->with('success', 'Jenjang Pendidikan berhasil diupdate.');

                return $this->CekReview($route, '1F4', 'MENGUBAH DATA JENJANG PENDIDIKAN');

            } catch (\Exception $e) {
                DB::rollBack();

                return redirect()->back()->withInput($validated)->with('error_alert', $e->getMessage());
            }
        }

        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');
    }

    public function profileRiwayatPendidikan($idUser)
    {
        try {
            $jp = User::findOrFail($idUser);

        } catch (ModelNotFoundException $e) {
            return $this->handleRedirectBack()->with('error_alert', 'Riwayat Jenjang Pendidikan ini tidak terdaftar!.');
        }
        if ($this->onlyOwnerAdminAndSdm($idUser) == true) {

            $user = (new ProfileController)->based_user_data($idUser);
            // $user['pendidikan'] = RiwayatJenjangPendidikan::with(['refJenjangPendidikan'])->find($user['id']);
            $user['pendidikan'] = RiwayatJenjangPendidikan::with('refJenjangPendidikan')->where('users_id', $user['id'])->get()->sortBy(fn ($item) => optional($item->refJenjangPendidikan)->urutan);

            // dd($user['pendidikan'][0]['refJenjangPendidikan']);
            $route = view('kelola_data.pegawai.view.history.pendidikan', ['user' => $user]);

            return $this->CekReview($route, '1F3', 'MELIHAT HISTORY JENJANG PENDIDIKAN', true);
        }

        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]))->with('error_alert', 'Anda hanya boleh mengelola data anda sendiri!.');

    }

    public function validation($id = null)
    {
        $id = $id == null ? '' : ','.$id;

        return [
            [

                // Staff & Jenjang Pendidikan
                'users_id' => ['required', 'exists:users,id'],
                'jenjang_pendidikan_id' => ['required', 'exists:ref_jenjang_pendidikans,id'],

                // Detail Pendidikan
                'bidang_pendidikan' => ['nullable', 'string', 'max:150'],
                'jurusan' => ['nullable', 'string', 'max:150'],
                'nama_kampus' => ['nullable', 'string', 'max:150'],
                'alamat_kampus' => ['nullable', 'string', 'max:300'],

                'tahun_lulus' => ['required', 'integer', 'min:1900', 'max:'.now()->year],

                'nilai' => ['required', 'numeric', 'min:0', 'max:4'], // IPK

                'gelar' => ['nullable', 'string', 'max:50'],
                'singkatan_gelar' => ['nullable', 'string', 'max:20'],

                // File Ijazah / Sertifikat
                'ijazah_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png'],

            ], [

                // Pesan Default
                'required' => ':attribute wajib diisi.',
                'numeric' => ':attribute harus berupa angka.',
                'integer' => ':attribute harus berupa angka bulat.',
                'min' => ':attribute minimal :min.',
                'max' => ':attribute maksimal :max.',
                'date' => ':attribute harus berupa tanggal yang valid.',
                'mimes' => ':attribute harus berformat: :values.',
                'exists' => ':attribute Tidak Terdaftar!.',

            ], [

                // Alias Attribute
                'users_id' => 'Staff',
                'jenjang_pendidikan_id' => 'Jenjang pendidikan',

                'bidang_pendidikan' => 'Bidang pendidikan / fakultas',
                'jurusan' => 'Jurusan / Program Studi',
                'nama_kampus' => 'Nama kampus',
                'alamat_kampus' => 'Alamat kampus',

                'tahun_lulus' => 'Tahun lulus',
                'nilai' => 'Nilai IPK',

                'gelar' => 'Gelar yang didapat',
                'singkatan_gelar' => 'Singkatan gelar',

                'ijazah_file' => 'Ijazah / Sertifikat kelulusan',

            ],
        ];

    }
}
