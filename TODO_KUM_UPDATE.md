# TODO: Fix KUM Update After TPAK Approval

## Steps:
- [ ] Step 1: Read app/Models/Dupak/HasilEvaluasi.php to confirm fields (nilai_angka_kredit, status_evaluasi='OK', peran_pemeriksa)
- [ ] Step 2: Edit app/Models/Dupak/Pengajuan.php - Add updateDosenKum() method
- [x] Step 3: Edit app/Http/Controllers/Dupak/ValidasiController.php - Call updateDosenKum() after status set to 'Diterima'
- [ ] Step 4: Test approval flow and verify kum update in users table/dashboard
- [ ] Step 5: Complete task

**Current Progress: Steps 1-2 complete - HasilEvaluasi read, updateDosenKum() added to Pengajuan model. Proceeding to Step 3: Edit ValidasiController.php**
