<?php

namespace App\Http\Controllers;

use App\Models\RiwayatNip;
use App\Models\SK;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
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

        if($sk==null){
            $sk_no = str_replace("|", "/", $id_sk_or_sk_number);
            // dd($sk_no);
            $sk = Sk::where('no_sk',$sk_no)->first();
        }

        if($sk==null){
            return redirect()->back()->with('message','SK Tidak Ditemukan');
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
                    ) x
                    WHERE sks_id = :sksId
                    GROUP BY sks_id, user_id, user_nama
                ) y
                GROUP BY sks_id
            ", ['sksId' => $sksId]);
            // dd($query);
        $user_terkait = (json_decode($query[0]->users_json, true));
        // dD($user_terkait, $sk);
        // $results akan berupa array objek
        
        if ($sk != null) {
            $blade_view = 'kelola_data.sk.view';
            $user = null;
            if(explode('/', Route::current()->uri)[0]=='profile'){
                // $user = (ProfileController::class)->based_user_data(session('account')['id']);
                $user = (new ProfileController())->based_user_data(session('account')['id']);
                $blade_view = 'kelola_data.pegawai.view.history.sk.view';
                // return view($blade_view, compact('sk', 'user_terkait','user'));
            }
            return view($blade_view, compact('sk', 'user_terkait','user'));
        }
    }

    public function new(Request $request, $YptOrDikti, $fromWhere = null)
    {
        // $cek1 = $fromWhere;
        $validated = $request->validate([
            'tmt_mulai'     => ['required', 'date'],
            'file_sk'   => ['required', 'file', 'mimes:pdf,png,jpg,jpeg'],
            'no_sk'     => ['required', 'string', 'max:50'],
            'keperluan'     => ['required', 'string', 'max:50'],
            'keterangan'     => ['required', 'string', 'max:200'],
            // 'file_name'     => ['required', 'string', 'max:50'],

        ], [

            'required' => ':attribute wajib diisi.',
            'date'     => ':attribute harus berupa tanggal yang valid.',

        ], [

            'file_sk'           => 'file SK YPT',
            'no_sk'             => 'Nomor SK YPT',
        ]);

        DB::beginTransaction();

        try {
            // $validated['no_sk'] = $validated['no_sk_ypt'];
            // $validated['users_id'] = User::where('id', $request['users_id'])->first()['id'];
            // dd($users_id);
            $validated['tipe_sk'] = $YptOrDikti == 'YPT' ? 'Pengakuan YPT' : 'LLDIKTI';
            // $nip_user = RiwayatNip::where('users_id', $validated['users_id'])->where('is_active', 1)->first()['nip'];
            // dd($nip_user);
            // dd($request->file('file_sk'));
            // dd($request['file_sk']);
            // 'app/public/SK/Pemetaan/';
            $nama_file = $validated['keperluan'] . "_" . $validated['tipe_sk'] . "_" . pathinfo($validated['file_sk']->getClientOriginalName(), PATHINFO_FILENAME);
            // DB::commit();
            $ekstension = $validated['file_sk']->getClientOriginalExtension();
            $file_to_save = $validated['file_sk'];
            $validated['file_sk'] = null;
            $validated['file_sk'] = $nama_file  . "." . $ekstension;
            // dd($validated['file_sk'], 'cek');
            // dd($validated);
            // $validated['nip'] = RiwayatNip::where('nip', $nip_user)->first()->users_id;
            // dd($validated['users_id']);
            // $validated['keterangan'] = 'Jabatan Fungsional Pegawai';

            $sk = SK::create($validated);
            $validated['sk_pengakuan_ypt_id'] = $sk->id;
            DB::commit();
            // dd($validated['file_sk']);
            // $save = $file_to_save->store('SK/' . $this->formatStringToURL($validated['keperluan']), 'public');
            // $filename = time() . '.' . $file_to_save->getClientOriginalExtension();

            $save = $file_to_save->storeAs(
                'SK/' . $this->formatStringToURL($validated['keperluan']),
                $validated['file_sk'],
                'public'
            );

            if ($save == null) {
                throw new \Exception('Terjadi masalah ketika melakukan proses simpan foto, foto mungkin terlalu besar atau format tidak sesuai');
            }

            if ($fromWhere === null) {
                // dd('masuk',$cek1,$cek2,$fromWhere==null);
                return redirect()->back()->with('success', 'SK ' . $YptOrDikti . ' Berhasil Ditambahkan');
            } else {
                // return $sk->id;
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membuat SK',
                    'data' => $sk
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat SK',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function formatStringToURL($text)
    {
        // Hilangkan spasi depan dan belakang
        $text = trim($text);

        // Ganti satu atau lebih spasi di tengah menjadi _
        $text = preg_replace('/\s+/', '_', $text);

        return $text;
    }

    public function getFile($file_path, $id_sk)
    {
        $sk = Sk::where('id', $id_sk)->first();
        // dd($sk, ($sk->file_sk == $file_path));
        $storagePath = storage_path('app/public/SK/' . explode("_", $sk->file_sk)[0] . '/' . $file_path);
        // dd($storagePath,$sk->keperluan,$sk,explode("_", $sk->file_sk)[0]);
        $publicPath = public_path($file_path);
        // dd($sk->file_sk == $file_path,!($sk->file_sk == $file_path),$file_path,$sk->file_sk);
        if (!($sk->file_sk == $file_path)) {
            abort(404, "File tidak ditemukan: $file_path");
        }

        if (file_exists($storagePath)) {
            $path = $storagePath;
        } elseif (file_exists($publicPath)) {
            $path = $publicPath;
        } else {
            // dd('masuk');
            abort(404, "File tidak ditemukan: $file_path");
        }
        return response()->file($path);
    }
}
