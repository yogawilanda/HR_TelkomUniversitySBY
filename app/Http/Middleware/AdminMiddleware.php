<?php

// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Jangan lupa import Auth
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN user adalah admin
        if (Auth::check() && Auth::user()->is_admin) {
            // Jika ya, lanjutkan request
            return $next($request);
        }

        // Jika tidak, redirect ke halaman home dengan pesan error
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    
}
