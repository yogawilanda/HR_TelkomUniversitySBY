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

            // 3. Safety check: Jika keduanya tidak ada, baru gagalkan test
            if (! $user) {
                $this->fail('User tidak ditemukan. Pastikan database memiliki akun Admin atau Dosen.');
            }

            $browser->loginAs($user)
                ->visit('/dupak/dashboard')
                ->waitForText('DUPAK', 10)
                ->assertSee('DUPAK')

                    // 1. Klik tombol Buat Pengajuan Baru
                    // Bisa menggunakan ->clickLink('Buat Pengajuan Baru') jika itu berupa link teks
                    // Atau ->press('Buat Pengajuan Baru') jika itu berupa tombol
                ->press('Buat Pengajuan Baru')
                //     ->clickLink('Buat Pengajuan Baru')

                    // 2. Tunggu sampai halaman form terbuka
                ->waitForText('Form Pengajuan DUPAK', 10) // Sesuaikan dengan teks di header form kamu

                //     // 3. Mulai isi form (Contoh: memilih masa penilaian atau kategori)
                //     // Sesuaikan 'name' input dengan yang ada di Blade kamu
                //     ->type('masa_awal', '2024-01-01')
                //     ->type('masa_akhir', '2024-06-30')

                    // Jika ada dropdown (select)
                ->select('kategori_kegiatan', 'Pendidikan')
                ->pause(1000)
                ->screenshot('isi-form-dupak')

                    // 4. Submit Form
                ->press('Simpan') // Sesuaikan teks tombol submit kamu

                    // 5. Validasi sukses
                ->waitForText('Pengajuan berhasil disimpan', 10)
                ->screenshot('pengajuan-berhasil');

            // $browser->loginAs($user)
            //     ->visit('/dupak/dashboard')
            //     ->waitForText('DUPAK', 10)
            //     ->pause(2000)
            //     ->assertSee('DUPAK')
            //     // Tambahkan Prosedur Menambahkan Pengajuan dengan klik tombol "buat pengajuan baru"

            //     ->screenshot('akses-dashboard-dupak');
        });
    }
}
