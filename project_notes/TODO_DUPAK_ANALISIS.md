# Analisis Status DUPAK & Rencana Pengerjaan

> Analisis lengkap status fitur DUPAK per hari ini. Dibuat setelah audit file controller, view, model, migration, routes, dan catatan proyek.

---

## ✅ FITUR YANG SUDAH SELESAI

### 1. Dashboard DUPAK
| Fitur | Status |
|-------|--------|
| Kartu Informasi KUM (Progress Bar, KUM Saat Ini, Target, Tersisa) | ✅ |
| Breakdown KUM per 5 Kategori | ✅ |
| Statistik Admin (Total, Selesai, Pending) | ✅ |
| Tab Personal + Tab TPAK | ✅ |
| Identitas Pengaju | ✅ |
| Tabel Daftar Pengajuan dengan pagination | ✅ |
| Blokir Guru Besar (tidak bisa ajukan kenaikan) | ✅ |

### 2. Pengajuan DUPAK
| Fitur | Status |
|-------|--------|
| List, Create, Store, Show | ✅ |
| Auto-determinasi JFA Asal → JFA Tujuan | ✅ |
| Timeline aktivitas dengan evaluasi TPAK | ✅ |

### 3. Detail Pengajuan (Input Kegiatan)
| Fitur | Status |
|-------|--------|
| Form dinamis per kategori (generic_form) | ✅ |
| Store: Pendidikan, Penelitian, Pengabdian, Penunjang | ✅ |
| Perhitungan AK otomatis (AK Baku × Volume) | ✅ |
| **Store: Pelaksanaan Pendidikan** | ⏳ BELUM |

### 4. Penunjukan TPAK
| Fitur | Status |
|-------|--------|
| Semua 10 item TODO_TPAK.md sudah selesai | ✅ |
| Validasi backend lengkap (self-assign, duplikat, max 5, JFA, final status) | ✅ |
| Progress real-time, audit trail, search, pagination | ✅ |

### 5. Validasi / Penilaian TPAK
| Fitur | Status |
|-------|--------|
| Authorization (cek apakah TPAK ditunjuk) | ✅ |
| Form penilaian per detail (score, flag, catatan) | ✅ |
| Simpan ke `hasil_evaluasi` (insert/update) | ✅ |
| Tampilkan evaluasi sebelumnya + TPAK lain | ✅ |
| Catatan umum TPAK ke `penunjukan_tpak` | ✅ |

---

## ❌ FITUR YANG MASIH BELUM / KURANG

### A. CRITICAL — Flow Status Pengajuan
| # | Fitur | Status | Keterangan |
|---|-------|--------|------------|
| 1 | **"Kirim Pengajuan" (Draft → Submitted/Diajukan)** | ✅ SELESAI | Backend: `PengajuanController::submit()` + route POST. View: `pengajuan/show.blade.php` & `dashboard.blade.php` (via `info-kum.blade.php`). |
| 2 | **Update status pengajuan dari Validasi** | ✅ SELESAI | `ValidasiController::update()` sudah memproses field `status`, mapping ke status pengajuan (Diterima/Ditolak/Revisi). |

### B. HIGH — Validasi Index View (`validasi/index.blade.php`)
| # | Masalah | Detail |
|---|---------|--------|
| 3 | Progress bar real-time | ✅ Dari `$progressMap` per detail — 0% atau 100% |
| 4 | Statistik "Selesai" real | ✅ `$selesaiCount` dari `HasilEvaluasi` query |
| 5 | Statistik "Rata-rata Score" real | ✅ `$avgScore` dihitung dari persentase nilai |
| 6 | Filter status berfungsi | ✅ Form `<form>` GET terhubung ke `ValidasiController::index()` |
| 7 | Badge "Sudah/Belum Dinilai" | ✅ Badge hijau/kuning + progress bar dinamis |

### C. MEDIUM — Fitur Kurang
| # | Fitur | Status |
|---|-------|--------|
| 8 | **Verifikasi Admin** (cek kelengkapan dokumen sebelum TPAK) | ❌ BELUM |
| 9 | **Mode TPAK untuk Dosen** (tombol "Menjadi TPAK" href kosong) | ✅ SELESAI | Link ke `route('dupak.validasi.index')` |
| 10 | **Form Pelaksanaan Pendidikan** | ⏳ BELUM |
| 11 | **Edit / Hapus Pengajuan** | ✅ SELESAI | `PengajuanController::edit/update/destroy` + view `edit.blade.php` |

### D. LOW — Fitur Akhir
| # | Fitur | Status |
|---|-------|--------|
| 12 | **Rekapitulasi Akhir & Export PDF** | ❌ BELUM |

---

## 📋 REKOMENDASI PLAN HARI INI

### Fase 1: CRITICAL (Wajib)
1. **Sambungkan update status pengajuan dari halaman validasi**
   - File: `ValidasiController::update()`
   - Update tabel `pengajuan.status` dari input form
   - Handle approval/rejection logic

2. **Fitur "Kirim Pengajuan"**
   - Tambahkan action/button di dashboard/pengajuan show
   - Controller method untuk ubah status `Draft` → `Diajukan`
   - Pastikan hanya pengaju yang bisa kirim

### Fase 2: HIGH (Validasi Index)
3. Progress bar real-time dari DB
4. Statistik selesai & rata-rata score real
5. Filter terhubung backend
6. Badge "Sudah/Belum Dinilai"

### Fase 3: MEDIUM
7. Verifikasi Admin
8. Mode TPAK untuk Dosen
9. Form Pelaksanaan Pendidikan

---

## File yang Akan Diedit (Fase 1)

| File | Perubahan |
|------|-----------|
| `app/Http/Controllers/Dupak/ValidasiController.php` | Update method menerima & proses `status` pengajuan |
| `resources/views/dupak/validasi/show.blade.php` | (opsional) tambahkan hidden input atau adjust form |
| `app/Http/Controllers/Dupak/PengajuanController.php` | Tambah method `submit()` untuk kirim pengajuan |
| `resources/views/dupak/pengajuan/show.blade.php` | Tambah tombol "Kirim Pengajuan" |
| `resources/views/dupak/dashboard.blade.php` | Tambah tombol/action kirim pengajuan jika status Draft |
| `routes/web.php` | Tambah route untuk submit pengajuan |

---

## Kesimpulan

> **Penilaian sudah masuk ke DB ✅, tapi flow status pengajuan belum tersambung sepenuhnya ❌.**
> TPAK bisa menilai dan nilai tersimpan di `hasil_evaluasi`, tapi status pengajuan (`Pending`, `Diajukan`, `Diterima`, `Ditolak`) tidak berubah otomatis.
> Ini membuat proses DUPAK "buntu" — dosen tidak bisa "kirim" pengajuan, dan TPAK tidak bisa "finalkan" hasil penilaian.

