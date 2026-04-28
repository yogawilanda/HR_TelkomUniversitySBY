# TODO: Fix PengajuanFlowTest Database Connection Failures

- [x] Step 1: Clear Laravel config cache (`php artisan config:clear`)
- [ ] Step 2: Update `config/database.php` to read `DUPAK_DB_CONNECTION` from env
- [ ] Step 3: Fix `PengajuanFlowTest.php` - replace `/** @test */` with `#[Test]` attributes
- [ ] Step 4: Fix `ValidasiFlowTest.php` - remove `RefreshDatabase`, replace annotations with attributes
- [ ] Step 5: Run tests and fix any remaining failures

