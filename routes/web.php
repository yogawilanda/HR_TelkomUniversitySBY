<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AllAboutAuthController;
use App\Http\Controllers\CoeController;
use App\Http\Controllers\DashboardProdiController;
use App\Http\Controllers\DosenHasCOEController;
use App\Http\Controllers\DosenHasKKController;
use App\Http\Controllers\Dupak\DashboardController;
use App\Http\Controllers\Dupak\DetilPengajuanController;
use App\Http\Controllers\Dupak\PengajuanController;
use App\Http\Controllers\Dupak\PenunjukanTPAKController;
use App\Http\Controllers\Dupak\RiwayatController;
use App\Http\Controllers\Dupak\ValidasiController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PengawakanController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RefJabatanFungsionalAkademikController;
use App\Http\Controllers\RefJabatanFungsionalKeahlianController;
use App\Http\Controllers\RefJenjangPendidikanController;
use App\Http\Controllers\RefPangkatGolonganController;
use App\Http\Controllers\RefResearchCoeController;
use App\Http\Controllers\RefStatusPegawaiController;
use App\Http\Controllers\RiwayatJabatanFungsionalAkademikController;
use App\Http\Controllers\RiwayatJabatanFungsionalKeahlianController;
use App\Http\Controllers\RiwayatJenjangPendidikanController;
use App\Http\Controllers\RiwayatNipController;
use App\Http\Controllers\RiwayatPangkatGolonganController;
use App\Http\Controllers\SertifikasiDosenController;
use App\Http\Controllers\SKController;
use App\Http\Controllers\TestingSIMDKController;
use App\Http\Controllers\WorkPositionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::post('/testing/{kode}/{nama_fitur}', [TestingSIMDKController::class, 'submit_review'])->name('testing');

// Route::get('/test-skrg', function () {
//     return view('testing');
// });

Route::get('/testForm', function () {
    return view('testForm');
})->name('testform');

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::match(['get','post'], '/', function () {
//     dd(request()->method());
// });

// Template middleware role :
// ->middleware(['admin:{"is_admin":true|"and":{"bagian":"sumber daya manusia"|"range-level":[3|5]}|"range-level":[2|3]}'])
// ->middleware(['admin:{"is_admin":true}'])
// ->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}'])

// penjelasan kode role: karena pakai , tidak terdeteksi jadi '|' as ','

// Role yg ada sesuai urutan:
// - Apakah Admin? (pakai "is_admin":true)
// - Apakah berpatokan range urut level? (pakai "range-level":[2|3] , kiri urut tertinggi, kanan urut terendah)
// - Apakah berpatokan bagian khusus dan memiliki range urut level ? (pakai "and":{"bagian":"sumber daya manusia"|"range-level":[3|5]})  kiri urut tertinggi, kanan urut terendah
// - Apakah berpatokan bagian khusus aja? (pakai "bagian":"nama bagian sesuai di table bagian")
// - Apakah hanya dosen? ("is_dosen":true)
// - Apakah hanya tpa? ("is_tpa":true)





Route::get('/dashboard', function () {
    $user = Auth::user();

    // --- Achievement Badges Logic (Fitur 2A5) ---
    $lastTenReports = \App\Models\PelaporanPekerjaan::where('user_id', $user->id)
        ->latest()
        ->take(10)
        ->get();

    $badges = [
        'reliable' => false,
        'speedy'   => false
    ];

    if ($lastTenReports->count() >= 10) {
        $badges['reliable'] = $lastTenReports->every(fn($rep) => $rep->status === 'approved' || $rep->status === 'completed');
    }

    $lastFiveReports = $lastTenReports->take(5);
    if ($lastFiveReports->count() >= 5) {
        $avgHour = $lastFiveReports->avg(fn($rep) => $rep->created_at->hour);
        $badges['speedy'] = $avgHour < 17;
    }

    Log::info('User accessing dashboard', [
        'id' => $user->id,
        'email' => $user->email_institusi,
        'session_id' => Session::getId(),
    ]);

    return view('dashboard', compact('badges'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['prefix' => 'verify-email', 'as' => 'verify-email.'], function () {
    Route::get('/verify-email-view', [AllAboutAuthController::class, 'go_to_verify_page'])->name('view');
    Route::post('/verify-email-code', [AllAboutAuthController::class, 'verifiy_code'])->name('send');
});

Route::group(['prefix' => 'forget-password', 'as' => 'forget-password.'], function () {
    Route::post('/send-email', [AllAboutAuthController::class, 'forget_password'])->name('send');
    // Route::post('/send-email', [AllAboutAuthController::class, 'forget_password'])->name('send');
    Route::get('/action/{email_institusi}/{verified_code}', [AllAboutAuthController::class, 'reset_view'])->name('action');
    Route::post('/reset', [PegawaiController::class, 'reset_password'])->name('reset');
    // Route::post('/reset-password', [AllAboutAuthController::class, 'reset_password'])->name('reset');
});

Route::middleware(['auth',  \App\Http\Middleware\CekFlashUser::class])->group(function () {

    Route::get('/profile/', function () {
        return redirect(route('profile.personal-info', ['idUser' => session('account')['id']]));
    });

    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        // Route::get('/edit', [ProfileController::class, 'profileNormalisasi'])->name('profile.edit'); //sepertinya tidak terpakai
        Route::get('/personal-information/{idUser}', [ProfileController::class, 'personalInfo'])->name('personal-info'); //done onController (only admin, owner)
        Route::get('/change-password', function () {
            return redirect(route('profile.change-password', ['idUser' => session('account')['id']]))->with('error_alert', 'Sepertinya anda salah url, Kami sudah membenarkan!.');
        });
        Route::get('/change-password/{idUser}', [ProfileController::class, 'changePassword'])->name('change-password'); //done onController (only admin, owner)
        Route::post('/update-password/', [ProfileController::class, 'updatePassword'])->name('update-password'); //tdk perlu role
        Route::get('/update_data/{id_user}/', [PegawaiController::class, 'update_data'])->name('update-data');
        Route::post('/update/{id_user}', [PegawaiController::class, 'update'])->name('update');

        Route::group(['prefix' => 'emergency-contacts', 'as' => 'emergency-contacts.'], function () {
            Route::get('/list/{id_User}', [EmergencyContactController::class, 'list'])->name('list'); //done onController (only admin, owner)
            Route::get('/new/{id_User}', [EmergencyContactController::class, 'new'])->name('new'); //done onController (only admin, owner)
            Route::post('/new-data/{id_User}', [EmergencyContactController::class, 'new_data'])->name('new-data'); //done onController (only admin, owner)
            Route::get('/{id_User}/update/{id_emergency_contact}', [EmergencyContactController::class, 'updateView'])->name('updateView'); //done onController (only admin, owner)
            Route::post('/{id_User}/update-data/{id_emergency_contact}', [EmergencyContactController::class, 'updateData'])->name('updateData'); //done onController (only admin, owner)
        });

        Route::group(['prefix' => 'history', 'as' => 'history.'], function () {
            Route::get('/{id_user}/pemetaan', [PengawakanController::class, 'history_pemetaan'])->name('pemetaan'); //done onController (only admin, owner)
            Route::get('/{id_user}/sk', [SKController::class, 'history_sk'])->name('sk'); //done onController (only admin, owner)
            Route::get('/{id_pegawai}/history-nip', [RiwayatNipController::class, 'history_nip'])->name('nip'); //done onController (only admin, owner)
            Route::get('/riwayat/{id_user}', [DosenHasKKController::class, 'riwayat'])->name('kelompok-keahlian'); //done onController (only admin, owner) //cek
            Route::get('/coe/{id_user}', [DosenHasCOEController::class, 'History'])->name('coe'); //done onController (only admin, owner)
            Route::get('/jfa/{id_user}', [RiwayatJabatanFungsionalAkademikController::class, 'history'])->name('jfa');; //done onController (only admin, owner)
            Route::get('/jfk/{id_user}', [RiwayatJabatanFungsionalKeahlianController::class, 'history'])->name('jfk');; //done onController (only admin, owner)
            Route::get('/pangkat-golongan/{id_user}', [RiwayatPangkatGolonganController::class, 'history'])->name('pangkat-golongan');; //done onController (only admin, owner)


            Route::group(['prefix' => 'pendidikan', 'as' => 'pendidikan.'], function () {
                Route::get('/{idUser}/index', [RiwayatJenjangPendidikanController::class, 'profileRiwayatPendidikan'])->name('index'); //done onController (only admin, owner)
                Route::get('/new/', [RiwayatJenjangPendidikanController::class, 'new'])->name('new'); //done onController (only admin, owner)
                Route::get('/update/{id_jp}/', [RiwayatJenjangPendidikanController::class, 'update'])->name('update'); //done onController (only admin, owner)
                Route::get('/ijazah/{idUser}/{id_jp}/', [RiwayatJenjangPendidikanController::class, 'view_ijazah'])->name('view-ijazah'); //done onController (only admin, owner)
            });
        });

        Route::group(['prefix' => 'sk', 'as' => 'sk.'], function () {
            Route::get('/{id_sk_or_sk_number}/view', [SKController::class, 'view'])->name('view'); //done onController (only admin, owner)
            Route::get('/{file_path}/{id_sk}/file', [SKController::class, 'getFile'])->name('file'); //done onController (only admin, owner)
        });
    });
    Route::group(['prefix' => 'manage', 'as' => 'manage.'], function () {
        Route::get('/', function () {
            return view('kelola_data.index');
        })->name('view');
        // })->name('view')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);

        // Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
        // Route::get('/view', function () {
        //     return view('kelola_data.manajemen_akun.view');
        // })->name('view');

        // Route::get('/list', function () {
        //     return view('kelola_data.manajemen_akun.list');
        // })->name('list');

        // Route::get('/new', function () {
        //     return view('kelola_data.manajemen_akun.new');
        // })->name('new');
        // Route::get('/dashboard', function () {
        //     return view('kelola_data.manajemen_akun.dashboard');
        // })->name('dashboard');
        // });

        Route::group(['prefix' => 'pegawai', 'as' => 'pegawai.'], function () {

            Route::get('/dashboard', [PegawaiController::class, 'dashboard'])->name('dashboard')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"range-level":[1|2]}']);
            Route::get('/list/{destination}', [PegawaiController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"range-level":[1|4]}']);
            Route::get('/new', [PegawaiController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/create', [PegawaiController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update_data/{id_user}/', [PegawaiController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update/{id_user}', [PegawaiController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{idUser}/non-active', [PegawaiController::class, 'setNonactive'])->name('set-non-active')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{idUser}/set-active', [PegawaiController::class, 'setActive'])->name('set-active')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{idUser}/set-admin', [PegawaiController::class, 'setAdmin'])->name('set-admin')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{idUser}/set-non-admin', [PegawaiController::class, 'setNonAdmin'])->name('set-non-admin')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);

            Route::group(['prefix' => 'view', 'as' => 'view.'], function () {
                // Route::get('/{idUser}/employee-information', [ProfileController::class, 'employeeInfo'])->name('employee-info')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                Route::get('/{idUser}/personal-information', [ProfileController::class, 'personalInfo'])->name('personal-info')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                // Route::get('/{idUser}/riwayat-jabatan', [ProfileController::class, 'riwayatJabatan'])->name('riwayat-jabatan');
                Route::get('/{idUser}/change-password', [PegawaiController::class, 'changePassword'])->name('change-password')->middleware('admin:{"is_admin":true}'); //done role
                Route::post('/{idUser}/update-password', [PegawaiController::class, 'updatePassword'])->name('update-password')->middleware('admin:{"is_admin":true}'); //done role
            });

            Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
                Route::get('/add-file/', [PegawaiController::class, 'importAdd'])->name('add-file')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                Route::post('/validate-file/', [PegawaiController::class, 'importValidateFile'])->name('validate-file')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                Route::get('/validate-data/', [PegawaiController::class, 'importValidateData'])->name('validate-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                Route::post('/save-data/', [PegawaiController::class, 'importSaveData'])->name('save-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            });
        });

        Route::group(['prefix' => 'emergency-contact', 'as' => 'emergency-contact.'], function () {

            Route::get('/{id_User}/list', [EmergencyContactController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/{id_User}/new', [EmergencyContactController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{id_User}/new-data', [EmergencyContactController::class, 'new_data'])->name('new-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/{id_User}/update/{id_emergency_contact}', [EmergencyContactController::class, 'updateView'])->name('updateView')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{id_User}/update-data/{id_emergency_contact}', [EmergencyContactController::class, 'updateData'])->name('updateData')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
        });

        // Route::group(['prefix' => 'emergency-contact', 'as' => 'emergency-contact.'], function () {

        //     Route::get('/{id_User}/list', [EmergencyContactController::class, 'list'])->name('list');

        // });
        Route::resource('fakultas', FakultasController::class)->except(['index'])->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
        Route::resource('fakultas', FakultasController::class)->only(['index'])->middleware(['admin:{"is_dosen":true|"is_admin":true|"bagian":"sumber daya manusia"}']);

        // Route::group(['prefix' => 'fakultas', 'as' => 'fakultas.'], function () {
        // Route::get('/view', function () {
        //     return view('kelola_data.fakultas.view');
        // })->name('view')->middleware(['admin:admin']);

        // Route::get('/list', [FacultyController::class, 'index'])->name('list');

        // Route::get('/new', function () {
        //     return view('kelola_data.manajemen_akun.input');
        // })->name('new')->middleware(['admin:admin']);
        // Route::get('/dashboard', function () {
        //     return view('kelola_data.manajemen_akun.dashboard');
        // })->name('dashboard')->middleware(['admin:admin']);
        // });

        Route::group(['prefix' => 'level', 'as' => 'level.'], function () {
            // Route::get('/view', function () {
            //     return view('kelola_data.fakultas.view');
            // })->name('view');

            Route::get('/list/', [LevelController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/new', [LevelController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/create', [LevelController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update-data/{idLevel}', [LevelController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update/{idLevel}', [LevelController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);

            // Route::get('/new', function () {
            //     return view('kelola_data.level.input');
            // })->name('new');
            // Route::get('/dashboard', function () {
            //     return view('kelola_data.manajemen_akun.dashboard');
            // })->name('dashboard');
        });
        Route::group(['prefix' => 'jfa', 'as' => 'jfa.'], function () {
            Route::get('/list/', [RiwayatJabatanFungsionalAkademikController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/new/', [RiwayatJabatanFungsionalAkademikController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update/{id_jfa}', [RiwayatJabatanFungsionalAkademikController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update-data/{id_jfa}', [RiwayatJabatanFungsionalAkademikController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/store/', [RiwayatJabatanFungsionalAkademikController::class, 'store_data'])->name('store')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/riwayat/{id_user}', [RiwayatJabatanFungsionalAkademikController::class, 'history'])->name('history')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);

            Route::group(['prefix' => 'ref', 'as' => 'ref.'], function () {
                Route::get('/new/', [RefJabatanFungsionalAkademikController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true}']);
                Route::get('/edit/', [RefJabatanFungsionalAkademikController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true}']);
                Route::get('/list/', [RefJabatanFungsionalAkademikController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true}']);
                Route::post('/store/', [RefJabatanFungsionalAkademikController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true}']);
                Route::post('/update/{id}', [RefJabatanFungsionalAkademikController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true}']);
            });
        });

        Route::group(['prefix' => 'jfk', 'as' => 'jfk.'], function () {
            Route::get('/list/', [RiwayatJabatanFungsionalKeahlianController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/new/', [RiwayatJabatanFungsionalKeahlianController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/store/', [RiwayatJabatanFungsionalKeahlianController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update/{id_jfk}/', [RiwayatJabatanFungsionalKeahlianController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update-data/{id_jfk}/', [RiwayatJabatanFungsionalKeahlianController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/fill-sk-ypt/{id_jfk}/', [RiwayatJabatanFungsionalKeahlianController::class, 'isi_sk_ypt'])->name('fill-sk-ypt')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/riwayat/{id_user}', [RiwayatJabatanFungsionalKeahlianController::class, 'history'])->name('riwayat')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);


            Route::group(['prefix' => 'ref', 'as' => 'ref.'], function () {
                Route::get('/list/', [RefJabatanFungsionalKeahlianController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true}']);
                Route::post('/store/', [RefJabatanFungsionalKeahlianController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true}']);
                Route::post('/update/{id}', [RefJabatanFungsionalKeahlianController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true}']);
            });
        });

        Route::group(['prefix' => 'pangkat-golongan', 'as' => 'pangkat-golongan.'], function () {
            Route::get('/list/', [RiwayatPangkatGolonganController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/new/', [RiwayatPangkatGolonganController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/store/', [RiwayatPangkatGolonganController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update/{id_pg}/', [RiwayatPangkatGolonganController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update-data/{id_pg}/', [RiwayatPangkatGolonganController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/fill-sk-dikti/{id_pg}/', [RiwayatPangkatGolonganController::class, 'isi_sk_dikti'])->name('fill-sk-dikti')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/history/{id_user}/', [RiwayatPangkatGolonganController::class, 'history'])->name('history')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);

            Route::group(['prefix' => 'ref', 'as' => 'ref.'], function () {
                Route::get('/', function () {
                    return redirect(route('manage.pangkat-golongan.ref.list'));
                })->name('index');
                Route::get('/list/', [RefPangkatGolonganController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true}']);
                Route::get('/new/', [RefPangkatGolonganController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true}']);
                Route::get('/edit/{id_rpg}/', [RefPangkatGolonganController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true}']);
                Route::post('/update/', [RefPangkatGolonganController::class, 'update'])->name('update-data')->middleware(['admin:{"is_admin":true}']);
                Route::post('/store/', [RefPangkatGolonganController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true}']);
            });
        });

        Route::group(['prefix' => 'jenjang-pendidikan', 'as' => 'jenjang-pendidikan.'], function () {
            Route::get('/list/', [RiwayatJenjangPendidikanController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/new/', [RiwayatJenjangPendidikanController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/store/', [RiwayatJenjangPendidikanController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update/{id_jp}/', [RiwayatJenjangPendidikanController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update-data/{id_jp}/', [RiwayatJenjangPendidikanController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/{idUser}/index', [RiwayatJenjangPendidikanController::class, 'profileRiwayatPendidikan'])->name('index')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/{idUser}/history', [RiwayatJenjangPendidikanController::class, 'profileRiwayatPendidikan'])->name('history')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/ijazah/{idUser}/{id_jp}/', [RiwayatJenjangPendidikanController::class, 'view_ijazah'])->name('view-ijazah')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);


            Route::group(['prefix' => 'ref', 'as' => 'ref.'], function () {
                Route::get('/new/', [RefJenjangPendidikanController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true}']);
                Route::get('/edit/', [RefJenjangPendidikanController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true}']);
                Route::get('/list/', [RefJenjangPendidikanController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true}']);
                Route::post('/store/', [RefJenjangPendidikanController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true}']);
                Route::post('/update/', [RefJenjangPendidikanController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true}']);
            });
        });

        Route::group(['prefix' => 'riwayat-nip', 'as' => 'riwayat-nip.'], function () {
            Route::get('/list/', [RiwayatNipController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/new/', [RiwayatNipController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/create/', [RiwayatNipController::class, 'create_data'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/history-nip/{id_pegawai}', [RiwayatNipController::class, 'history_nip'])->name('history')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update-data/{id_nip}', [RiwayatNipController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update/{id_nip}', [RiwayatNipController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
        });

        Route::group(['prefix' => 'sk', 'as' => 'sk.'], function () {
            Route::get('/list', [SKController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/input', [SKController::class, 'input_blade'])->name('input')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/edit/{id}', [SKController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update/{id}', [SKController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{YptOrDikti}/new', [SKController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/simpan', [SKController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/{id_sk_or_sk_number}/view', [SKController::class, 'view'])->name('view'); //role sudah diatur di controller krn berhubungan dg profile
            // Route::get('/new-dikti/',[SKController::class, 'new'])->name('new-dikti');

            Route::get('/file/{id_sk}', [SKController::class, 'getFile'])->name('file'); //role sudah diatur di controller krn berhubungan dg profile

            Route::get('/history/sk/{id_user}', [SKController::class, 'history_sk'])->name('history'); //role sudah diatur di controller krn berhubungan dg profile
        });

        Route::group(['prefix' => 'formasi', 'as' => 'formasi.'], function () {
            // Route::get('/view', function () {
            //     return view('kelola_data.formasi.view');
            // })->name('view');

            Route::get('/list/', [FormationController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/new/', [FormationController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/create/', [FormationController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update/{idFormasi}', [FormationController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update-data/{idFormasi}', [FormationController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);

            // Route::get('/new', function () {
            //     return view('kelola_data.formasi.view');
            // })->name('new');
            // Route::get('/dashboard', function () {
            //     return view('kelola_data.manajemen_akun.dashboard');
            // })->name('dashboard');
        });

        Route::group(['prefix' => 'pengawakan', 'as' => 'pengawakan.'], function () {
            // Route::get('/view', function () {
            //     return view('kelola_data.sotk-pengawakan.view');
            // })->name('view');
            // Route::get('/input', function () {
            //     return view('kelola_data.sotk-pengawakan.input');
            // })->name('input');

            Route::get('/list/', [PengawakanController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"range-level":[1|4]}']);
            Route::get('/new/', [PengawakanController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/struktur/', [PengawakanController::class, 'struktur'])->name('struktur')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"range-level":[1|4]}']);
            Route::post('/create/', [PengawakanController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/update/{idPemetaan}/', [PengawakanController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update-data/{idPemetaan}/', [PengawakanController::class, 'update_data'])->name('update-data')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/selesaikan-jabatan/', [PengawakanController::class, 'end_pemetaan'])->name('selesaikan-jabatan')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/history-pemetaan/{id_user}/', [PengawakanController::class, 'history_pemetaan'])->name('history-pemetaan')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
        });

        // Fakultas Routes

        // Prodi Routes
        // Route::resource('prodi', ProdiController::class)->only(['create', 'store'])->middleware(['admin:admin']);
        Route::resource('prodi', ProdiController::class)->except(['edit'])->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);;

        Route::group(['prefix' => 'prodi', 'as' => 'prodi.'], function () {
            Route::get('/{prodi}/get-cached-stats', [ProdiController::class, 'getCachedStats'])->name('getCachedStats')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{prodi}/update-stats', [ProdiController::class, 'updateStats'])->name('updateStats')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/{prodi_id}/edit', [ProdiController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
        });

        Route::group(['prefix' => 'bagian', 'as' => 'bagian.'], function () {
            Route::get('/list', [WorkPositionController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/input', [WorkPositionController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/{id_wp}/edit', [WorkPositionController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/create', [WorkPositionController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/{id_wp}/edit_data', [WorkPositionController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
        });

        Route::group(['prefix' => 'status-pegawai', 'as' => 'status-pegawai.'], function () {
            Route::get('/list', [RefStatusPegawaiController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true}']);
            Route::post('/update', [RefStatusPegawaiController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true}']);
            Route::post('/create', [RefStatusPegawaiController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true}']);
        });

        // Dashboard Prodi Routes
        Route::group(['prefix' => 'dashboard-prodi', 'as' => 'dashboard-prodi.'], function () {
            Route::get('/pendidikan', [DashboardProdiController::class, 'pendidikan'])->name('pendidikan');
            Route::get('/fungsional', [DashboardProdiController::class, 'fungsional'])->name('fungsional');
            Route::get('/kepegawaian', [DashboardProdiController::class, 'kepegawaian'])->name('kepegawaian');
        });

        // Sertifikasi Dosen Routes
        Route::group(['prefix' => 'sertifikasi-dosen', 'as' => 'sertifikasi-dosen.'], function () {
            Route::get('/list', [SertifikasiDosenController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::get('/bpmn', [SertifikasiDosenController::class, 'bpmn'])->name('bpmn')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::get('/{id_serdos}/file', [SertifikasiDosenController::class, 'serdos_file'])->name('file')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::get('/input', [SertifikasiDosenController::class, 'create'])->name('input')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::post('/store', [SertifikasiDosenController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::get('/view/{id}', [SertifikasiDosenController::class, 'view'])->name('view')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::get('/edit/{id}', [SertifikasiDosenController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update/{id}', [SertifikasiDosenController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::delete('/destroy/{id}', [SertifikasiDosenController::class, 'destroy'])->name('destroy')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            Route::get('/upload', [SertifikasiDosenController::class, 'upload'])->name('upload')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            // Route::get('/file/{id}', [SertifikasiDosenController::class, 'view_file'])->name('file')->middleware(['admin:dosen|admin']);
            Route::post('/process-upload', [SertifikasiDosenController::class, 'processUpload'])->name('process-upload')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
        });

        // Route::resource('coe', \App\Http\Controllers\CoeController::class);
        Route::group(['prefix' => 'coe', 'as' => 'coe.'], function () {
            Route::get('/list/', [CoeController::class, 'index'])->name('index')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"bagian":"center of excellent"}']);
            Route::get('/new/', [CoeController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/create/', [CoeController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::post('/update/{id_coe}', [CoeController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
            Route::get('/edit/{id_coe}', [CoeController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);

            Route::group(['prefix' => 'ref-reserach', 'as' => 'ref-reserach.'], function () {
                Route::get('/list/', [RefResearchCoeController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true}']);
                Route::get('/edit/{id_ref}', [RefResearchCoeController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true}']);
                Route::get('/new/', [RefResearchCoeController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true}']);
                Route::post('/create/', [RefResearchCoeController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true}']);
                Route::post('/update/{id}', [RefResearchCoeController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true}']);
            });

            Route::group(['prefix' => 'dosen', 'as' => 'dosen.'], function () {
                Route::get('/list/', [DosenHasCOEController::class, 'list'])->name('list')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
                Route::get('/edit/{id_coe}', [DosenHasCOEController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                Route::get('/new/', [DosenHasCOEController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                Route::post('/create/', [DosenHasCOEController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                Route::post('/update/{id_coe}', [DosenHasCOEController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
                Route::get('/history/{id_user}', [DosenHasCOEController::class, 'History'])->name('history')->middleware(['admin:{"is_admin":true|"is_dosen":true|"bagian":"sumber daya manusia"}']);
            });
        });

        // Kelompok Keahlian Routes
        Route::group(['prefix' => 'kelompok-keahlian', 'as' => 'kelompok-keahlian.'], function () {
            Route::get('/list', [\App\Http\Controllers\KelompokKeahlianController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::get('/input', [\App\Http\Controllers\KelompokKeahlianController::class, 'create'])->name('input')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::post('/store', [\App\Http\Controllers\KelompokKeahlianController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::get('/view/{id}', [\App\Http\Controllers\KelompokKeahlianController::class, 'show'])->name('view')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::get('/edit/{id}', [\App\Http\Controllers\KelompokKeahlianController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true|"|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::post('/update/{id}', [\App\Http\Controllers\KelompokKeahlianController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::delete('/destroy/{id}', [\App\Http\Controllers\KelompokKeahlianController::class, 'destroy'])->name('destroy')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::post('/nonaktifkan/{id}', [\App\Http\Controllers\KelompokKeahlianController::class, 'nonaktifkan'])->name('nonaktifkan')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::post('/assign-dosen/{id}', [\App\Http\Controllers\KelompokKeahlianController::class, 'assignDosen'])->name('assignDosen')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            Route::get('/pegawai-list', [\App\Http\Controllers\KelompokKeahlianController::class, 'pegawaiList'])->name('pegawai-list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);

            Route::group(['prefix' => 'sub', 'as' => 'sub.'], function () {
                Route::get('/list', [\App\Http\Controllers\RefSubKelompokKeahlianController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::get('/new', [\App\Http\Controllers\RefSubKelompokKeahlianController::class, 'create'])->name('create')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::post('/store', [\App\Http\Controllers\RefSubKelompokKeahlianController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::post('/update/{id}', [\App\Http\Controllers\RefSubKelompokKeahlianController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            });

            Route::group(['prefix' => 'dosen-with-kk', 'as' => 'dosen-with-kk.'], function () {
                Route::get('/list', [\App\Http\Controllers\DosenHasKKController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true}'])->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::get('/new', [\App\Http\Controllers\DosenHasKKController::class, 'new'])->name('new')->middleware(['admin:{"is_admin":true}'])->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::post('/store', [\App\Http\Controllers\DosenHasKKController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true}'])->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::get('/lepas-dosen/{DosenHasKK_id}', [\App\Http\Controllers\DosenHasKKController::class, 'lepas_dosen'])->name('lepas-dosen')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::get('/struktur/', [DosenHasKKController::class, 'struktur'])->name('struktur')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true|"is_dosen":true}']);
                Route::get('/table/', [DosenHasKKController::class, 'table'])->name('table')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::get('/riwayat/{id_user}', [DosenHasKKController::class, 'riwayat'])->name('riwayat')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
                Route::get('/pasang-kembali-dosen/{DosenHasKK_id}', [\App\Http\Controllers\DosenHasKKController::class, 'Aktifkan_Pemetaan'])->name('pasang-kembali-dosen')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_kk":true}']);
            });

            // COE (Center of Excellence) Routes

        });

        // Studi Lanjut Routes
        Route::group(['prefix' => 'studi-lanjut', 'as' => 'studi-lanjut.'], function () {
            Route::get('/list', [\App\Http\Controllers\StudiLanjutController::class, 'index'])->name('list')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_dosen":true}']);
            Route::get('/input', [\App\Http\Controllers\StudiLanjutController::class, 'create'])->name('input')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_dosen":true}']);
            Route::post('/store', [\App\Http\Controllers\StudiLanjutController::class, 'store'])->name('store')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_dosen":true}']);
            Route::get('/view/{id}', [\App\Http\Controllers\StudiLanjutController::class, 'show'])->name('view')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_dosen":true}']);
            Route::get('/edit/{id}', [\App\Http\Controllers\StudiLanjutController::class, 'edit'])->name('edit')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_dosen":true}']);
            Route::post('/update/{id}', [\App\Http\Controllers\StudiLanjutController::class, 'update'])->name('update')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_dosen":true}']);
            // Route::delete('/destroy/{id}', [\App\Http\Controllers\StudiLanjutController::class, 'destroy'])->name('destroy')->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"is_dosen":true}']);
        });
    });

    // Kinerja Pegawai Routes (Prefix: /kinerja_pegawai)
    Route::group(['prefix' => 'kinerja_pegawai', 'as' => 'manage.', 'middleware' => ['admin:{"is_admin":true|"and":{"bagian":"sumber daya manusia"|"range-level":[3|5]}|"range-level":[2|3]|"is_dosen":true|"is_tpa":true}']], function () {
        // Main Dashboard
        Route::get('/', [\App\Http\Controllers\KinerjaDashboardController::class, 'index'])->name('target-kinerja.index');

        // Presensi & Jam Kerja
        Route::group(['prefix' => 'presensi', 'as' => 'presensi.'], function () {
            Route::get('/', [\App\Http\Controllers\PresensiController::class, 'index'])->name('index');
            Route::get('/tardiness', [\App\Http\Controllers\PresensiController::class, 'tardinessReport'])->name('tardiness');
            Route::get('/settings', [\App\Http\Controllers\PresensiController::class, 'settings'])->name('settings');
            Route::post('/settings', [\App\Http\Controllers\PresensiController::class, 'updateSettings'])->name('settings.update');
        });

        // Target Kinerja Sub-Routes
        Route::group(['as' => 'target-kinerja.'], function () {
            // CRUD Satuan Ukur
            Route::get('/ref-satuan', [\App\Http\Controllers\RefSatuanController::class, 'index'])->name('ref-satuan.index');
            Route::post('/ref-satuan', [\App\Http\Controllers\RefSatuanController::class, 'store'])->name('ref-satuan.store');
            Route::put('/ref-satuan/{id}', [\App\Http\Controllers\RefSatuanController::class, 'update'])->name('ref-satuan.update');
            Route::delete('/ref-satuan/{id}', [\App\Http\Controllers\RefSatuanController::class, 'destroy'])->name('ref-satuan.destroy');

            Route::get('/list', [\App\Http\Controllers\TargetKinerjaController::class, 'index'])->name('list');
            Route::get('/detail/{id}', [\App\Http\Controllers\KinerjaDashboardController::class, 'targetDetail'])->name('detail');
            Route::get('/input', [\App\Http\Controllers\TargetKinerjaController::class, 'create'])->name('input');
            Route::post('/store', [\App\Http\Controllers\TargetKinerjaController::class, 'store'])->name('store');
            Route::get('/view/{id}', [\App\Http\Controllers\TargetKinerjaController::class, 'show'])->name('view');
            Route::get('/edit/{id}', [\App\Http\Controllers\TargetKinerjaController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [\App\Http\Controllers\TargetKinerjaController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [\App\Http\Controllers\TargetKinerjaController::class, 'destroy'])->name('destroy');
            Route::get('/assign/{id}', [\App\Http\Controllers\TargetKinerjaController::class, 'assign'])->name('assign');
            Route::post('/assign/{id}', [\App\Http\Controllers\TargetKinerjaController::class, 'storeAssignment'])->name('store-assignment');
            Route::post('/assign/{id}/pegawai/{userId}/status', [\App\Http\Controllers\TargetKinerjaController::class, 'updateAssignmentStatus'])->name('update-assignment-status');
            Route::delete('/assign/{id}/pegawai/{userId}', [\App\Http\Controllers\TargetKinerjaController::class, 'detachPegawai'])->name('detach-pegawai');
            Route::get('/laporan', [\App\Http\Controllers\TargetKinerjaController::class, 'laporan'])->name('laporan');

            // Target Kinerja Harian
            Route::group(['prefix' => 'harian', 'as' => 'harian.'], function () {
                Route::get('/get-induk-kpi', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'getIndukKpiByResponsibility'])->name('get-induk-kpi');
                Route::get('/list', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'index'])->name('list');
                Route::get('/input', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'create'])->name('input');
                Route::get('/edit/{id}', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'edit'])->name('edit');
                Route::post('/store', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'store'])->name('store');
                Route::put('/update/{id}', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'update'])->name('update');
                Route::get('/view/{id}', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'show'])->name('view');
                Route::delete('/destroy/{id}', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/isi', [\App\Http\Controllers\PelaporanPekerjaanController::class, 'create'])->name('isi');
                Route::post('/{id}/submit-report', [\App\Http\Controllers\PelaporanPekerjaanController::class, 'store'])->name('submit-report');
                Route::get('/{id}/assign', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'assign'])->name('assign');
                Route::post('/{id}/assign', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'storeAssignment'])->name('store-assignment');
                Route::post('/{id}/assign/pegawai/{userId}/status', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'updateAssignmentStatus'])->name('update-assignment-status');
                Route::delete('/{id}/assign/pegawai/{userId}', [\App\Http\Controllers\TargetKinerjaHarianController::class, 'detachPegawai'])->name('detach-pegawai');
                Route::get('/reports', [\App\Http\Controllers\PelaporanPekerjaanController::class, 'approvalList'])->name('reports');
                Route::get('/reports/{id}/approval', [\App\Http\Controllers\PelaporanPekerjaanController::class, 'showApproval'])->name('reports.approval');
                Route::post('/reports/{id}/approve', [\App\Http\Controllers\PelaporanPekerjaanController::class, 'approve'])->name('reports.approve');
            });
        });

        // Additional Dashboard & Preview Routes (Merged from old kinerja group)
        Route::get('/base', function () {
            return view('kinerja_pegawai.base');
        })->name('base');
        Route::get('/sidebar-preview', function () {
            return view('kinerja_pegawai.sidebar');
        })->name('sidebar-preview');
        Route::get('/dashboard/fakultas', function () {
            return view('kinerja_pegawai.dashboard_fakultas.index');
        })->name('dashboard.fakultas.index');
        Route::get('/dashboard/fakultas/{id?}', function ($id = null) {
            return view('kinerja_pegawai.dashboard_fakultas.detail', ['id' => $id]);
        })->name('dashboard.fakultas.detail');
        Route::get('/dashboard/fakultas/input/{id?}', function ($id = null) {
            return view('kinerja_pegawai.dashboard_fakultas.input', ['id' => $id]);
        })->name('dashboard.fakultas.input');
        Route::get('/dashboard/target', function () {
            return view('kinerja_pegawai.dashboard_target.input');
        })->name('dashboard.target.input');
        Route::get('/dashboard/target/{action}/{id?}', function ($action, $id = null) {
            $action = in_array($action, ['approval', 'edit', 'input']) ? $action : 'detail';
            return view("kinerja_pegawai.dashboard_target.$action", ['id' => $id]);
        })->where('action', 'approval|edit|input')->name('dashboard.target.action');

        // Export Routes
        Route::get('/laporan/export', [\App\Http\Controllers\KinerjaExportController::class, 'export'])->name('laporan.export');
        Route::get('/laporan/print', [\App\Http\Controllers\KinerjaExportController::class, 'exportPrint'])->name('laporan.print');

        // Monitoring Route (Fitur 2G2)
        Route::get('/monitoring', [\App\Http\Controllers\KinerjaDashboardController::class, 'monitoring'])->name('monitoring.index');

        // Laporan Output & Analytics (Phase 4)
        Route::get('/laporan-efektivitas', [\App\Http\Controllers\PelaporanPekerjaanController::class, 'laporanIndividual'])->name('laporan.efektivitas');
        Route::get('/reporting', [\App\Http\Controllers\PelaporanPekerjaanController::class, 'reporting'])->name('laporan.reporting');
        Route::get('/laporan-capaian-tw', [\App\Http\Controllers\TargetKinerjaController::class, 'laporanCapaian'])->name('laporan.capaian-tw');

        Route::get('/laporan/target/{id?}', function ($id = null) {
            return view('kinerja_pegawai.laporan_target.detail', ['id' => $id]);
        })->name('laporan.target.detail');

        // Role Switcher
        Route::get('/switch-role/{role_name}', [\App\Http\Controllers\TestingSIMDKController::class, 'switchRole'])
            ->name('switch-role')
            ->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"}']);
        Route::get('/leave-impersonate', [\App\Http\Controllers\TestingSIMDKController::class, 'leaveImpersonate'])
            ->name('leave-impersonate');
    });

    Route::group([
        'prefix' => 'dupak',
        'as' => 'dupak.',
    ], function () {
        // catatan:
        // - pengajuan: hanya dosen/pemilik (di controller)
        // - validasi/flagging: hanya TPAK yang ditunjuk (di ValidasiController via penunjukan_tpak)
        // - penunjukan-tpak: dibatasi admin/SDM agar hanya TPAK tertentu yang bisa ditugaskan

        // Route::get('/notifikasi/{id}/read', [App\Http\Controllers\DupakNotifikasiController::class, 'markAsReadAndRedirect'])
        // ->name('dupak.notifikasi.read');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Pengajuan DUPAK
        Route::resource('pengajuan', PengajuanController::class);
        Route::post('pengajuan/{id}/submit', [PengajuanController::class, 'submit'])
            ->name('pengajuan.submit');

        // Riwayat DUPAK
        Route::resource('riwayat', RiwayatController::class)
            ->only(['index', 'show']);

        // Validasi DUPAK (for admin/validator)
        Route::resource('validasi', ValidasiController::class)
            ->only(['index', 'show', 'update', 'store']);

        Route::post('validasi/{pengajuan}/detail/{detail}/save', [ValidasiController::class, 'saveDetail'])
            ->name('validasi.detail.save');

        // Rute Dinamis untuk Input Form Detail Pengajuan
        Route::get('detil_pengajuan/{category}/{id}', [DetilPengajuanController::class, 'showForm'])
            ->name('dupak.detil_pengajuan.form');

        // Rute untuk menyimpan data detail pengajuan
        Route::post('detil_pengajuan/{category}/{id}', [DetilPengajuanController::class, 'store'])
            ->name('dupak.detil_pengajuan.store');

        // Pengisian Detil Formulir Pengajuan (Resource ditaruh setelah rute spesifik)
        // Route::resource('detil_pengajuan', \App\Http\Controllers\Dupak\DetilPengajuanController::class);

        // route penunjukan_tpak, tanpa id, karena sistemnya SDM akan menunjuk TPAK berdasarkan kebutuhan, bukan berdasarkan pengajuan tertentu
        // Hapus "is_dosen":true dan "is_tpa":true jika Dosen/TPA biasa TIDAK boleh mengelola penunjukan TPAK
        Route::group(['prefix' => 'penunjukan-tpak', 'as' => 'penunjukan_tpak.', 'middleware' => ['admin:{"is_admin":true|"and":{"bagian":"sumber daya manusia"|"range-level":[3|5]}|"range-level":[2|3]}']], function () {
            Route::get('/', [PenunjukanTPAKController::class, 'index'])->name('index');
            Route::post('/', [PenunjukanTPAKController::class, 'store'])->name('store');
            Route::delete('/{id}', [PenunjukanTPAKController::class, 'destroy'])->name('destroy');
            Route::get('/tambah_tpak_baru', [PenunjukanTPAKController::class, 'create_new_tpak'])->name('create_new_tpak');
        });
        // ->middleware(['admin:{"is_admin":true|"bagian":"sumber daya manusia"|"range-level":[3|5]}']);

        Route::get(
            '/eligibilitas_dosen',
            [DashboardController::class, 'eligibilitas']
        )->name('eligibilitas');
    });
});

// Admin Routes
Route::middleware(['auth', 'admin:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
});



require __DIR__ . '/auth.php';
