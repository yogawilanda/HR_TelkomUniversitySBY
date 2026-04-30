<?php

namespace App\Helpers;

/**
 * Helper class for DUPAK scoring calculations.
 * Handles special calculations like SKS overload for teaching activities.
 */
class DupakScoringHelper
{
    /**
     * Maximum SKS per semester before overload penalty applies.
     */
    public const MAX_SKS_NORMAL = 10;

    /**
     * Overload factor for SKS above maximum.
     */
    public const OVERLOAD_FACTOR = 0.5;

    /**
     * Normal SKS factor.
     */
    public const NORMAL_FACTOR = 1.0;

    /**
     * Calculate teaching activity credit with SKS overload logic.
     * 
     * When semester total SKS > 10, the formula splits into:
     * - Normal portion (first 10 SKS): × 1.0
     * - Overload portion (remaining SKS): × 0.5
     * 
     * @param int|float $sksTotal Total SKS (sks × classes)
     * @param float $nilaiBaku Nilai baku per SKS (usually 1.0)
     * @return array Array with keys: 'normal', 'overload', 'total', 'is_overloaded'
     */
    public static function calculateTeachingCredit($sksTotal, $nilaiBaku = 1.0)
    {
        $sksTotal = floatval($sksTotal);
        $nilaiBaku = floatval($nilaiBaku);

        if ($sksTotal <= self::MAX_SKS_NORMAL) {
            // No overload - simple calculation
            $total = $sksTotal * $nilaiBaku * self::NORMAL_FACTOR;
            return [
                'normal' => $sksTotal * $nilaiBaku,
                'overload' => 0,
                'total' => $total,
                'is_overloaded' => false,
                'records' => [
                    [
                        'volume' => $sksTotal,
                        'factor' => self::NORMAL_FACTOR,
                        'angka_kredit' => $sksTotal * $nilaiBaku
                    ]
                ]
            ];
        }

        // Has overload - split calculation
        $normalSks = self::MAX_SKS_NORMAL;
        $overloadSks = $sksTotal - self::MAX_SKS_NORMAL;

        $normalCredit = $normalSks * $nilaiBaku * self::NORMAL_FACTOR;
        $overloadCredit = $overloadSks * $nilaiBaku * self::OVERLOAD_FACTOR;
        $totalCredit = $normalCredit + $overloadCredit;

        return [
            'normal' => $normalCredit,
            'overload' => $overloadCredit,
            'total' => $totalCredit,
            'is_overloaded' => true,
            'records' => [
                [
                    'volume' => $normalSks,
                    'factor' => self::NORMAL_FACTOR,
                    'angka_kredit' => $normalCredit,
                    'type' => 'normal'
                ],
                [
                    'volume' => $overloadSks,
                    'factor' => self::OVERLOAD_FACTOR,
                    'angka_kredit' => $overloadCredit,
                    'type' => 'overload'
                ]
            ]
        ];
    }

    /**
     * Calculate thesis/dissertation guidance credit.
     * 
     * @param string $jenisPembimbing Type: 'utama' or 'pendamping'
     * @param int $jumlahBimbingan Number of students supervised
     * @param float|null $nilaiBaku Override default nilai_baku
     * @return float Total angka kredit
     */
    public static function calculateThesisCredit($jenisPembimbing, $jumlahBimbingan = 1, $nilaiBaku = null)
    {
        $factor = ($jenisPembimbing === 'utama') ? 1.0 : 0.5;
        $nilaiBaku = $nilaiBaku ?? 1.0;

        return $jumlahBimbingan * $nilaiBaku * $factor;
    }

    /**
     * Calculate exam committee credit.
     * 
     * @param string $jenisPenguji Type: 'ketua' or 'anggota'
     * @param int $jumlahMahasiswa Number of students examined
     * @param float|null $nilaiBaku Override default nilai_baku
     * @return float Total angka kredit
     */
    public static function calculateExamCredit($jenisPenguji, $jumlahMahasiswa = 1, $nilaiBaku = null)
    {
        $factor = ($jenisPenguji === 'ketua') ? 1.0 : 0.5;
        $nilaiBaku = $nilaiBaku ?? 1.0;

        return $jumlahMahasiswa * $nilaiBaku * $factor;
    }

    /**
     * Format teaching credit for display (showing both normal and overload if applicable).
     * 
     * @param array $calculationResult Result from calculateTeachingCredit()
     * @return string Formatted string like "8 (+2 overload)"
     */
    public static function formatTeachingCredit($calculationResult)
    {
        if (!$calculationResult['is_overloaded']) {
            return number_format($calculationResult['total'], 2);
        }

        return sprintf(
            "%s (normal) + %s (overload) = %s",
            number_format($calculationResult['normal'], 2),
            number_format($calculationResult['overload'], 2),
            number_format($calculationResult['total'], 2)
        );
    }

    /**
     * Get maximum allowed credit per semester for a category.
     * 
     * @param string $kategori Category name (pelaksanaan_pendidikan, penelitian, etc)
     * @return float|null Maximum credit, or null if no limit
     */
    public static function getMaxCredit($kategori)
    {
        $limits = [
            'pelaksanaan_pendidikan' => 11, // Teaching per semester
            'bimbing_skripsi' => 32,     // Thesis guidance per semester
            'ujian_akhir' => 8,        // Exam committee per semester
            'keg_mahasiswa' => 4,      // Student activities per semester
            'pengembangan_kuliah' => 2,  // Course development per semester
        ];

        return $limits[$kategori] ?? null;
    }
}
