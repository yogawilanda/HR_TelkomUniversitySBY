# TODO: Implementasi Sub-Komponen D (idKomponen=6) - Membimbing Disertasi/Tesis/Skripsi/Laporan Akhir

Status: Approved plan - No new migrations

## Breakdown of Approved Plan (Logical Steps)

### Step 1: Extend DupakScoringHelper.php [✅ COMPLETE]
- Add RATES and QUOTAS constants
- Implement calculateBimbinganCredit(array $entries) method
  - Parse entries from deskripsi_kegiatan (NIM|Jenis|Peran|Semester|Tahun)
  - Group by 'jenis_peran' key
  - Apply quota cap per group
  - Sum and final cap at 32
  - Return total_ak, breakdown
- Add getBimbinganConfig() for frontend rates/quotas

### Step 2: Update DetilPengajuanController.php [✅ COMPLETE]
- showForm(): Enhance $specialFields for id=6 (peran, jenis_bimbingan, nim_mahasiswa, semester, tahun_ajaran)
- Pass $bimbinganConfig from helper to view
- store(): Parse request fields into formatted deskripsi_kegiatan
- Fetch/update all details for pengajuan_id + idKomponen=6
- Use helper to compute final_ak
- Cascade update angka_kredit_total on all related details

### Step 3: Update generic_form.blade.php [✅ COMPLETE]
- Add @if($isMembimbingDisertasi) block for special fields grid
- JS: Category counters, projected preview with quota warnings
- Use $bimbinganConfig data
- ak_preview shows custom rate (not from jenis_input)

### Step 4: Testing & Validation [PENDING]
- Test over-quota (e.g., 12 Skripsi Utama -> 8 AK)
- Test total cap 32
- Frontend warnings
- Dashboard aggregation preview

### Step 5: Completion [PENDING]
- attempt_completion with demo command

**Progress: 0/5 steps complete**

