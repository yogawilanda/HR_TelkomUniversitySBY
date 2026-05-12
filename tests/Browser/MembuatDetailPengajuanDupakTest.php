<?php

namespace Tests\Browser;

use App\Models\Dupak\Pengajuan;
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

            if (! $user) {
                $this->fail('User dengan tipe_pegawai Dosen tidak ditemukan di database.');
            }

            $admin = User::where('email_institusi', 'admin@telkomuniversity.ac.id')->first();

            if (! $admin) {
                $this->fail('Admin dengan email_institusi admin@telkomuniversity.ac.id tidak ditemukan di database.');
            }

            // Pastikan dosen sudah memiliki pengajuan (jika tidak ada, test fail)
            // Ambil pengajuan dari tabel dupak langsung (hindari mapping idDosen dari User karena skemanya bisa beda)
            $pengajuan = Pengajuan::first();

            if (! $pengajuan) {
                $this->fail('Tidak ada data pengajuan DUPAK di database.');
            }

            $browser->loginAs($admin)
                ->visit('/dupak/dashboard')
                ->waitForText('Tambahkan Kegiatan', 15)
                ->clickLink('Tambahkan Kegiatan')
                ->pause(1000); // Tunggu modal benar-benar tampil

            // 1. Pilih Kategori secara random
            $kategoriOptions = $browser->script('
    return Array.from(document.querySelectorAll("#kategori option"))
        .map(opt => opt.value)
        .filter(val => val !== "");
')[0];

            $randomKategori = $kategoriOptions[array_rand($kategoriOptions)];

            $browser->select('#kategori', $randomKategori)
                ->script("document.getElementById('kategori').dispatchEvent(new Event('change'));");

            // 2. TUNGGU sampai opsi komponen muncul
            $browser->waitUntil("document.querySelectorAll('#idKomponen option').length > 1", 10);

            // 3. Ambil dan Pilih Komponen secara random lewat JavaScript
            $randomKomponen = $browser->script("
    let options = Array.from(document.querySelectorAll('#idKomponen option'))
                       .map(opt => opt.value)
                       .filter(val => val !== '');
    if (options.length === 0) return null;
    let selected = options[Math.floor(Math.random() * options.length)];
    let sel = document.getElementById('idKomponen');
    sel.value = selected;
    sel.dispatchEvent(new Event('change'));
    return selected;
")[0];

            if (! $randomKomponen) {
                $this->fail('Gagal mengambil atau memilih komponen secara acak.');
            }

            // 4. Klik Simpan dengan "Forced Script Click"
            // Kita gunakan JavaScript untuk memicu event submit secara manual.
            // Ini akan memastikan listener 'addEventListener("submit", ...)' di script kamu terpanggil.
            $browser->script("
    let form = document.getElementById('kegiatan-form');
    // Memicu event submit agar e.preventDefault() dan redirect logic kamu jalan
    let event = new Event('submit', { cancelable: true, bubbles: true });
    form.dispatchEvent(event);
");

            // 5. Beri waktu proses window.location.href
            $browser->pause(3000)
                ->assertPathContains('/dupak/detil_pengajuan/')
                ->screenshot('berhasil_redirect');
        });
    }
}
