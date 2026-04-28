<?php

// Read the test file
$testFile = 'tests/Feature/Dupak/ValidasiFlowTest.php';
$content = file_get_contents($testFile);

// Find the setUp method and replace it
$oldSetUp = <<<'PHP'
    protected function setUp(): void
    {
        parent::setUp();

        // Configure both connections to use SQLite in-memory for testing
        config(['database.connections.mysql.driver' => 'sqlite']);
        config(['database.connections.mysql.database' => ':memory:']);
        config(['database.connections.dupak.driver' => 'sqlite']);
        config(['database.connections.dupak.database' => ':memory:']);

        // Reconnect with new config
        \DB::purge('mysql');
        \DB::purge('dupak');
        \DB::reconnect('mysql');
        \DB::reconnect('dupak');

        // Run migrations for main database
        $this->artisan('migrate', [
            '--path' => 'database/migrations/default',
            '--database' => 'mysql',
        ])->run();

        // Run migrations for dupak database
        $this->artisan('migrate', [
            '--path' => 'database/migrations/dupak',
            '--database' => 'dupak',
        ])->run();
PHP;

$newSetUp = <<<'PHP'
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

        \DB::purge('mysql');
        \DB::purge('dupak');
        \DB::reconnect('mysql');
        \DB::reconnect('dupak');

        // Run migrations for main database
        $this->artisan('migrate', [
            '--path' => 'database/migrations/default',
            '--database' => 'mysql',
        ])->run();

        // Run migrations for dupak database
        $this->artisan('migrate', [
            '--path' => 'database/migrations/dupak',
            '--database' => 'dupak',
        ])->run();
PHP;

$content = str_replace($oldSetUp, $newSetUp, $content);
file_put_contents($testFile, $content);
echo "Done\n";
