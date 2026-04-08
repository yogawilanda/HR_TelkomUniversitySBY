<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticationPersists
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Ensure we have the necessary session data
            if (!Session::has('account')) {
                $role = \App\Models\Tpa::where('users_id', $user->id)->exists() ? 'TPA' : 'Dosen';
                Session::put('account', array_merge($user->toArray(), ['role' => [$role]]));
                Session::save();
            }

            // Add auth check headers
            $response = $next($request);
            return $response->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        }

        return $next($request);
    }

    
}
