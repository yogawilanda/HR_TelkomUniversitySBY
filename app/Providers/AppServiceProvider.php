<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth; // <-- Pastikan ini di-import
use App\Models\Dupak\NotifikasiDupakModel; // <-- Pastikan ini di-import

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ middleware tetap
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);

        // ✅ migrations tetap
        $this->loadMigrationsFrom(database_path('migrations/default'));
        $this->loadMigrationsFrom(database_path('migrations/dupak'));

        // ===============================
        // ✅ SIDEBAR LOGIC
        // ===============================
        View::composer('kelola_data.sidebar', function ($view) {
            // dd(session('sidebar-simdk', []));
            $view->with('sidebars', session('sidebar-simdk', []));
        });


        // ===============================
        // ✅ NOTIFIKASI DUPAK (NAVBAR/LAYOUT)
        // ===============================
        // Sesuaikan 'layouts.navbar' dengan nama file blade navbar kamu
        View::composer(['layouts.navigation', 'dupak.dashboard'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $notifications = NotifikasiDupakModel::where('notifiable_id', $user->id)
                    ->where('notifiable_type', get_class($user))
                    ->latest()
                    ->take(3)
                    ->get();

                $unreadCount = NotifikasiDupakModel::where('notifiable_id', $user->id)
                    ->where('notifiable_type', get_class($user))
                    ->whereNull('read_at')
                    ->count();

                $view->with([
                    'notifications' => $notifications,
                    'unreadCount' => $unreadCount,
                ]);
            }
        });


    }

    
}
