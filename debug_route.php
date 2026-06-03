<?php

use App\Models\User;
use App\Models\TargetKinerja;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Login as admin
$user = User::where('email_institusi', 'admin@telkomuniversity.ac.id')->first();
Auth::login($user);

$request = Illuminate\Http\Request::create('/manage/target-kinerja/harian/input', 'GET');
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() !== 200) {
    if ($response instanceof \Illuminate\Http\Response && $response->exception) {
        echo "Error: " . $response->exception->getMessage() . "\n";
        echo "Trace: " . $response->exception->getTraceAsString() . "\n";
    } else {
        echo "Response Content: " . substr($response->getContent(), 0, 1000) . "...\n";
    }
} else {
    echo "Success!\n";
}
