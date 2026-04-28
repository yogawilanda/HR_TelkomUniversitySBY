# TODO: Validasi Penilaian TPAK per Detail Pengajuan

## Status Saat Ini

### Yang Sudah Ada
- Tabel `hasil_evaluasi` (evaluations) sudah ada di database
- Model `HasilEvaluasi` sudah ada dengan field: `detail_pengajuan_id`, `idUserPemeriksa`, `peran_pemeriksa`, `status_evaluasi`, `catatan`, `nilai_angka_kredit`
- View `validasi/show.blade.php` sudah punya form: slider score, flag (OK/Doubt/Fake), catatan per detail
- View `validasi/index.blade.php` sudah menampilkan daftar detail pengajuan yang ditugaskan ke TPAK

### Yang Belum Ada / Butuh Perbaikan
1. **Authorization** — `show()` tidak cek apakah TPAK yang akses ini ditunjuk untuk pengajuan tersebut
2. **Simpan evaluasi per detail** — `update()` hanya update status pengajuan, tidak simpan ke `hasil_evaluasi`
3. **Duplikasi evaluasi** — TPAK bisa submit berkali-kali untuk detail yang sama (harus update, bukan insert baru)
4. **Tampilkan evaluasi sebelumnya** — Jika TPAK sudah pernah nilai, form harus menampilkan nilai lama
5. **Tampilkan evaluasi TPAK lain** — TPAK perlu lihat penilaian TPAK lain untuk transparansi
6. **Progress di index** — Statistik "60%" hardcoded, harusnya dari data evaluasi real

## Plan Implementasi
- [x] 1. Perbaiki `ValidasiController::show()` — cek authorization + load evaluasi sebelumnya
- [x] 2. Perbaiki `ValidasiController::update()` — simpan evaluasi per detail ke `hasil_evaluasi`
- [x] 3. Handle duplikasi — update existing record jika TPAK sudah pernah nilai detail yang sama
- [x] 4. Update view `show.blade.php` — tampilkan nilai yang sudah pernah diisi + evaluasi TPAK lain
- [x] 5. Update view `index.blade.php` — progress real-time + status sudah/belum dinilai
