<?php

namespace Tests\Browser;

// <!-- AksesDashboardDupakTest.php -->
// <!-- Akses Dashboard dupak test setelah login berhasil -->

use App\Models\User; // Penting: Import model User
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AksesDashboardUserDupakTest extends DuskTestCase
{
   public function test_halaman_dupak_bisa_diakses(): void
{
    $this->browse(function (Browser $browser) {
        
        $user = User::where('tipe_pegawai', 'Dosen')
                    ->inRandomOrder()
                    ->first();

        // 3. Safety check: Jika keduanya tidak ada, baru gagalkan test
        if (!$user) {
            $this->fail('User tidak ditemukan. Pastikan database memiliki akun Admin atau Dosen.');
        }

        $browser->loginAs($user)
            ->visit('/dupak/dashboard')
            ->waitForText('DUPAK', 10)
            ->pause(2000)
            ->assertSee('DUPAK')
            // Tambahkan screenshot untuk bukti laporan skripsi
            ->screenshot('akses-dashboard-dupak'); 
    });
}
}
