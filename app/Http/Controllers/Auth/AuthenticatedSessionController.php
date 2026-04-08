<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
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

            // $cek4 = session('account');

            // dd($cek1,$cek2,$cek3,$cek4,'cek ini');

            if (!$user) {

                Log::error('No user after authentication');

                return redirect()->route('login');
            }

            $role = \App\Models\Tpa::where('users_id', $user->id)->exists() ? 'TPA' : 'Dosen';

            $sessionData = array_merge(
                $user->toArray(),
                ['role' => [$role]]
            );

            session(['account' => $sessionData]);

            Log::info('Login successful', [
                'user_id' => $user->id,
                'session_id' => session()->getId()
            ]);
            // dd($user,$user->is_new==true ,$user->is_new===1,$user->is_new==1);
            if ($user->is_new == true || $user->is_new === 1 || $user->is_new == 1) {
                return redirect(route('profile.change-password', ['idUser' => session('account')['id']]))->with('message', 'Karena akun Anda baru dibuat, silakan ubah kata sandi Anda terlebih dahulu demi keamanan akun Anda.');
            } else {
                return redirect()->intended(route('home'))
                    ->with('message', 'Login Berhasil!')
                    ->withCookie(cookie()->forever('auth_check', true));
            }
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

        return redirect('/');
    }
}
