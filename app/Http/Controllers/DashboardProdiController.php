<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\User;
use App\Models\work_position;
use Illuminate\Http\Request;

class DashboardProdiController extends Controller
{
    /**
     * Dashboard Pendidikan - Statistik S2 dan S3
     */
    public function pendidikan(Request $request)
    {
        // $cek_user_study = User::with('last_studi')->get();
        // dd($cek_user_study[0]->last_studi());
        $type = $request->get('type', 'all');

        $query = work_position::with('prodi_parent');
        // dd($query);

        if ($type == 'dosen') {
            $query->where('type_work_position', 'Program Studi');
        } elseif ($type == 'tpa') {
            $query->where('type_work_position', 'Bagian');
        } else {
            $query->whereIn('type_work_position', ['Program Studi', 'Bagian']);
        }

        $prodis = $query->get();
        // dd($prodis);

        $prodiStats = $prodis->map(function ($prodi) use ($type) {
            $pegawais = $this->getPegawais($prodi, $type);
            $totalPegawai = $pegawais->count();

            // Hitung pendidikan
            $d3Count = 0;
            $s1Count = 0;
            $s2Count = 0;
            $s3Count = 0;

            foreach ($pegawais as $pegawai) {
                if ($pegawai->pegawai && $pegawai->pegawai->riwayatJenjangPendidikan) {
                    $latestPendidikan = $pegawai->pegawai->riwayatJenjangPendidikan
                        ->sortByDesc('tahun_lulus')
                        ->first();

                    if ($latestPendidikan && $latestPendidikan->refJenjangPendidikan) {
                        $jenjang = strtoupper($latestPendidikan->refJenjangPendidikan->jenjang_pendidikan ?? '');
                        if (str_contains($jenjang, 'D3')) {
                            $d3Count++;
                        } elseif (str_contains($jenjang, 'S1') || str_contains($jenjang, 'SARJANA') || str_contains($jenjang, 'D4')) {
                            $s1Count++;
                        } elseif (str_contains($jenjang, 'S2') || str_contains($jenjang, 'MAGISTER')) {
                            $s2Count++;
                        } elseif (str_contains($jenjang, 'S3') || str_contains($jenjang, 'DOKTOR')) {
                            $s3Count++;
                        }
                    }
                }
            }

            $persenS3 = $totalPegawai > 0 ? min($s3Count / $totalPegawai, 1.0) : 0;
            return [
                'id' => $prodi->id,
                'nama_prodi' => $prodi->position_name,
                'fakultas' => $prodi->prodi_parent()->first()->position_name ?? '-',
                'total_pegawai' => $totalPegawai,
                'd3' => $d3Count,
                's1' => $s1Count,
                's2' => $s2Count,
                's3' => $s3Count,
                'persen_s3' => $persenS3,
                'type' => $prodi->type_work_position,
            ];
        });

        $totals = [
            'total_pegawai' => $prodiStats->sum('total_pegawai'),
            'd3' => $prodiStats->sum('d3'),
            's1' => $prodiStats->sum('s1'),
            's2' => $prodiStats->sum('s2'),
            's3' => $prodiStats->sum('s3'),
        ];
        $totals['persen_s3'] = $totals['total_pegawai'] > 0 ? min($totals['s3'] / $totals['total_pegawai'], 1.0) : 0;

        return view('kelola_data.dashboard_prodi.pendidikan', compact('prodiStats', 'totals', 'type'));
    }

    /**
     * Dashboard Jabatan Fungsional - Statistik JFA
     */
    public function fungsional()
    {
        $prodis = work_position::where('type_work_position', 'Program Studi')->with('prodi_parent')->get();

        $prodiStats = $prodis->map(function ($prodi) {
            $dosens = $this->getDosens($prodi);
            $totalDosen = $dosens->count();

            // Hitung jabatan fungsional
            $njadCount = 0;
            $aaCount = 0;
            $lCount = 0;
            $lkCount = 0;
            $gbCount = 0;

            foreach ($dosens as $dosen) {
                if ($dosen->riwayatJabatanFungsional && $dosen->riwayatJabatanFungsional->count() > 0) {
                    $latestJafung = $dosen->riwayatJabatanFungsional
                        ->sortByDesc('tmt_jafung')
                        ->first();

                    if ($latestJafung && $latestJafung->jabatanFungsional) {
                        $jafung = strtoupper($latestJafung->jabatanFungsional->nama_jafung ?? '');

                        if (str_contains($jafung, 'GURU BESAR') || str_contains($jafung, 'PROFESOR')) {
                            $gbCount++;
                        } elseif (str_contains($jafung, 'LEKTOR KEPALA')) {
                            $lkCount++;
                        } elseif (str_contains($jafung, 'LEKTOR')) {
                            $lCount++;
                        } elseif (str_contains($jafung, 'ASISTEN AHLI')) {
                            $aaCount++;
                        }
                    } else {
                        $njadCount++;
                    }
                } else {
                    $njadCount++;
                }
            }

            // Check cache
            $statsKey = 'prodi_stats_' . $prodi->id;
            $cachedStats = cache()->get($statsKey);

            if ($cachedStats && isset($cachedStats['njad'])) {
                $njadCount = $cachedStats['njad'];
                $aaCount = $cachedStats['aa'];
                $lCount = $cachedStats['l'];
                $lkCount = $cachedStats['lk'];
                $gbCount = $cachedStats['gb'];
                $totalDosen = $cachedStats['tetap'] + $cachedStats['calon_tetap'] +
                             $cachedStats['profesional'] + $cachedStats['perbantuan'];
            }

            $llkgb = $totalDosen > 0 ? min(($lCount + $lkCount + $gbCount) / $totalDosen, 1.0) : 0;
            $jfa = $totalDosen > 0 ? min(($aaCount + $lCount + $lkCount + $gbCount) / $totalDosen, 1.0) : 0;

            return [
                'id' => $prodi->id,
                'nama_prodi' => $prodi->position_name,
                'fakultas' => $prodi->prodi_parent()->first()->position_name ?? '-',
                'total_dosen' => $totalDosen,
                'njad' => $njadCount,
                'aa' => $aaCount,
                'l' => $lCount,
                'lk' => $lkCount,
                'gb' => $gbCount,
                'llkgb' => $llkgb,
                'jfa' => $jfa,
            ];
        });

        $totals = [
            'total_dosen' => $prodiStats->sum('total_dosen'),
            'njad' => $prodiStats->sum('njad'),
            'aa' => $prodiStats->sum('aa'),
            'l' => $prodiStats->sum('l'),
            'lk' => $prodiStats->sum('lk'),
            'gb' => $prodiStats->sum('gb'),
        ];
        $totals['llkgb'] = $totals['total_dosen'] > 0 ? min(($totals['l'] + $totals['lk'] + $totals['gb']) / $totals['total_dosen'], 1.0) : 0;
        $totals['jfa'] = $totals['total_dosen'] > 0 ? min(($totals['aa'] + $totals['l'] + $totals['lk'] + $totals['gb']) / $totals['total_dosen'], 1.0) : 0;

        return view('kelola_data.dashboard_prodi.fungsional', compact('prodiStats', 'totals'));
    }

    /**
     * Dashboard Kepegawaian - Statistik Status Pegawai
     */
    public function kepegawaian()
    {
        $prodis = work_position::where('type_work_position', 'Program Studi')->with('prodi_parent')->get();
        // dd($prodis);

        $prodiStats = $prodis->map(function ($prodi) {
            $dosens = $this->getDosens($prodi);
            $totalDosen = $dosens->count();

            // Hitung status kepegawaian
            $tetapCount = 0;
            $calonTetapCount = 0;
            $profesionalCount = 0;
            $perbantuanCount = 0;

            foreach ($dosens as $dosen) {
                if ($dosen->pegawai && $dosen->pegawai->riwayatNip) {
                    $latestNip = $dosen->pegawai->riwayatNip
                        ->where('is_active', true)
                        ->sortByDesc('tmt_mulai')
                        ->first();

                    if ($latestNip && $latestNip->statusPegawai) {
                        $status = strtoupper($latestNip->statusPegawai->status_pegawai ?? '');

                        if (str_contains($status, 'TETAP') && !str_contains($status, 'CALON')) {
                            $tetapCount++;
                        } elseif (str_contains($status, 'CALON')) {
                            $calonTetapCount++;
                        } elseif (str_contains($status, 'PROFESIONAL')) {
                            $profesionalCount++;
                        } elseif (str_contains($status, 'PERBANTUAN')) {
                            $perbantuanCount++;
                        }
                    }
                }
            }

            // Check cache
            $statsKey = 'prodi_stats_' . $prodi->id;
            $cachedStats = cache()->get($statsKey);

            if ($cachedStats && isset($cachedStats['tetap'])) {
                $tetapCount = $cachedStats['tetap'];
                $calonTetapCount = $cachedStats['calon_tetap'];
                $profesionalCount = $cachedStats['profesional'];
                $perbantuanCount = $cachedStats['perbantuan'];
                $totalDosen = $tetapCount + $calonTetapCount + $profesionalCount + $perbantuanCount;
            }

            return [
                'id' => $prodi->id,
                'nama_prodi' => $prodi->position_name,
                'fakultas' => $prodi->prodi_parent()->first()->position_name ?? '-',
                'total_dosen' => $totalDosen,
                'tetap' => $tetapCount,
                'calon_tetap' => $calonTetapCount,
                'profesional' => $profesionalCount,
                'perbantuan' => $perbantuanCount,
            ];
        });
        dd($prodiStats);

        $totals = [
            'total_dosen' => $prodiStats->sum('total_dosen'),
            'tetap' => $prodiStats->sum('tetap'),
            'calon_tetap' => $prodiStats->sum('calon_tetap'),
            'profesional' => $prodiStats->sum('profesional'),
            'perbantuan' => $prodiStats->sum('perbantuan'),
        ];

        return view('kelola_data.dashboard_prodi.kepegawaian', compact('prodiStats', 'totals'));
    }

    /**
     * Helper method to get pegawais (dosen and/or tpa)
     */
    private function getPegawais($prodi, $type = 'all')
    {
        $pegawais = collect([]);

        if ($type == 'all' || $type == 'dosen') {
            try {
                if (method_exists($prodi, 'dosen')) {
                    $dosens = $prodi->dosen()
                        ->with([
                            'pegawai.riwayatNip.statusPegawai',
                            'pegawai.riwayatJenjangPendidikan.refJenjangPendidikan'
                        ])
                        ->get();
                    $pegawais = $pegawais->merge($dosens);
                }
            } catch (\Exception $e) {
            }
        }

        if ($type == 'all' || $type == 'tpa') {
            try {
                if (method_exists($prodi, 'tpa')) {
                    $tpas = $prodi->tpa()
                        ->with([
                            'pegawai.riwayatNip.statusPegawai',
                            'pegawai.riwayatJenjangPendidikan.refJenjangPendidikan'
                        ])
                        ->get();
                    $pegawais = $pegawais->merge($tpas);
                }
            } catch (\Exception $e) {
            }
        }

        return $pegawais;
    }

    /**
     * Helper method to get dosens
     */
    private function getDosens($prodi)
    {
        $dosens = collect([]);

        try {
            if (method_exists($prodi, 'dosen')) {
                $dosens = $prodi->dosen()
                    ->with([
                        'pegawai.riwayatNip.statusPegawai',
                        'riwayatJabatanFungsional.jabatanFungsional',
                        'pegawai.riwayatJenjangPendidikan.refJenjangPendidikan'
                    ])
                    ->get();
            }
        } catch (\Exception $e) {
            $dosens = collect([]);
        }

        return $dosens;
    }
}
