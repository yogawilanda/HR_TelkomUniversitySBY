<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Carbon\Carbon;
// use App\Models\User;

use Illuminate\View\View;

class AllAboutAuthController extends Controller
{
    public function send_verify($email_institusi)
    {
        try {
            $user = User::where('email_institusi', $email_institusi)->first();
            if ($user != null) {
                $otp = (string) random_int(100000, 999999);
                $user->verified_code = $otp;
                Mail::to($user->email_pribadi)->send(new SendEmail($otp));
                $user->save();
                $email_pribadi = $this->mask_email($user->email_pribadi);

                return response()->json(['success' => true, 'data' => ['Berhasil membuat kode verifikasi', $email_pribadi]], 200);
            } else {
                throw new \Exception('Tidak ada akun dengan email institusi tersebut');
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function go_to_verify_page(Request $request)
    {
        try {
            $send_code = $this->send_verify($request->email_institusi);
            if ($send_code->getStatusCode() == 200) {
                $data_return = $send_code->getData(true);
                // $user = User::where('')
                // dd($data_return['data'][1]);
                return view('auth.verify-email-code', ['email_pribadi' => $data_return['data'][1]])->with('message', 'Kode Verifikasi sudah berhasil dikirim ke email pribadi');
            } else {
                // dd($send_code->getData(true), 'cek masuk else');
                throw new \Exception('Email Institusi yang anda masukkan sepertinya salah atau tidak terdaftar di sistem kami!');
            }
        } catch (\Exception $e) {
            // dd( 'cek masuk catch');
            // return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function verifiy_code(Request $request)
    {
        try {
            $validated = $request->validate([
                'email_institusi' => 'required|email',
                'otp' => 'required',
            ]);

            // Cari user
            $user = User::where('email_institusi', $request->email_institusi)->first();

            if (!$user) {
                // Pakai redirect dengan error dan old input
                // return back()->withErrors(['email_institusi' => 'Email tidak ditemukan.'])
                //     ->withInput();
                // throw new \Exception('');
                throw new \Exception('Kode Validasi Tidak Sesuai! ');

                // throw ValidationException::withMessages([
                //     'Kode Validasi Tidak Sesuai! '
                // ]);
            }

            if ($user->verified_code === $request->otp) {
                $user->email_verified_at = Carbon::now();
                $user->verified_code = null;
                $user->save();
                return redirect('login')->with('message', 'Email berhasil divalidasi, silahkan login kembali');
            } else {
                throw new \Exception('Kode Validasi Tidak Sesuai! ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function validation(Request $request)
    {
        return $request->validate(
            [
                'email_institusi' => [
                    'required',
                    'email',
                    'max:100',
                ],
                'otp' => [
                    'required',
                    'string',
                    'size:6',          // tepat 6 karakter
                    'regex:/^\d{6}$/', // hanya angka 0-9
                ],
            ],
            [
                'required' => ':attribute wajib diisi.',
                'max'      => ':attribute maksimal :max karakter.',
                'string'   => ':attribute harus berupa text.',
                'email.email' => 'Format Email Kontak Darurat tidak valid.',
                'size'     => ':attribute harus tepat :size karakter.',
                'regex'    => ':attribute harus berupa 6 digit angka.',
            ]
        );
    }

    public function mask_email($email)
    {
        // Pisahkan menjadi username dan domain
        list($user, $domain) = explode('@', $email);

        // Masking username: huruf pertama + ** + huruf terakhir (jika panjang > 2)
        $user_masked = strlen($user) > 2
            ? $user[0] . str_repeat('*', strlen($user) - 2) . $user[strlen($user) - 1]
            : $user;

        // Pisahkan domain menjadi nama domain dan ekstensi
        $domain_parts = explode('.', $domain);
        $domain_name = $domain_parts[0];
        $domain_ext = $domain_parts[1];

        // Masking domain name: huruf pertama + **** + huruf terakhir (jika panjang > 2)
        $domain_name_masked = strlen($domain_name) > 2
            ? $domain_name[0] . str_repeat('*', strlen($domain_name) - 2) . $domain_name[strlen($domain_name) - 1]
            : $domain_name;

        // Gabungkan kembali
        return $user_masked . '@' . $domain_name_masked . '.' . $domain_ext;
    }
}
