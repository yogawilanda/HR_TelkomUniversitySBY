<?php
$file = 'tests/Feature/Dupak/ValidasiFlowTest.php';
$s = file_get_contents($file);

// Fix the corrupted setUp signature — replace everything between "$pengajuan;" and "parent::setUp();"
$b = '    protected Pengajuan $pengajuan;';
$a = '    protected function setUp(): void
    {';

$tmp = explode($b, $s, 2);
if (count($tmp) === 2) {
    $before = $tmp[0] . $b . "\n";
    $rest = $tmp[1];
    // Find first occurrence of 'parent::setUp();' and replace everything before it
    $pos = strpos($rest, '        parent::setUp();');
    if ($pos !== false) {
        $after = substr($rest, $pos); // keeps from parent::setUp() onwards
        // Replace :memory: with temp file path
        $after = str_replace(
            "config(['database.connections.mysql.database' => ':memory:']);\n        config(['database.connections.dupak.database' => ':memory:']);",
            "if (!isset(self::\\$sharedDbPath)) {
            self::\\$sharedDbPath = sys_get_temp_dir() . '/dupak_test_' . getmypid() . '.sqlite';
        }

        config(['database.connections.mysql.database' => self::\\$sharedDbPath]);
        config(['database.connections.dupak.database' => self::\\$sharedDbPath]);",
            $after
        );
        // Add the static property declaration before setUp
        $property = "\n    protected static string \\$sharedDbPath;\n\n";
        $newContent = $before . $property . $a . "\n" . $after;
        file_put_contents($file, $newContent);
        echo "Fixed\n";
    } else {
        echo "Could not find parent::setUp();\n";
    }
} else {
    echo "Could not find split point\n";
}
