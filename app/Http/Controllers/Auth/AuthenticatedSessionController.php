<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Dosen;
use App\Models\Pengawakan;
// use App\Models\TestingSIMDK;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {

            // $cek1 = session('account');

            // Attempt authentication
            $request->authenticate();
            // dd($request->authenticate());

            // $cek2 = session('account');

            // regenerate session
            $request->session()->regenerate();

            // $cek3 = session('account');

            $user = Auth::user();

            // dd($save);

            // $cek4 = session('account');

            // dd($cek1,$cek2,$cek3,$cek4,'cek ini');
            // dd($user,$user->is_active==0);

            if (! $user) {

                Log::error('No user after authentication');
                session()->flush();

                return redirect()->route('login');
            } elseif ($user->is_active == 0) {
                Log::error('User yang dinonaktifkan Mencoba Login');
                session()->flush();

                return redirect()->route('login')->with('error_alert', 'Akun anda sudah dinonaktifkan! Silahkan menghubungi pihak SDM apabila ada yang dibutuhkan!.');
            }

            $type = strtolower(\App\Models\Tpa::where('users_id', $user->id)->exists() ? 'TPA' : 'Dosen');
            $role = [];
            if ($type == 'tpa') {
                $role['is_tpa'] = true;
            } else {
                $role['is_dosen'] = true;

            }
            // dd($user);
            if ($user->is_admin == 1) {
                $role['is_admin'] = true;
            }

            $active_bagian = Pengawakan::with(['formasi.level_id', 'formasi.bagian'])->where('users_id', $user->id)
                ->where(function ($query) {
                    $query->where('tmt_selesai', '>=', Carbon::now())
                        ->orWhereNull('tmt_selesai');
                })
                ->get();
            // dd($active_bagian!=null);
            if ($active_bagian != null) {
                foreach ($active_bagian as $item) {
                    $role[strtolower($item->formasi->bagian->position_name)] = ['level' => strtolower($item->formasi->level_id->urut)];
                }
            }

            $max_level = $active_bagian->sortBy(function ($item) {
                return $item->formasi->level_id->urut ?? 0;
            })
                ->first();

            if ($max_level) {
                $role['top-level'] = strtolower($max_level->formasi->level_id->urut);
            }

            $dosen = Dosen::where('users_id', $user->id)->first();
            $userArray = $user->toArray();
            $userArray['dosen_id'] = $dosen ? $dosen->id : null;

            $sessionData = array_merge(
                $userArray,
                ['role' => $role]
            );

            // $sessionData = $role;

            session(['account' => $sessionData]);
            // dd($user->id);
            $save = DB::table('sessions')
                ->where('id', session()->getId())
                ->update([
                    'user_id' => $user->id,
                ]);
            // dd(session('account'));

            Log::info('Login successful', [
                'user_id' => $user->id,
                'session_id' => session()->getId(),
            ]);

            $route = null;
            $sidebar = BuildSidebar();
            // dump($sidebar);
            session([
                'sidebar-simdk' => $sidebar,
            ]);

            // dd(session)
            // dump($sidebar);
            if ($user->is_new == 1) {
                Log::info('Redirecting to change password for new user.');
                $route = redirect(route('profile.change-password', ['idUser' => session('account')['id']]))->with('message', 'Karena akun Anda baru dibuat, silakan ubah kata sandi Anda terlebih dahulu demi keamanan akun Anda.');
            } else {
                Log::info('Pengguna telah berhasil login ke sistem 2.');
                $route = redirect()->route('dashboard')
                    ->withCookie(cookie()->forever('auth_check', true))->with('message', 'Login Berhasil!');
            }

            return $this->CekReview($route, '1C1', 'Login');
        } catch (\Exception $e) {

            Log::error('Login exception', ['error' => $e->getMessage()]);

            return redirect()->route('login')
                ->withErrors([
                    // 'email_institusi' => 'Login failed',
                    $e->getMessage(),
                ])
                ->withInput();
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('login'))->with('message','Logout Berhasil');
    }
}
