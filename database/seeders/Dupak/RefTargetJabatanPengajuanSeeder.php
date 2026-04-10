<?php

namespace Database\Seeders\Dupak;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefTargetJabatanPengajuanSeeder extends Seeder
{
    public function run(): void
    {
        $aa = 'b467678d-8e9f-4453-bb76-f0cba91468dc';
        $lektor = 'f6890047-b0ea-4b45-a9f9-b0584c65bdd6';
        $lk = '21ac00aa-1f19-4347-84c1-9e70413209ab';
        $gb = 'd6418a5e-b76f-4d67-9990-056e1acabe66';

        $data = [
            [
                'jfaAsal' => $aa,
                'jfaTujuan' => $lektor,
                'kumTarget' => 200.00,
                'keterangan' => 'Kenaikan dari Asisten Ahli ke Lektor'
            ],
            [
                'jfaAsal' => $lektor,
                'jfaTujuan' => $lk,
                'kumTarget' => 400.00,
                'keterangan' => 'Kenaikan dari Lektor ke Lektor Kepala'
            ],
            [
                'jfaAsal' => $lk,
                'jfaTujuan' => $gb,
                'kumTarget' => 850.00,
                'keterangan' => 'Kenaikan dari Lektor Kepala ke Guru Besar'
            ],
        ];

        foreach ($data as $item) {
            DB::connection('dupak')->table('ref_target_jabatan_pengajuan')->updateOrInsert(
                ['jfaAsal' => $item['jfaAsal'], 'jfaTujuan' => $item['jfaTujuan']],
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
