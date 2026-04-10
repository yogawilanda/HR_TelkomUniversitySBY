<?php

namespace Database\Seeders\Dupak;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefJenisInputSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Text', 'Number', 'Date', 'File/PDF', 'Link/URL', 'Dropdown'];

        foreach ($types as $type) {
            DB::connection('dupak')->table('ref_jenis_input')->updateOrInsert(
                ['nama' => $type],
                [
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
