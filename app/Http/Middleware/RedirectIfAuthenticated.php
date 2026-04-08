<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Log::info('User is already authenticated, redirecting to home', [
                    'user_id' => Auth::guard($guard)->id(),
                    'route' => RouteServiceProvider::HOME
                ]);
                return redirect(RouteServiceProvider::HOME);
            }
        }

        Log::info('User is not authenticated, continuing request', [
            'path' => $request->path(),
            'method' => $request->method()
        ]);

        return $next($request);
    }

    
}
