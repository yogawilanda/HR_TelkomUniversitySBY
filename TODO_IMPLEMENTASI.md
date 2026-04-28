# TODO Implementasi Fase 1 — Critical DUPAK Fixes

## Step 1: ValidasiController — Proses status pengajuan dari form TPAK
- [x] Tambah validasi `status` di `ValidasiController::update()`
- [x] Update `pengajuan.status` setelah simpan evaluasi
- [x] Update juga `detail_pengajuan.status` per-row jika flag Fake/Rejected

## Step 2: PengajuanController — Fitur "Kirim Pengajuan"
- [x] Tambah method `submit($id)` di PengajuanController
- [x] Validasi: hanya pengaju sendiri + status Draft/Pending
- [x] Ubah status menjadi `Diajukan`

## Step 3: Routes
- [x] Tambah route POST `dupak/pengajuan/{id}/submit`

## Step 4: View — Tombol "Kirim Pengajuan"
- [x] `resources/views/dupak/pengajuan/show.blade.php` — Tambah form tombol Kirim + flash message
- [x] `resources/views/dupak/dashboard.blade.php` — Tambah tombol Kirim di card pengajuan aktif

## Step 5: Validasi Index View
- [x] Progress bar real-time dari DB
- [x] Statistik selesai & rata-rata score real
- [x] Filter terhubung ke backend + tombol reset
- [x] Badge "Sudah/Belum Dinilai" + warna tombol dinamis

