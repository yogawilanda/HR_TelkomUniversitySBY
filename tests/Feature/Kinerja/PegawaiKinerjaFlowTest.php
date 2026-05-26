<?php

namespace Tests\Feature\Kinerja;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unit;
use App\Models\TargetKinerja;
use App\Models\TargetKinerjaHarian;
use App\Models\PelaporanPekerjaan;
use App\Models\RefSatuan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PegawaiKinerjaFlowTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected $connectionsToTransact = ['mysql'];

    protected function setUp(): void
    {
        parent::setUp();
        // Skip Vite manifest issues
        $this->withoutVite();
    }

    public function test_satuan_ukur_crud_api()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // 1. Create Satuan
        $response = $this->actingAs($admin)->postJson(route('manage.target-kinerja.ref-satuan.store'), [
            'nama' => 'Box',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('ref_satuan', ['nama' => 'Box']);
        $satuanId = $response->json('id');

        // 2. Read Satuan
        $response = $this->actingAs($admin)->getJson(route('manage.target-kinerja.ref-satuan.index'));
        $response->assertStatus(200);
        $response->assertJsonFragment(['nama' => 'Box']);

        // 3. Delete Satuan
        $response = $this->actingAs($admin)->deleteJson(route('manage.target-kinerja.ref-satuan.destroy', $satuanId));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('ref_satuan', ['id' => $satuanId]);
    }

    public function test_satuan_ukur_alternate_flows()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        // 1. Duplicate Name
        RefSatuan::create(['nama' => 'Pcs']);
        $response = $this->actingAs($admin)->postJson(route('manage.target-kinerja.ref-satuan.store'), [
            'nama' => 'Pcs',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nama']);

        // 2. Empty Name
        $response = $this->actingAs($admin)->postJson(route('manage.target-kinerja.ref-satuan.store'), [
            'nama' => '',
        ]);
        $response->assertStatus(422);

        // 3. Delete Satuan that is in USE
        $satuan = RefSatuan::create(['nama' => 'Kilogram']);
        TargetKinerja::create([
            'nama_kpi' => 'Test KPI Satuan',
            'satuan' => 'Kilogram',
            'tahun' => 2026,
            'is_active' => 1
        ]);

        $response = $this->actingAs($admin)->deleteJson(route('manage.target-kinerja.ref-satuan.destroy', $satuan->id));
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Satuan ini tidak bisa dihapus karena sedang digunakan oleh data KM/SM.']);
    }

    public function test_pegawai_can_create_and_delete_target_kinerja_harian()
    {
        $unit = Unit::create(['nama_unit' => 'Unit Test ' . uniqid(), 'kode_unit' => uniqid()]);
        
        $pegawai = User::factory()->create([
            'role' => 'pegawai',
            'is_admin' => false,
            'unit_id' => $unit->id
        ]);

        $targetKinerja = TargetKinerja::create([
            'nama_kpi' => 'Test KPI',
            'is_active' => 1,
            'tahun' => 2026,
            'responsibility_id' => $unit->id
        ]);

        // Pegawai creates target harian
        $response = $this->actingAs($pegawai)->post(route('manage.target-kinerja.harian.store'), [
            'pekerjaan' => 'Pekerjaan Harian Test',
            'target_kinerja_id' => $targetKinerja->id,
            'start' => now()->format('Y-m-d'),
            'end' => now()->format('Y-m-d'),
            'is_active' => 1
        ]);

        $response->assertRedirect(route('manage.target-kinerja.harian.list'));
        $this->assertDatabaseHas('target_kinerja_harian', [
            'pekerjaan' => 'Pekerjaan Harian Test'
        ]);

        $harian = TargetKinerjaHarian::where('pekerjaan', 'Pekerjaan Harian Test')->first();

        // Pegawai tries to delete it
        $responseDelete = $this->actingAs($pegawai)->delete(route('manage.target-kinerja.harian.destroy', $harian->id));
        $responseDelete->assertRedirect(route('manage.target-kinerja.harian.list'));
        $this->assertDatabaseMissing('target_kinerja_harian', ['id' => $harian->id]);
    }

    public function test_pegawai_cannot_delete_other_peoples_target()
    {
        $pegawai1 = User::factory()->create(['role' => 'pegawai', 'is_admin' => false]);
        $pegawai2 = User::factory()->create(['role' => 'pegawai', 'is_admin' => false]);

        $harian = TargetKinerjaHarian::create([
            'pekerjaan' => 'Pekerjaan Milik Pegawai 1',
            'is_active' => 1
        ]);

        $harian->pegawai()->attach($pegawai1->id, ['status' => 'pending']);

        // Pegawai 2 tries to delete Pegawai 1's target
        $response = $this->actingAs($pegawai2)->delete(route('manage.target-kinerja.harian.destroy', $harian->id));
        
        // TargetKinerjaHarianController::destroy uses abort(403)
        // If ExceptionHandler is enabled, this returns 403.
        $response->assertStatus(403);
    }

    public function test_pegawai_can_create_report()
    {
        $pegawai = User::factory()->create(['role' => 'pegawai', 'is_admin' => false]);

        $harian = TargetKinerjaHarian::create([
            'pekerjaan' => 'Pekerjaan Harian Test',
            'is_active' => 1
        ]);

        $response = $this->actingAs($pegawai)->post(route('manage.target-kinerja.harian.submit-report', $harian->id), [
            'waktu_pengerjaan' => 60,
            'realisasi' => 'Selesai 100%'
        ]);

        $response->assertRedirect(route('manage.target-kinerja.harian.list'));
        $this->assertDatabaseHas('pelaporan_pekerjaan', [
            'created_by' => $pegawai->id,
            'target_harian_id' => $harian->id,
            'realisasi' => 'Selesai 100%',
            'waktu_pengerjaan' => 60
        ]);
    }

    public function test_approval_restrictions()
    {
        $unitA = Unit::create(['nama_unit' => 'Unit A ' . uniqid(), 'kode_unit' => uniqid()]);
        $unitB = Unit::create(['nama_unit' => 'Unit B ' . uniqid(), 'kode_unit' => uniqid()]);

        $pegawaiA = User::factory()->create(['role' => 'pegawai', 'unit_id' => $unitA->id, 'is_admin' => false]);
        $atasanA = User::factory()->create(['role' => 'atasan', 'unit_id' => $unitA->id, 'is_admin' => false]);
        $atasanB = User::factory()->create(['role' => 'atasan', 'unit_id' => $unitB->id, 'is_admin' => false]);
        $admin = User::factory()->create(['is_admin' => true]);

        $harian = TargetKinerjaHarian::create(['pekerjaan' => 'Pekerjaan A', 'is_active' => 1]);

        $report = PelaporanPekerjaan::create([
            'user_id' => $pegawaiA->id,
            'created_by' => $pegawaiA->id,
            'target_harian_id' => $harian->id,
            'status' => 'pending',
            'waktu_pengerjaan' => 60
        ]);

        // 1. Pegawai cannot approve (returns 302 because of try-catch in controller)
        $resPegawai = $this->actingAs($pegawaiA)->get(route('manage.target-kinerja.harian.reports.approval', $report->id));
        $resPegawai->assertStatus(302);

        // 2. Atasan B (different unit) cannot approve
        $resAtasanB = $this->actingAs($atasanB)->get(route('manage.target-kinerja.harian.reports.approval', $report->id));
        $resAtasanB->assertStatus(302);

        // 3. Atasan A (same unit) CAN approve
        $resAtasanA = $this->actingAs($atasanA)->get(route('manage.target-kinerja.harian.reports.approval', $report->id));
        $resAtasanA->assertStatus(200);

        // 4. Admin CAN approve
        $resAdmin = $this->actingAs($admin)->get(route('manage.target-kinerja.harian.reports.approval', $report->id));
        $resAdmin->assertStatus(200);
    }
}
