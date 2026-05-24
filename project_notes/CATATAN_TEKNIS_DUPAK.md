# Catatan Teknis Khusus Project DUPAK

> File ini dibuat sebagai referensi bagi AI/Developer lain agar tidak mengulangi kesalahan struktural.

## 1. Arsitektur Database Multi-Connection

Aplikasi ini menggunakan **dua database terpisah**:
- **`sdm_tus`** (koneksi `mysql` / default) — database utama SDM: `users`, `dosens`, `pegawai`, `ref_jabatan_fungsional_akademiks`, dll.
- **`dupak`** (koneksi `dupak`) — database modul DUPAK: `pengajuan`, `penunjukan_tpak`, `detail_pengajuan`, `hasil_evaluasi`, dll.

### Aturan Penting
- Model DUPAK **wajib** extend `DupakModel` yang sudah mengatur koneksi `dupak`.
- Query ke DB utama dari controller DUPAK harus pakai `DB::connection('mysql')` atau model Eloquent biasa (`Dosen`, `User`).
- **Tidak bisa membuat Foreign Key constraint** antar database. Contoh: `penunjukan_tpak.idDosenTpak` hanya UUID tanpa FK constraint ke `dosens.id`.

## 2. Lokasi Migration & Aturan Migration

Migration DUPAK **wajib** diletakkan di:
```
database/migrations/dupak/
```

Format nama file:
```
YYYY_MM_DD_HHMMSS_dupak_table_<nama_tabel>.php
```

Contoh:
```
2025_11_19_072934_dupak_table_penunjukan_tpak.php
```

Setiap migration DUPAK wajib memiliki:
```php
protected $connection = 'dupak';
```

dan menggunakan:
```php
Schema::connection($this->connection)->create(...);
// atau
Schema::connection($this->connection)->table(...);
```

### ⚠️ Aturan Penting: Jangan Buat Migration Baru untuk Alter Table

Tim menggunakan custom command:
```bash
php artisan db:full-refresh --seed
```

Command ini **menghapus dan membuat ulang semua tabel**, jadi:
- **Tidak perlu membuat file migration baru** hanya untuk menambah/mengubah kolom.
- **Edit langsung migration existing** (file `create` table) agar struktur tabel selalu terdefinisi lengkap dalam satu file.
- Semua perubahan schema DUPAK harus dilakukan di file migration yang sudah ada, bukan file baru.

## 3. Model DUPAK

Semua model DUPAK ada di `app/Models/Dupak/` dan **wajib** extend `DupakModel`:
```php
class PenunjukanTPAKModel extends DupakModel { ... }
```

`DupakModel` sendiri sudah mengatur:
- `$connection = 'dupak'`
- Guarded / Fillable sesuai kebutuhan

Jangan membuat model DUPAK yang extend `Model` langsung tanpa melalui `DupakModel`.

## 4. Relasi Cross-Database

Karena DB terpisah, relasi Eloquent tetap bisa digunakan tapi **tidak dengan Foreign Key constraint di level database**.

Contoh yang benar:
```php
// Model DUPAK — relasi ke Dosen (DB utama)
public function dosenTpak()
{
    return $this->belongsTo(Dosen::class, 'idDosenTpak');
}
```

Constraint FK hanya boleh dibuat antar tabel dalam **database DUPAK yang sama** (contoh: `penunjukan_tpak.pengajuan_id` → `pengajuan.id`).

## 5. Jabatan Fungsional Akademik (JFA) & Urutan Kenaikan

Urutan kenaikan JFA didefinisikan di controller sebagai array map:
```php
protected $aturanPengajuanJFA = [
    '8a7c0b44-...' => 'Non JAD',
    'b467678d-...' => 'Asisten Ahli',
    'f6890047-...' => 'Lektor',
    '21ac00aa-...' => 'Lektor Kepala',
    'd6418a5e-...' => 'Guru Besar (Profesor)',
];
```

JFA terakhir = **Guru Besar**. Jika dosen sudah Guru Besar, tidak boleh mengajukan kenaikan jabatan lagi (sudah puncak karir).

## 6. Penunjukan TPAK — Aturan Bisnis

Berikut aturan yang sudah diimplementasikan dan wajib dijaga:
1. Dosen tidak boleh menilai pengajuannya **sendiri**.
2. Tidak boleh duplikasi TPAK untuk pengajuan yang sama.
3. Maksimal **5 TPAK** per pengajuan.
4. JFA TPAK harus **≥** JFA Tujuan Pengaju.
5. TPAK harus memiliki JFA aktif (tidak null).
6. Status pengajuan harus aktif (`Pending` / `Submitted`) — tidak boleh `Diterima` / `Ditolak`.
7. Audit trail tersimpan di kolom `created_by` (User ID admin yang menunjuk).

## 7. View & Layout

Layout utama: `resources/views/layouts/app.blade.php`
- Menggunakan `@yield('script')` untuk JS tambahan, **bukan** `@stack('scripts')`.
- Jadi setiap view yang butuh script wajib pakai `@section('script')` di akhir file, bukan `@push('scripts')`.

## 8. Catatan Lain

- Fokus pengembangan hanya pada modul **DUPAK**.
- Jangan mengubah struktur database utama (`sdm_tus`) tanpa koordinasi.
- Gunakan `DB::raw()` dan `DB::connection()` dengan hati-hati untuk menghindari SQL Injection.
- Jalankan migrasi dan seeding dengan: `php artisan db:full-refresh --seed`
