<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MembuatDetailPengajuanDupakTest extends DuskTestCase
{
    public function test_dosen_mengajukan_pengajuan_dupak(): void
    {
        $this->browse(function (Browser $browser) {
            // Ambil user dosen secara random
            $user = User::where('tipe_pegawai', 'Dosen')
                ->inRandomOrder()
                ->first();

            if (!$user) {
                $this->fail('User dengan tipe_pegawai Dosen tidak ditemukan di database.');
            }

            $browser->loginAs($user)
                ->visit('/dupak/dashboard')
                ->waitForText('DUPAK', 10)
                ->assertSee('DUPAK')
                // ->pause('3')
                
                // 1. Klik tombol Buat Pengajuan Baru
                ->clickLink('Buat Pengajuan Baru')
                
                // 2. Tunggu sampai halaman form terbuka
                ->waitForText('Formulir Pengajuan DUPAK', 5) 

                // 3. Simpan header pengajuan (asumsi form awal langsung simpan)
                ->pause(1000)
                ->press('Simpan')

                // 4. Pastikan masuk ke halaman detail pengajuan
                ->waitForText('Detail Pengajuan DUPAK', 5)
                ->assertSee('Detail Pengajuan DUPAK')

                // 5. Klik tombol Tambahkan Kegiatan (untuk buka modal)
                ->press('Tambahkan Kegiatan')

                // 6. Pastikan Pop up/Modal terbuka
                ->waitForText('Tambah Kegiatan Baru (Dupak)', 5)
                ->assertSee('Tambah Kegiatan Baru (Dupak)')

                // 7. Pilih Kategori secara Random via JavaScript
                ->waitForSelector('select[name="kategori_id"]')
                ->script([
                    "let select = document.querySelector('select[name=\"kategori_id\"]');
                     let options = Array.from(select.options).filter(o => o.value !== '');
                     if(options.length > 0) {
                        let randomOption = options[Math.floor(Math.random() * options.length)];
                        select.value = randomOption.value;
                        select.dispatchEvent(new Event('change'));
                     }"
                ])

                // Jeda 2 detik untuk memberi waktu Livewire/AJAX memuat Sub-Kategori
                ->pause(2000)

                // 8. Pilih Sub-Kategori secara Random via JavaScript
                ->waitForSelector('select[name="sub_kategori_id"]')
                ->script([
                    "let selectSub = document.querySelector('select[name=\"sub_kategori_id\"]');
                     let optionsSub = Array.from(selectSub.options).filter(o => o.value !== '' && !o.disabled);
                     if(optionsSub.length > 0) {
                        let randomSub = optionsSub[Math.floor(Math.random() * optionsSub.length)];
                        selectSub.value = randomSub.value;
                        selectSub.dispatchEvent(new Event('change'));
                     }"
                ])

                // 9. Klik Simpan Kegiatan di dalam modal
                ->press('Simpan Kegiatan')
                
                // 10. Verifikasi sukses
                ->waitUntilMissingText('Tambah Kegiatan Baru (Dupak)', 10)
                ->assertSee('Berhasil'); 
        });
    }
}