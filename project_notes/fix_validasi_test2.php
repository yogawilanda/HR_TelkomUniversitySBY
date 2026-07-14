<?php

$testFile = 'tests/Feature/Dupak/ValidasiFlowTest.php';
$content = file_get_contents($testFile);

$old = <<<'PHP'
    protected static $sharedDbPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Use a shared SQLite file so both connections can see each other's tables
        if (!isset(self::$sharedDbPath)) {
            self::$sharedDbPath = ':memory:';
        }

        config(['database.connections.mysql.driver' => 'sqlite']);
        config(['database.connections.mysql.database' => self::$sharedDbPath]);
        config(['database.connections.dupak.driver' => 'sqlite']);
        config(['database.connections.dupak.database' => self::$sharedDbPath]);
PHP;

$new = <<<'PHP'
    protected static $sharedDbPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Use a shared SQLite file so both connections can see each other's tables
        if (!isset(self::$sharedDbPath)) {
            self::$sharedDbPath = sys_get_temp_dir() . '/dupak_test_' . getmypid() . '.sqlite';
        }

        config(['database.connections.mysql.driver' => 'sqlite']);
        config(['database.connections.mysql.database' => self::$sharedDbPath]);
        config(['database.connections.dupak.driver' => 'sqlite']);
        config(['database.connections.dupak.database' => self::$sharedDbPath]);
PHP;

$content = str_replace($old, $new, $content);
file_put_contents($testFile, $content);
echo "Done\n";
