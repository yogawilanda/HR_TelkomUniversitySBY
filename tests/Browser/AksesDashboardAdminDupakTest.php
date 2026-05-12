<?php

namespace Tests\Browser;

// <!-- AksesDashboardDupakTest.php -->
// <!-- Akses Dashboard dupak test setelah login berhasil -->

use App\Models\User; // Penting: Import model User
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AksesDashboardAdminDupakTest extends DuskTestCase
{
    public function test_halaman_dupak_bisa_diakses(): void
    {
        $this->browse(function (Browser $browser) {
            // Ambil user pertama yang ada di database (siapa saja)
            //   $user = \App\Models\User::first();

            // Cari user spesifik berdasarkan email institusi
            $user = User::where('email_institusi', 'admin@telkomuniversity.ac.id')->first();

            // Cek dulu apakah user ada, kalau tidak ada kita gagalkan dengan pesan jelas
            if (! $user) {
                $this->fail('Tidak ada user di database. Jalankan php artisan db:seed dulu!');
            }

            $browser->loginAs($user)
                ->visit('/dupak/dashboard') // Pastikan route ini benar
                ->waitForText('DUPAK', 10)
                ->pause(2000)
                ->assertSee('DUPAK');
        });
    }
}
