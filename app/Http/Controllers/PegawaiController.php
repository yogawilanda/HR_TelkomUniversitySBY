<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorParser;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Dosen;
use App\Models\Emergency_contact;
use App\Models\Fakultas;
use App\Models\formation;
use App\Models\Prodi;
use App\Models\RefBagian;
use App\Models\refJabatanFungsionalAkademik;
use App\Models\refJenjangPendidikan;
use App\Models\RefPangkatGolongan;
use App\Models\RefStatusPegawai;
use App\Models\riwayatJabatanFungsionalAkademik;
use App\Models\riwayatJenjangPendidikan;
use App\Models\RiwayatNip;
use App\Models\Tpa;
use App\Http\Controllers\EmergencyContactController;
use App\Models\work_position;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PegawaiController extends Controller
{
    /**
     * Fungsi ini dipertahankan agar tidak menyebabkan error pada fungsi yang memanggilnya,
     * namun isinya dikosongkan karena cache telah dinonaktifkan.
     */
    private function clearPegawaiCache()
    {
        // Cache dinonaktifkan.
    }

    /**
     * Display a listing of the resource.
     * Menggunakan pengecekan ketat untuk mencegah Infinite Redirect Loop.
     */
    public function index($destination)
    {
        // 1. Normalisasi input agar konsisten (Active, Nonactive, Semua)
        $target = ucfirst(strtolower($destination));
        $validTargets = ['Active', 'Nonactive', 'Semua'];

        // 2. Cegah Redirect Loop: Hanya redirect jika input benar-benar di luar kategori
        if (!in_array($target, $validTargets)) {
            return redirect('/manage/pegawai/list/Semua');
        }

        // 3. Jika input valid tapi casing-nya salah (misal 'active'), redirect ke yang benar satu kali
        if ($destination !== $target) {
            return redirect('/manage/pegawai/list/' . $target);
        }

        $query = \App\Models\User::query()
            ->select([
                'users.*',
                'rn.nip as nip_aktif',
                DB::raw('COALESCE(wp_dosen.kode, wp_tpa.kode) as bagian_kode'),
                DB::raw('COALESCE(wp_dosen.position_name, wp_tpa.position_name) as bagian_nama'),
                DB::raw('COALESCE(wp_dosen.type_work_position, wp_tpa.type_work_position) as bagian_tipe'),
            ])
            ->leftJoin('riwayat_nips as rn', function ($join) {
                $join->on('users.id', '=', 'rn.users_id')
                    ->whereNull('rn.tmt_selesai');
            })
            ->leftJoin('dosens', 'users.id', '=', 'dosens.users_id')
            ->leftJoin('work_positions as wp_dosen', 'dosens.prodi_id', '=', 'wp_dosen.id')
            ->leftJoin('tpas', 'users.id', '=', 'tpas.users_id')
            ->leftJoin('work_positions as wp_tpa', 'tpas.bagian_id', '=', 'wp_tpa.id')
            ->orderBy('users.created_at', 'desc');

        if ($target === 'Active') {
            $query->where('users.is_active', 1);
        } elseif ($target === 'Nonactive') {
            $query->where('users.is_active', 0);
        }

        $users = $query->paginate(50);

        $send = [$target];
        return view('kelola_data.pegawai.list', compact('send', 'users'));
    }

    public function new()
    {
        $options = [
            'jenjang_pendidikan' => refJenjangPendidikan::all(),
            'status_pegawai'     => RefStatusPegawai::all(),
            'jenjang_jfa'        => RefPangkatGolongan::all(),
        ];

        $jenjang_pendidikan_options = $options['jenjang_pendidikan'];
        $status_pegawai_options     = $options['status_pegawai'];
        $jenjang_jfa_options        = $options['jenjang_jfa'];

        $send = null;
        return view('kelola_data.pegawai.input', compact('send', 'jenjang_pendidikan_options', 'status_pegawai_options', 'jenjang_jfa_options'));
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        [$rules, $messages, $attributes] = $this->getPegawaiRules($request);
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        $validated = $validator->validated();

        $response = DB::transaction(function () use ($request) {
            $response = $this->apiCreateCompleteAccount($request);

            return $response;
        });

        if ($response->getStatusCode() === 200) {
            DB::commit();
            $responseData = $response->getData(true);
            $user = $responseData['data_return'];

            $this->clearPegawaiCache();
            return redirect(route('manage.pegawai.view.personal-info', ['idUser' => $user['id']]))
                ->with('success', 'Data pegawai berhasil disimpan!');
        } else {
            // DB::
            DB::rollBack();

            $responseData = $response->getData(true);
            $errorMessage = $responseData['error'] ?? 'Terjadi kesalahan sistem';

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal: ' . $errorMessage);
        }
    }

    protected function getPegawaiRules(Request $request, $id = null)
    {
        $namaLengkapInput = $request->input('nama_lengkap');
        $isBatch = is_array($namaLengkapInput);
        $suffix = $isBatch ? '.*' : '';

        $rules = [
            "nama_lengkap$suffix"      => ['required', 'string', 'max:100', "regex:/^(?=.*[A-Za-z])[A-Za-z' ]+$/"],
            "username$suffix"          => [
                'required',
                'alpha_dash',
                'string',
                $isBatch ? 'distinct' : '',
                $isBatch
                    ? \Illuminate\Validation\Rule::unique('users', 'username')
                    : \Illuminate\Validation\Rule::unique('users', 'username')->ignore($id)
            ],
            "email_pribadi$suffix"      => ['required', 'email:filter'],
            "email_institusi$suffix"    => ['required', 'email:filter'],
            "jenis_kelamin$suffix"      => ['required', 'in:Perempuan,Laki-laki'],
            "tgl_lahir$suffix"          => ['required', 'date'],
            "tempat_lahir$suffix"          => ['required'],
            "alamat$suffix"          => ['required'],
            "tipe_pegawai$suffix"       => ['required', 'in:Dosen,TPA'],
            "status_kepegawaian$suffix" => ['required', 'string'],
            "jabatan$suffix"            => ['nullable', 'string'],
            "tmt_mulai$suffix"          => ['nullable', 'date', 'after:tgl_lahir' . ($isBatch ? $suffix : '')],
            "telepon$suffix" => ['required', 'string', 'regex:/^[0-9]+$/'],
            "nik$suffix"     => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            "nip$suffix"     => ['nullable', 'string', 'max:30', 'regex:/^[0-9]+$/'],
        ];

        $messages = [
            "required" => "Kolom :attribute Pegawai wajib diisi.",
            "email"    => "Alamat email pada :attribute Pegawai tidak valid.",
            "in"       => "Pilihan pada :attribute Pegawai tidak tersedia.",
            "max"      => "Input pada :attribute Pegawai terlalu panjang.",
            "date"     => "Format tanggal pada :attribute Pegawai tidak valid.",
            "after"    => "Tanggal :attribute Pegawai harus setelah Tanggal Lahir.",
            'nama_lengkap.regex' => 'Nama Lengkap Pegawai hanya boleh berisi huruf, spasi, dan tanda petik (\') serta harus mengandung minimal 1 huruf.',

            "telepon$suffix.required" => "Nomor telepon Pegawai wajib diisi.",
            "telepon$suffix.regex"    => "Nomor telepon Pegawai hanya boleh berisi angka.",

            // Pesan untuk NIK
            "nik$suffix.required"     => "NIK Pegawai wajib diisi.",
            "nik$suffix.max"          => "NIK Pegawai tidak boleh lebih dari :max karakter.",
            "nik$suffix.regex"        => "NIK Pegawai harus berupa angka saja.",

            // Pesan untuk NIP
            "nip$suffix.max"          => "NIP Pegawai tidak boleh lebih dari :max karakter.",
            "nip$suffix.regex"        => "NIP Pegawai harus berupa angka saja.",
        ];

        $attributes = [];
        if ($isBatch) {
            foreach ($namaLengkapInput as $index => $value) {
                $baris = " (Baris " . ($index + 1) . ")";

                $attributes["nama_lengkap.$index"]       = "Nama Lengkap" . $baris;
                $attributes["nik.$index"]                = "NIK" . $baris;
                $attributes["username.$index"]           = "Username" . $baris;
                $attributes["telepon.$index"]            = "Nomor Telepon" . $baris;
                $attributes["email_pribadi.$index"]      = "Email Pribadi" . $baris;
                $attributes["email_institusi.$index"]    = "Email Institusi" . $baris;
                $attributes["jenis_kelamin.$index"]      = "Jenis Kelamin" . $baris;
                $attributes["alamat.$index"]             = "Alamat" . $baris;
                $attributes["tempat_lahir.$index"]          = "Tempat Lahir" . $baris;
                $attributes["tgl_lahir.$index"]          = "Tanggal Lahir" . $baris;
                $attributes["tipe_pegawai.$index"]       = "Tipe Pegawai" . $baris;
                $attributes["status_kepegawaian.$index"] = "Status Kepegawaian" . $baris;
                $attributes["jabatan.$index"]            = "Jabatan" . $baris;
                $attributes["tmt_mulai.$index"]          = "TMT Mulai" . $baris;
                $attributes["nip.$index"]                = "NIP" . $baris;
            }
        } else {
            $attributes = [
                "nama_lengkap"       => "Nama Lengkap",
                "nik"                => "NIK",
                "username"           => "Username",
                "telepon"            => "Nomor Telepon",
                "email_pribadi"      => "Email Pribadi",
                "email_institusi"    => "Email Institusi",
                "jenis_kelamin"      => "Jenis Kelamin",
                "tgl_lahir"          => "Tanggal Lahir",
                "tempat_lahir"          => "Tempat Lahir",
                "tipe_pegawai"       => "Tipe Pegawai",
                "status_kepegawaian" => "Status Kepegawaian",
                "jabatan"            => "Jabatan",
                "alamat"            => "Alamat",
                "tmt_mulai"          => "TMT Mulai",
                "nip"                => "NIP",
            ];
        }

        return [$rules, $messages, $attributes];
    }

    public function apiCreateCompleteAccount(Request $request)
    {
        try {
            // Validasi data (disini $request sudah berupa data tunggal, bukan array)
            [$rules, $messages, $attributes] = $this->getPegawaiRules($request);
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => $validator->errors()->first()], 422);
            }

            $validated = $validator->validated();

            $cek_exist = DB::table('users')
                ->selectSub(function ($q) use ($validated) {
                    $q->from('users')
                        ->select('telepon')
                        ->where('telepon', $validated['telepon'])
                        ->limit(1);
                }, 'telepon')
                ->selectSub(function ($q) use ($validated) {
                    $q->from('users')
                        ->select('email_pribadi')
                        ->where('email_pribadi', $validated['email_pribadi'])
                        ->limit(1);
                }, 'email_pribadi')
                ->selectSub(function ($q) use ($validated) {
                    $q->from('users')
                        ->select('email_institusi')
                        ->where('email_institusi', $validated['email_institusi'])
                        ->limit(1);
                }, 'email_institusi')
                ->selectSub(function ($q) use ($validated) {
                    $q->from('users')
                        ->select('nik')
                        ->where('nik', $validated['nik'])
                        ->limit(1);
                }, 'nik')
                ->selectSub(function ($q) use ($validated) {
                    $q->from('users')
                        ->select('username')
                        ->where('username', $validated['username'])
                        ->limit(1);
                }, 'username')
                ->limit(1)
                ->first();

            $labels = $this->getPegawaiRules($request)[2];
            // dd($label[2]);
            $labels = [
                'telepon'         => 'Telepon',
                'email_pribadi'   => 'Email Pribadi',
                'email_institusi' => 'Email Institusi',
                'nik'             => 'NIK',
            ];

            $message_eror = collect((array) $cek_exist)
                ->only(array_keys($labels))
                ->filter()
                ->map(function ($val, $key) use ($labels) {
                    // Kita bungkus setiap baris dengan div Tailwind agar rata kiri
                    return "<div class='text-left w-full'>• " . $labels[$key] . " ($val) sudah terdaftar</div>";
                })
                ->implode('');

            if ($message_eror == '') {
                $validated['status_pegawai_id'] = $request->status_kepegawaian_id ?? $validated['status_kepegawaian'];
                $validated['is_new'] = true;
                // $validated['password'] = bcrypt($request->password);


                // 1. Buat Akun
                $account = $this->create_account(new Request($validated));
                // dd($account);
                // dd(($account), Hash::check($account[1], $account[0]['password']));

                // 2. Simpan Riwayat NIP
                RiwayatNip::create([
                    'users_id' => $account->id,
                    'nip' => $validated['nip'],
                    'tmt_mulai' => $validated['tmt_mulai'],
                    'status_pegawai_id' => $validated['status_pegawai_id']
                ]);

                // 3. Emergency Contacts
                if ($request->has('emergency_contacts')) {
                    foreach ($request->input('emergency_contacts') as $ecData) {
                        $ecData['users_id'] = $account->id;
                        // dd($ecData);
                        (new EmergencyContactController())->create(new Request($ecData));
                        // EmergencyContactController::fungsi($request);
                        // Emergency_contact::create($ecData);
                    }
                }

                // 4. Data Spesifik Dosen/TPA
                $validated['users_id'] = $account->id;
                $validated['jabatan_id'] = $request->jabatan_id ?? null;

                $tipe_emp = null;
                if ($validated['tipe_pegawai'] == 'Dosen') {
                    $tipe_emp = Dosen::create($validated);
                } else {
                    $tipe_emp = Tpa::create($validated);
                }

                return response()->json(['success' => true, 'data_return' => $account], 200);
            } else {
                throw new \Exception($message_eror);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function changePassword($idUser)
    {
        $user = (new ProfileController)->based_user_data($idUser);
        return view('kelola_data.pegawai.change-password', compact('user'));
    }

    public function updatePassword(Request $request, $idUser)
    {
        $validated = $request->validate(
            [
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)->max(20)->mixedCase()->numbers()->symbols()
                ],
                'new-password' => ['required', 'max:20'],
                'password_confirmation' => ['required', 'max:20'],
            ],
            [
                'password.required' => 'Password baru Pegawai wajib diisi.',
                'password.confirmed' => 'Konfirmasi password Pegawai tidak cocok.',
                'password.min' => 'Password baru Pegawai minimal :min karakter.',
                'password.max' => 'Password baru Pegawai maksimal :max karakter.',
            ]
        );

        $user = User::find($idUser);
        $user->password = bcrypt($validated['password']);
        $user->is_new = false;
        $user->save();
        // dd($user);

        // if()

        return redirect()->back()->with('success', 'Password berhasil diperbarui!');
    }

    public function setNonactive(Request $request, $idUser)
    {
        $user = User::find($idUser);
        $user->is_active = false;
        $user->save();

        $this->clearPegawaiCache();
        return redirect()->back()->with('success', 'Akun pegawai berhasil dinonaktifkan!');
    }

    public function setActive(Request $request, $idUser)
    {
        $user = User::find($idUser);
        $user->is_active = true;
        $user->save();

        $this->clearPegawaiCache();
        return redirect()->back()->with('success', 'Akun pegawai berhasil diaktifkan!');
    }

    public function dashboard()
    {
        $stats = [
            'total' => User::count(),
            'dosen' => User::where('tipe_pegawai', 'Dosen')->count(),
            'tpa' => User::where('tipe_pegawai', 'TPA')->count(),
            'active' => User::where('is_active', 1)->count(),
            'male' => User::where('jenis_kelamin', 'Laki-laki')->count(),
            'female' => User::where('jenis_kelamin', 'Perempuan')->count(),
        ];

        $recentEmployees = User::orderBy('created_at', 'desc')->take(10)->get();

        return view('kelola_data.pegawai.dashboard', compact('stats', 'recentEmployees'));
    }

    public function importAdd()
    {
        return view('kelola_data.pegawai.import.import');
    }

    public function importValidateFile(Request $req)
    {
        $validated = $req->validate([
            'file' => [
                'required',
                'file',
                'max:10280',
                'mimes:xlsx,xls,csv,json',
            ],
        ], [
            'file.required' => 'Pilih file Import terlebih dahulu.',
            'file.file'     => 'Upload harus berupa file.',
            'file.max'      => 'Ukuran file melebihi 10 MB.',
            'file.mimes'    => 'Format file tidak diizinkan. Gunakan: xlsx, xls, csv, atau json.',
        ]);
        try {
            // $file = $request->file('file_import');

            $file = $req->file('file');

            $pathTemplateAsli = public_path('template/Template Import Pegawai.xlsx');

            // 3. Hitung Hash (MD5) keduanya
            $hashTemplateAsli = md5_file($pathTemplateAsli);
            $hashFileUser = md5_file($file->getRealPath());
            // dd($hashFileUser, $hashTemplateAsli,);
            if ($hashFileUser == $hashTemplateAsli) {
                throw new \Exception('Sepertinya file yang Anda unggah masih berupa template dari kami dan belum dilakukan pengisian atau perubahan. Mohon untuk melengkapi dan menyesuaikannya terlebih dahulu sebelum mengunggah kembali.');
            }

            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            $data = array_values(array_filter(
                $sheet->toArray(null, true, true, true),
                fn($row) => (bool) array_filter($row)
            ));

            array_shift($data); // Hapus header
            $rows = $this->convertAllRow($data);

            session(['temp_rows' => $rows]);
            session(['old_data_import' => null]);

            return redirect()->route('manage.pegawai.import.validate-data')->with('message', 'Alert');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function importValidateData()
    {
        $data = session('temp_rows');
        session(['temp_rows' => null]);
        $refStatusKepegawaian = RefStatusPegawai::orderBy('status_pegawai', 'asc')
            ->pluck('status_pegawai', 'status_pegawai');

        $refFormasi = formation::orderBy('nama_formasi', 'asc')
            ->pluck('nama_formasi', 'nama_formasi');

        return view('kelola_data.pegawai.import.preview-data', [
            'data' => $data,
            'refStatusKepegawaian' => $refStatusKepegawaian,
            'refFormasi' => $refFormasi
        ]);
    }

    public function convertAllRow($data)
    {
        $out = [];
        foreach ($data as $index => $row) {
            $out[] = $this->convertRow($row);
        }

        return $out;
    }

    public function convertRow($data)
    {
        $out = [];
        $ref = $this->colToField();
        $cekEmergency = 1;
        $cekIsi = false;
        $countCekEc = 5;
        foreach ($ref as $col => $field) {
            $out[$field] = $data[$col] ?? null;
            if (str_contains($field, "ec" . $cekEmergency)) {
                if ($out[$field] != null) {
                    $cekIsi = true;
                }
                $countCekEc--;
                if ($countCekEc == 0) {
                    $out['ec' . $cekEmergency] = $cekIsi;
                    $cekEmergency++;
                    $countCekEc = 5;
                    $cekIsi = false;
                }
            }
        }
        return $out;
    }

    public function colToField()
    {
        return [
            'A'  => 'nama_lengkap',
            'B'  => 'nik',
            'C'  => 'username',
            'D'  => 'telepon',
            'E'  => 'email_pribadi',
            'F'  => 'email_institusi',
            'G'  => 'telepon_darurat',
            'H'  => 'jenis_kelamin',
            'I'  => 'alamat',
            'J'  => 'tempat_lahir',
            'K'  => 'tgl_lahir',
            'L'  => 'tipe_pegawai',
            'M'  => 'status_kepegawaian',
            'N'  => 'nip',
            'O'  => 'tmt_mulai',
            'P'  => 'jabatan',
            'Q'  => 'ec1_status_hubungan',
            'R'  => 'ec1_nama_lengkap',
            'S'  => 'ec1_telepon',
            'T'  => 'ec1_email',
            'U'  => 'ec1_alamat',
            'V'  => 'ec2_status_hubungan',
            'W'  => 'ec2_nama_lengkap',
            'X'  => 'ec2_telepon',
            'Y'  => 'ec2_email',
            'Z'  => 'ec2_alamat',
            'AA' => 'ec3_status_hubungan',
            'AB' => 'ec3_nama_lengkap',
            'AC' => 'ec3_telepon',
            'AD' => 'ec3_email',
            'AE' => 'ec3_alamat',
            'AF' => 'ec4_status_hubungan',
            'AG' => 'ec4_nama_lengkap',
            'AH' => 'ec4_telepon',
            'AI' => 'ec4_email',
            'AJ' => 'ec4_alamat'
        ];
    }

    public function importSaveData(Request $request)
    {
        session(['old_data_import' => $request->all()]);

        try {
            // 1. Normalisasi Data Tanggal
            $tglLahir = (array) $request->input('tgl_lahir', []);
            $tmtMulai = (array) $request->input('tmt_mulai', []);

            foreach ($tglLahir as $i => $tgl) {
                $tglLahir[$i] = $this->normalizeDate(trim($tgl));
            }
            foreach ($tmtMulai as $i => $tgl) {
                $tmtMulai[$i] = $this->normalizeDate(trim($tgl));
            }

            $request->merge(['tgl_lahir' => $tglLahir, 'tmt_mulai' => $tmtMulai]);

            // 2. Validasi Massal (Cek Duplikat di Excel & Format)
            [$rules, $messages, $attributes] = $this->getPegawaiRules($request);
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $allData = $request->all();
            $rowCount = count($allData['nama_lengkap']);

            DB::beginTransaction();

            for ($idx = 0; $idx < $rowCount; $idx++) {
                $userNew = [];
                foreach ($allData as $key => $values) {
                    if (is_array($values) && isset($values[$idx])) {
                        // Mapping Emergency Contacts (ec1_nama, etc)
                        if (preg_match('/^(ec[1-4])_(.+)$/', $key, $m)) {
                            $userNew[$m[1]][$m[2]] = $values[$idx];
                        } else {
                            $userNew[$key] = $values[$idx];
                        }
                    }
                }

                // Konversi Jabatan & Status ke ID
                $status = RefStatusPegawai::where('status_pegawai', $userNew['status_kepegawaian'])->first();
                if (!$status) throw new \Exception("Baris " . ($idx + 1) . ": Status '{$userNew['status_kepegawaian']}' tidak ditemukan.");
                $userNew['status_kepegawaian_id'] = $status->id;

                // Mapping Emergency Contacts ke array tunggal
                $ecs = [];
                foreach (['ec1', 'ec2', 'ec3', 'ec4'] as $ecKey) {
                    if (!empty($userNew[$ecKey]['nama_lengkap'])) {
                        $ecs[] = $userNew[$ecKey];
                    }
                }
                $userNew['emergency_contacts'] = $ecs;

                // 3. Panggil API Create (Kirim data baris tunggal)
                $singleRequest = new Request($userNew);
                $response = $this->apiCreateCompleteAccount($singleRequest);
                $resData = $response->getData(true);

                if ($response->getStatusCode() != 200) {
                    throw new \Exception("Baris " . ($idx + 1) . ": " . ($resData['error'] ?? 'G agal simpan data.'));
                } else if ($response->getStatusCode() == 200) {
                    if ($userNew['jabatan'] != null) {
                        $userNew['users_id'] = ($resData['data_return']['id']);
                        $formasi = formation::where('nama_formasi', $userNew['jabatan'])->first();
                        if (!$formasi) throw new \Exception("Baris " . ($idx + 1) . ": Jabatan '{$userNew['jabatan']}' tidak ditemukan.");
                        $userNew['formasi_id'] = $formasi->id;
                        $userNew['sk_ypt_id'] = 'Perlu Diisi';
                        $userNew['is_main_position'] = '0';

                        $create_jabatan = (new PengawakanController())->create_by_import(new Request($userNew));
                        $er = $create_jabatan->getData(true);

                        if ($create_jabatan->getStatusCode() != 200) {
                            throw new \Exception("Terjadi masalah ketika memetakan pegawai, berikut penjelasannya: " . ($er['error'] ?? 'Gagal simpan data.'));
                        } else {
                            $bagian_id = formation::where('id', $userNew['formasi_id'])->first()['work_position_id'];
                            $update =  Dosen::where('users_id', $userNew['users_id'])->first() ?? Tpa::where('users_id', $userNew['users_id'])->first();
                            if ($userNew['tipe_pegawai'] == 'Dosen') {
                                $update->prodi_id = $bagian_id;
                            } else {
                                $update->bagian_id = $bagian_id;
                            }
                            $update->save();
                        }
                    }
                }
            }

            DB::commit();
            $this->clearPegawaiCache();
            return redirect(route('manage.pegawai.list', ['destination' => 'Active']))->with('success', 'Import data berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function normalizeDate($value)
    {
        if (empty($value)) return null;
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return $value;
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function create_account(Request $request)
    {
        $data = $request->all();
        // Password default bcrypt
        $rawPass = 'US' . $data['telepon'];
        // dd($rawPass);
        $data['password'] = bcrypt($rawPass);
        $data['tgl_bergabung'] = $data['tmt_mulai'] ?? now();
        $data['is_active'] = true;

        return User::create($data);
    }
}
