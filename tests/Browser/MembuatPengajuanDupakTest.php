<?php

namespace Tests\Browser;

use App\Models\User; // Penting: Import model User
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MembuatPengajuanDupakTest extends DuskTestCase
{
    public function test_dosen_mengajukan_pengajuan_dupak(): void
    {
        $this->browse(function (Browser $browser) {

            $user = User::where('tipe_pegawai', 'Dosen')
                ->inRandomOrder()
                ->first();

            if (! $user) {
                $this->fail('User tidak ditemukan.');
            }

            $browser->loginAs($user)
                ->visit('/dupak/dashboard')
                ->waitForText('DUPAK', 10)
                ->assertSee('DUPAK')
                // 1. Klik tombol Buat Pengajuan Baru
                ->clickLink('Buat Pengajuan Baru')
                
                // 2. Tunggu sampai halaman form terbuka
                ->waitForText('Formulir Pengajuan DUPAK', 3) 

                // 3. Mengisi Data Form (Lengkapi sesuai name di Blade kamu)
                // Contoh mengisi range tanggal:
                ->pause(1000)

                // 4. Submit Form
                // Jika tombolnya x-primary-button dengan teks "Simpan", pakai press
                ->press('Simpan');
        });
    }
}
