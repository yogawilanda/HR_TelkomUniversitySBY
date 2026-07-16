# TODO - Seeder DUPAK

- [ ] Buat folder `database/seeders/DupakSeeder/` atau penyesuaian nama folder sesuai kebutuhan (tetap pakai CapitalSentence)
- [ ] Buat/port seeder: `PengajuanSeeder`, `DetailPengajuanSeeder`, `PenunjukanTpakSeeder`, `HasilEvaluasiSeeder` ke folder `database/seeders/Dupak/<nama_seeder>` (sesuai aturan pengguna)
- [ ] Pastikan namespace sesuai PSR-4 terhadap folder: `Database\\Seeders\\Dupak`
- [x] Update `database/seeders/DatabaseSeeder.php` agar memanggil seeder-seeder DUPAK yang baru setelah `DupakUserSeeder`
- [ ] Jalankan `php artisan db:seed` untuk verifikasi

