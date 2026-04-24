# TODO: Audit & Perbaikan Fitur Penunjukan TPAK

## Status Saat Ini

### Sudah Ada (Bagus)
1. Validasi backend: Dosen tidak boleh menilai pengajuannya sendiri.
2. Validasi duplikasi TPAK.
3. Validasi maksimal 5 TPAK per pengajuan.
4. Validasi JFA TPAK harus >= JFA Tujuan Pengaju.
5. Pesan error/success di view.
6. Fitur pencarian dan pagination.
7. Pembatalan penunjukan (destroy).

### Belum Ada / Blind Spot (Perlu Diperbaiki)
1. **UI Dropdown TPAK masih menampilkan dosen pengaju** — User baru tahu error setelah submit.
2. **UI tidak menampilkan jumlah TPAK yang sudah ditunjuk** di select pengajuan.
3. **Tombol "Tunjuk Penilai" di tabel antrean tidak disabled** meski sudah 5 TPAK.
4. **Dropdown TPAK tidak mengecualikan dosen yang sudah ditunjuk** untuk pengajuan tersebut.
5. **Tidak ada info beban kerja TPAK** — jumlah penugasan aktif per dosen.
6. **Tidak ada pengecekan status pengajuan** — jika sudah Diterima/Ditolak, tidak boleh ditambah TPAK.

## Plan Perbaikan
- [x] 1. Filter dosen pengaju dari dropdown TPAK di form (UI via JS).
- [x] 2. Tampilkan jumlah TPAK yang sudah ditunjuk di select pengajuan.
- [x] 3. Disable tombol "Tunjuk Penilai" di tabel antrean jika sudah 5 TPAK.
- [x] 4. Filter dosen yang sudah ditunjuk untuk pengajuan tertentu dari dropdown TPAK (JS).
- [x] 5. Tampilkan beban kerja TPAK (jumlah penugasan aktif) di dropdown.
- [x] 6. Validasi backend: blokir penunjukan jika status pengajuan sudah final (Diterima/Ditolak/Selesai).
- [x] 7. Ubah form penunjukan menjadi modal popup yang muncul saat tombol "Tunjuk Penilai" ditekan.

