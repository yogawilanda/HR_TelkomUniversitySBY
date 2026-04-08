<?php

namespace App\Http\Controllers;

use App\Models\refJenjangPendidikan;
use App\Models\riwayatJenjangPendidikan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatJenjangPendidikanController extends Controller
{
    public function index()
    {
        // $users = User::all();
        // foreach($users as $user){
        //     $pendidikan = riwayatJenjangPendidikan::with('refJenjangPendidikan')
        //         ->where('users_id', $user->id)
        //         ->join('ref_jenjang_pendidikans', 'ref_jenjang_pendidikans.id', '=', 'riwayat_jenjang_pendidikans.jenjang_pendidikan_id')
        //         ->orderBy('ref_jenjang_pendidikans.urutan', 'asc')
        //         ->select('riwayat_jenjang_pendidikans.*') // penting agar data utama tidak tertimpa
        //         ->get();



        //         // $user['pendidikan_tertinggi']

        //     for($i=0;$i<count($user['pendidikan_tertinggi'])-1;$i++){
        //         $user['pendidikan_tertinggi'][$i]['data_tingkat'] = refJenjangPendidikan::where('id', $user['pendidikan_tertinggi'][$i]['jenjang_pendidikan_id'] )->first();
        //     }
        // }
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
            $results[$i]['pendidikan_data'] = riwayatJenjangPendidikan::where('id', $results[$i]['id_pendidikan_tertinggi'])->first();
        }

        // dd($results,$results[0]['pendidikan_data']->refJenjangPendidikan,$results[0]['pendidikan_data']->bidang_pendidikan);
        return view('kelola_data.jenjang-pendidikan.list', compact('results'));
    }

    public function new()
    {
        
        $data_user = user::where('id', request()->id_user)->first();
        // dd($data_user);
        $jenjang_pendidikans = refJenjangPendidikan::all()->sortBy('jenjang_pendidikan');
        $users = user::all()->sortBy('nama_lengkap');
        return view('kelola_data.jenjang-pendidikan.input', compact('jenjang_pendidikans', 'users','data_user'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([

            // Staff & Jenjang Pendidikan
            'users_id'               => ['required'],
            'jenjang_pendidikan_id'  => ['required'],

            // Detail Pendidikan
            'bidang_pendidikan'      => ['nullable', 'string', 'max:150'],
            'jurusan'                => ['nullable', 'string', 'max:150'],
            'nama_kampus'            => ['nullable', 'string', 'max:150'],
            'alamat_kampus'          => ['nullable', 'string', 'max:300'],

            'tahun_lulus'            => ['required', 'integer', 'min:1900', 'max:' . now()->year],

            'nilai'                  => ['required', 'numeric', 'min:0', 'max:4'], // IPK

            'gelar'                  => ['nullable', 'string', 'max:50'],
            'singkatan_gelar'        => ['nullable', 'string', 'max:20'],

            // File Ijazah / Sertifikat
            'ijazah_file'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png'],

        ], [

            // Pesan Default
            'required'      => ':attribute wajib diisi.',
            'numeric'       => ':attribute harus berupa angka.',
            'integer'       => ':attribute harus berupa angka bulat.',
            'min'           => ':attribute minimal :min.',
            'max'           => ':attribute maksimal :max.',
            'date'          => ':attribute harus berupa tanggal yang valid.',
            'mimes'         => ':attribute harus berformat: :values.',

        ], [

            // Alias Attribute
            'users_id'                => 'Staff',
            'jenjang_pendidikan_id'   => 'Jenjang pendidikan',

            'bidang_pendidikan'       => 'Bidang pendidikan / fakultas',
            'jurusan'                 => 'Jurusan / Program Studi',
            'nama_kampus'             => 'Nama kampus',
            'alamat_kampus'           => 'Alamat kampus',

            'tahun_lulus'             => 'Tahun lulus',
            'nilai'                   => 'Nilai IPK',

            'gelar'                   => 'Gelar yang didapat',
            'singkatan_gelar'         => 'Singkatan gelar',

            'ijazah_file'             => 'Ijazah / Sertifikat kelulusan',

        ]);


        DB::beginTransaction();
        try {
            riwayatJenjangPendidikan::create($validated);



            DB::commit();
            return redirect(route('manage.jenjang-pendidikan.list'))->with('success', 'Jenjang Pendidikan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Jenjang Pendidikan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update($id_jp)
    {
        // dd($id_jp);
        $data_user = riwayatJenjangPendidikan::where('id', $id_jp)->first();
        $jenjang_pendidikans = refJenjangPendidikan::all()->sortBy('jenjang_pendidikan');
        $users = user::all()->sortBy('nama_lengkap');
        // dd($users[0]->id);
        return view('kelola_data.jenjang-pendidikan.update', compact('jenjang_pendidikans', 'users','data_user','id_jp'));
    }
    public function update_data(Request $request, $id_jp)
    {
        $validated = $request->validate([

            // Staff & Jenjang Pendidikan
            'users_id'               => ['required'],
            'jenjang_pendidikan_id'  => ['required'],

            // Detail Pendidikan
            'bidang_pendidikan'      => ['nullable', 'string', 'max:150'],
            'jurusan'                => ['nullable', 'string', 'max:150'],
            'nama_kampus'            => ['nullable', 'string', 'max:150'],
            'alamat_kampus'          => ['nullable', 'string', 'max:300'],

            'tahun_lulus'            => ['required', 'integer', 'min:1900', 'max:' . now()->year],

            'nilai'                  => ['required', 'numeric', 'min:0', 'max:4'], // IPK

            'gelar'                  => ['nullable', 'string', 'max:50'],
            'singkatan_gelar'        => ['nullable', 'string', 'max:20'],

            // File Ijazah / Sertifikat
            'ijazah_file'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png'],

        ], [

            // Pesan Default
            'required'      => ':attribute wajib diisi.',
            'numeric'       => ':attribute harus berupa angka.',
            'integer'       => ':attribute harus berupa angka bulat.',
            'min'           => ':attribute minimal :min.',
            'max'           => ':attribute maksimal :max.',
            'date'          => ':attribute harus berupa tanggal yang valid.',
            'mimes'         => ':attribute harus berformat: :values.',

        ], [

            // Alias Attribute
            'users_id'                => 'Staff',
            'jenjang_pendidikan_id'   => 'Jenjang pendidikan',

            'bidang_pendidikan'       => 'Bidang pendidikan / fakultas',
            'jurusan'                 => 'Jurusan / Program Studi',
            'nama_kampus'             => 'Nama kampus',
            'alamat_kampus'           => 'Alamat kampus',

            'tahun_lulus'             => 'Tahun lulus',
            'nilai'                   => 'Nilai IPK',

            'gelar'                   => 'Gelar yang didapat',
            'singkatan_gelar'         => 'Singkatan gelar',

            'ijazah_file'             => 'Ijazah / Sertifikat kelulusan',

        ]);

        $old_jp = riwayatJenjangPendidikan::where('id', $id_jp)->first();
        if(!isset($validated['ijazah_file'])){
            $validated['ijazah'] = $old_jp->ijazah;
        }

        DB::beginTransaction();
        try {
            // riwayatJenjangPendidikan::create($validated);
            
            $jp = riwayatJenjangPendidikan::findOrFail($id_jp);
            $jp->update($validated);


            DB::commit();
            return redirect(route('manage.jenjang-pendidikan.list'))->with('success', 'Jenjang Pendidikan berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate Jenjang Pendidikan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profileRiwayatPendidikan($idUser){
        $user = (new ProfileController)->based_user_data($idUser);
        // $user['pendidikan'] = riwayatJenjangPendidikan::with(['refJenjangPendidikan'])->find($user['id']);
        $user['pendidikan'] = riwayatJenjangPendidikan::with('refJenjangPendidikan')->where('users_id',$user['id'])->get()->sortBy(fn ($item) => optional($item->refJenjangPendidikan)->urutan);
        // dd($user['pendidikan'][0]['refJenjangPendidikan']);
        return view('kelola_data.pegawai.view.history.pendidikan',['user'=>$user]);
    }
}
