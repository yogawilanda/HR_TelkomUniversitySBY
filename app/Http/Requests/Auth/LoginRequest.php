<?php

namespace App\Http\Requests\Auth;

use App\Models\Dosen;
use App\Models\Tpa;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email_institusi' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $validated = $this->validated();

        Log::info('Login attempt', ['email' => $validated['email_institusi']]);

        // Try to find the user first
        $user = \App\Models\User::where('email_institusi', $validated['email_institusi'])->first();

        if (!$user) {

            Log::error('User not found', ['email' => $validated['email_institusi']]);

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email_institusi' => trans('auth.failed'),
            ]);
        }

        Log::info('Found user', ['id' => $user->id]);

        // cek email verified dulu
        if ($user->email_verified_at == null) {

            Log::error('Email not verified', ['email' => $validated['email_institusi']]);

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email_institusi' => 'email pribadi belum terverifikasi',
            ]);
        }

        if (!Auth::attempt($validated, false)) {

            Log::error('Password verification failed', ['email' => $validated['email_institusi']]);

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email_institusi' => trans('auth.failed'),
            ]);
        }

        Log::info('Authentication successful', ['user_id' => $user->id]);

        if ($user->email_verified_at != null) {

            if ($user = Auth::user()) {

                $role = Tpa::where('users_id', $user->id)->exists() ? 'TPA' : 'Dosen';

                session([
                    'account' => array_merge(
                        $user->toArray(),
                        ['role' => [$role]]
                    )
                ]);

                Log::info('Session data set', [
                    'user_id' => $user->id,
                    'role' => $role,
                    'session_id' => session()->getId()
                ]);
            }

            RateLimiter::clear($this->throttleKey());
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email_institusi' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        $email = $this->validated()['email_institusi'];

        return Str::transliterate(
            Str::lower($email) . '|' . request()->ip()
        );
    }
}