# TODO.md - Completion of @dupak_table_ref_jenis_input for idKomponen 12 (Komponen J: Menduduki jabatan pimpinan perguruan tinggi)

## Current Status
- Migration seeding incomplete: only 3/8 jabatan entries.
- Plan approved. Progress tracked here.

## Steps (Breakdown of Approved Plan)
- [ ] **Step 1**: Edit `database/migrations/dupak/2025_11_19_085504_dupak_table_ref_jenis_input.php` to complete 8 seeding entries for idKomponen 12 (fix duplicates, add missing d-h).
- [ ] **Step 2**: Refresh migration seeds (rollback then migrate or direct DB insert if needed).
- [ ] **Step 3**: Verify in DB: 8 rows with idKomponen=12 and correct nama/nilai_baku.
- [ ] **Step 4**: Inspect dependent files (DupakScoringHelper.php, DetilPengajuanController.php, generic_form.blade.php) for any idKomponen 12 specific logic needing updates.
- [ ] **Step 5**: Test frontend form dropdown shows all 8 jabatan for idKomponen 12.
- [ ] **Step 6**: Update this TODO.md with completion, then mark task done.

**Next Action**: Proceed to Step 1 (edit migration).

