<?php

namespace Database\Seeders;

use App\Models\RefBagian;
use App\Models\RefJabatanFungsional;
use App\Models\refJabatanFungsionalKeahlian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
public function run(): void
{
    
    
    $path = database_path('sdm_new.sql');

    $sql = File::get($path);

    DB::unprepared($sql);

    $this->call([
        UserSeeder::class,
        \Database\Seeders\Dupak\PengajuanSeeder::class, // Tambahkan ini agar dijalankan setelah SQL
    ]);
}

}
