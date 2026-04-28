# TODO: Audit & Perbaikan Fitur Penunjukan TPAK

## Status Akhir: ✅ 10/10 Item Terimplementasi

> Hasil audit lengkap per 2025. Tidak ada item TODO yang tersisa.
> Semua fitur sudah terimplementasi baik di backend (Controller) maupun frontend (View + JS).

---

## ✅ Sudah Ada (Bagus)

| # | Fitur | Lokasi Implementasi |
|---|-------|---------------------|
| 1 | Validasi backend: Dosen tidak boleh menilai pengajuannya sendiri | `PenunjukanTPAKController::store()` |
| 2 | Validasi duplikasi TPAK | `PenunjukanTPAKController::store()` |
| 3 | Validasi maksimal 5 TPAK per pengajuan | `PenunjukanTPAKController::store()` |
| 4 | Validasi JFA TPAK harus >= JFA Tujuan Pengaju | `PenunjukanTPAKController::store()` |
| 5 | Pesan error/success di view | `index.blade.php` (session flash) |
| 6 | Fitur pencarian dan pagination | `index()` + `index.blade.php` |
| 7 | Pembatalan penunjukan (destroy) | `destroy()` |

---

## ✅ Blind Spot yang Sudah Diperbaiki (10/10)

| # | Item | Backend | Frontend | Status |
|---|------|---------|----------|--------|
| 1 | Filter dosen pengaju dari dropdown TPAK | `pengajuMap` JSON | JS `isSelf` hide option | ✅ |
| 2 | Tampilkan jumlah TPAK di select pengajuan | `$tpakCounts` | `(X/5 TPAK)` di option | ✅ |
| 3 | Disable tombol "Tunjuk Penilai" jika sudah 5 TPAK | `$isFull` flag | Badge hijau "Lengkap" | ✅ |
| 4 | Filter dosen yang sudah ditunjuk dari dropdown | `$assignedMap` JSON | JS `isAssigned` hide option | ✅ |
| 5 | Info beban kerja TPAK | `$dosenWorkload` query | `(Beban: X penugasan)` + widget chart | ✅ |
| 6 | Blokir penunjukan jika status pengajuan final | `$finalStatuses` check | — | ✅ |
| 7 | Form penunjukan menjadi modal popup | — | Modal `#modalTpak` + `quickAssign()` | ✅ |
| 8 | Progress real-time di tabel penugasan | `DetailPengajuan` + `HasilEvaluasi` JOIN | Progress bar dinamis `%` | ✅ |
| 9 | Filter/search di tabel antrean pengajuan | `$antreanSearch` query | Input search + form GET | ✅ |
| 10 | Audit trail (siapa admin yang menunjuk) | `created_by` + `Auth::id()` | `Ditunjuk oleh: Nama` + relasi `creator()` | ✅ |

---

## 🔍 Blind Spot TAMBAHAN (Ditemukan Saat Audit Mendalam)

Berikut celah yang **belum tercatat di TODO asli** dan belum terimplementasi:

| # | Celah | Dampak | Prioritas |
|---|-------|--------|-----------|
| 11 | **Double submit protection** — Tombol submit modal tidak disabled saat loading | User bisa klik 2x, duplikat data (meski backend cegah) | Medium |
| 12 | **Validasi JFA pakai nama string**, bukan UUID/level numerik dari tabel | Jika admin ubah nama "Guru Besar" → "Profesor", mapping level bisa gagal | Medium |
| 13 | **Notifikasi ke TPAK** — Setelah ditunjuk, TPAK tidak dapat notifikasi apa pun | TPAK tidak tahu ada penugasan baru | Low |
| 14 | **History perubahan penunjukan** — Hapus TPAK hanya hard delete, tidak ada log siapa yang menghapus | Tidak bisa audit siapa yang membatalkan | Low |
| 15 | **Widget "Selesai Dinilai" masih hardcoded `0`** | Statistik tidak akurat | Low |
| 16 | **Escape key menutup modal tapi tidak reset form** | Data terbuka saat modal dibuka kembali | Very Low |

---

## 📝 Catatan Implementasi Detail

### Item 1, 4: Filter Self-Assign & Duplikat (JS)
```javascript
const isSelf = pengajuId && dosenId === pengajuId;
const isAssigned = assigned.includes(dosenId);
if (isSelf || isAssigned) opt.hidden = true;
```

### Item 6: Status Final Check
```php
$finalStatuses = ['Diterima', 'Ditolak', 'Selesai'];
if (in_array($pengajuan->status, $finalStatuses)) {
    return redirect()->back()->with('error', 'Pengajuan sudah final...');
}
```

### Item 8: Progress Real-time
```php
$detailCounts = DetailPengajuan::select('pengajuan_id', DB::raw('count(*) as total'))...
$evaluatedCounts = HasilEvaluasi::select(...)...
$progress_percent = $totalDetail > 0 ? round(($evaluated / $totalDetail) * 100) : 0;
```

### Item 10: Audit Trail
```php
// Migration
$table->uuid('created_by')->nullable()->comment('User ID admin yang menunjuk');

// Model
public function creator() {
    return $this->belongsTo(\App\Models\User::class, 'created_by');
}

// Controller
'created_by' => Auth::id(),

// View
Ditunjuk oleh: {{ $item->creator->nama_lengkap ?? 'Sistem' }}
```

---

## Kesimpulan

**Dari TODO_TPAK.md sendiri, tidak ada yang kurang. Semua 10 item sudah terimplementasi dengan baik.**

Jika ingin melanjutkan, fokus ke **blind spot tambahan #11–16** atau fitur besar lain yang belum dikerjakan (item 19–24 di `catatan_pengerjaan_dupak.txt`).
