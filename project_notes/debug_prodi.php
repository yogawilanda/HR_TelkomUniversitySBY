<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Prodi;
use App\Models\Dosen;

// Cari semua prodi, ambil yang pertama (atau ganti dengan ID spesifik)
$prodi = Prodi::first();

if (!$prodi) {
    echo "Prodi Informatika tidak ditemukan!\n";
    exit;
}

echo "=== DEBUG PRODI: {$prodi->nama_prodi} ===\n\n";

$dosens = Dosen::where('id_prodi', $prodi->id)->get();
echo "Total Dosen: " . $dosens->count() . "\n\n";

// Hitung jabatan fungsional dengan detail
$njadCount = 0;
$aaCount = 0;
$lCount = 0;
$lkCount = 0;
$gbCount = 0;

echo "=== DETAIL JABATAN FUNGSIONAL ===\n";
foreach ($dosens as $dosen) {
    $nama = $dosen->pegawai ? $dosen->pegawai->nama_pegawai : 'Unknown';

    if ($dosen->riwayatJabatanFungsional && $dosen->riwayatJabatanFungsional->count() > 0) {
        $latestJafung = $dosen->riwayatJabatanFungsional
            ->sortByDesc('tmt_jafung')
            ->first();

        if ($latestJafung && $latestJafung->jabatanFungsional) {
            $jafung = $latestJafung->jabatanFungsional->nama_jafung ?? '';
            $jafungUpper = strtoupper($jafung);

            if (str_contains($jafungUpper, 'GURU BESAR') || str_contains($jafungUpper, 'PROFESOR')) {
                $gbCount++;
                $kategori = 'GB (Guru Besar)';
            } elseif (str_contains($jafungUpper, 'LEKTOR KEPALA')) {
                $lkCount++;
                $kategori = 'LK (Lektor Kepala)';
            } elseif (str_contains($jafungUpper, 'LEKTOR')) {
                $lCount++;
                $kategori = 'L (Lektor)';
            } elseif (str_contains($jafungUpper, 'ASISTEN AHLI')) {
                $aaCount++;
                $kategori = 'AA (Asisten Ahli)';
            } else {
                $njadCount++;
                $kategori = 'NJAD';
            }

            echo sprintf("%-30s | %-40s | %s\n", substr($nama, 0, 30), substr($jafung, 0, 40), $kategori);
        } else {
            $njadCount++;
            echo sprintf("%-30s | %-40s | %s\n", substr($nama, 0, 30), '(Tidak ada jabatan)', 'NJAD');
        }
    } else {
        $njadCount++;
        echo sprintf("%-30s | %-40s | %s\n", substr($nama, 0, 30), '(Tidak ada riwayat)', 'NJAD');
    }
}

echo "\n=== RINGKASAN JFA ===\n";
echo "NJAD (Non-JFA Dosen): $njadCount\n";
echo "AA (Asisten Ahli): $aaCount\n";
echo "L (Lektor): $lCount\n";
echo "LK (Lektor Kepala): $lkCount\n";
echo "GB (Guru Besar): $gbCount\n";

$totalDosen = $dosens->count();
$llkgb = $totalDosen > 0 ? (($lkCount + $gbCount) / $totalDosen * 100) : 0;
$jfa = $totalDosen > 0 ? (($aaCount + $lCount + $lkCount + $gbCount) / $totalDosen * 100) : 0;

echo "\n=== PERHITUNGAN ===\n";
echo "LLKGB = (($lkCount + $gbCount) / $totalDosen) × 100% = " . number_format($llkgb, 2) . "%\n";
echo "JFA = (($aaCount + $lCount + $lkCount + $gbCount) / $totalDosen) × 100% = " . number_format($jfa, 2) . "%\n";

// Cek cache
$statsKey = 'prodi_stats_' . $prodi->id;
$cachedStats = cache()->get($statsKey);

echo "\n=== CACHE STATUS ===\n";
if ($cachedStats) {
    echo "Ada data cache manual:\n";
    echo "  LK: " . ($cachedStats['lk'] ?? 'N/A') . "\n";
    echo "  GB: " . ($cachedStats['gb'] ?? 'N/A') . "\n";
    echo "  LLKGB akan dihitung dari data cache\n";
} else {
    echo "Tidak ada cache, menggunakan perhitungan otomatis\n";
}
