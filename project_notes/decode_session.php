<?php
// decode_session.php
// Usage: php decode_session.php <session_id>

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
// Bootstrap the application kernel so facades work
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if ($argc < 2) {
    echo "Usage: php decode_session.php <session_id>\n";
    exit(1);
}

$sessionId = $argv[1];

use Illuminate\Support\Facades\DB;

$row = DB::table('sessions')->where('id', $sessionId)->first();
if (! $row) {
    echo "Session not found for id: $sessionId\n";
    exit(1);
}

$payload = base64_decode($row->payload);

// The payload is usually a serialized array of key=>value pairs (maybe encrypted depending on session settings)
// Attempt to unserialize; catch errors

$decoded = null;
// Some Laravel installations serialize using serialize(); others may be encrypted.
// Try to unserialize directly
try {
    $decoded = @unserialize($payload);
} catch (\Throwable $e) {
    $decoded = false;
}

if ($decoded === false) {
    // Try to json_decode if it's json
    $json = @json_decode($payload, true);
    if ($json !== null) {
        echo "Decoded as JSON:\n";
        print_r($json);
        exit(0);
    }

    echo "Could not unserialize payload; raw payload:\n";
    // show some safe hexdump or raw print
    echo $payload . "\n";
    exit(0);
}

echo "Unserialized payload:\n";
print_r($decoded);

exit(0);
