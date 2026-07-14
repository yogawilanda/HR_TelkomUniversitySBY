<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Prodi;

echo "=== CLEAR PRODI STATISTICS CACHE ===\n\n";

$prodis = Prodi::all();

foreach ($prodis as $prodi) {
    $statsKey = 'prodi_stats_' . $prodi->id;

    if (cache()->has($statsKey)) {
        cache()->forget($statsKey);
        echo "✓ Cleared cache for: {$prodi->nama_prodi}\n";
    }
}

echo "\n✅ All prodi statistics cache cleared!\n";
echo "Refresh your browser to see updated calculations.\n";
