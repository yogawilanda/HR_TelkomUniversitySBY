<?php

namespace Tests\Feature\Dupak;

use App\Models\Dosen;
use App\Models\Dupak\DetailPengajuan;
use App\Models\Dupak\HasilEvaluasi;
use App\Models\Dupak\Pengajuan;
use App\Models\Dupak\PenunjukanTPAKModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PengajuanFlowTest extends TestCase
{
    protected User $dosenUser;
    protected Dosen $dosen;
    protected User $otherUser;
    protected Dosen $otherDosen;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.mysql.driver' => 'sqlite']);
        config(['database.connections.mysql.database' => ':memory:']);
        config(['database.connections.dupak.driver' => 'sqlite']);
        config(['database.connections.dupak.database' => ':memory:']);

        DB::purge('mysql');
        DB::purge('dupak');
        DB::reconnect('mysql');
        DB::reconnect('dupak');

        $this->artisan('migrate', [
            '--path' => 'database/migrations/default',
            '--database' => 'mysql',
        ])->run();

        $this->artisan('migrate', [
            '--path' => 'database/migrations/dupak',
            '--database' => 'dupak',
        ])->run();

        $this->dosenUser = User::factory()->create([
            'nama_lengkap' => 'Dosen Test',
            'email_institusi' => 'dosen@test.com',
            'is_admin' => false,
        ]);

        $this->dosen = Dosen::factory()->create([
            'users_id' => $this->dosenUser->id,
            'nidn' => '1234567890',
        ]);

        $this->otherUser = User::factory()->create([
            'nama_lengkap' => 'Other Dosen',
            'email_institusi' => 'other@test.com',
            'is_admin' => false,
        ]);

        $this->otherDosen = Dosen::factory()->create([
            'users_id' => $this->otherUser->id,
            'nidn' => '0987654321',
        ]);

        $this->adminUser = User::factory()->create([
            'nama_lengkap' => 'Admin Test',
            'email_institusi' => 'admin@test.com',
            'is_admin' => true,
        ]);
    }

    #[Test]
    public function guest_cannot_access_pengajuan_pages(): void
    {
        $response = $this->get(route('dupak.pengajuan.index'));
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_dosen_can_view_pengajuan_list(): void
    {
        $this->actingAs($this->dosenUser);
        $response = $this->get(route('dupak.pengajuan.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dupak.pengajuan.index');
    }

    #[Test]
    public function dosen_can_create_pengajuan(): void
    {
        $this->actingAs($this->dosenUser);
        $response = $this->get(route('dupak.pengajuan.create'));
        $response->assertStatus(200);
        $response->assertViewIs('dupak.pengajuan.create');
    }

    #[Test]
    public function non_dosen_cannot_create_pengajuan(): void
    {
        $nonDosenUser = User::factory()->create(['is_admin' => false]);
        $this->actingAs($nonDosenUser);
        $response = $this->post(route('dupak.pengajuan.store'));
        $response->assertRedirect(route('dupak.dashboard'));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function dosen_can_submit_pengajuan_from_draft_status(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        DetailPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 10.5,
        ]);

        $response = $this->post(route('dupak.pengajuan.submit', $pengajuan->id));
        $response->assertRedirect(route('dupak.pengajuan.show', $pengajuan->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan', [
            'id' => $pengajuan->id,
            'status' => 'Diajukan',
        ], 'dupak');
    }

    #[Test]
    public function dosen_cannot_submit_pengajuan_without_details(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->post(route('dupak.pengajuan.submit', $pengajuan->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('pengajuan', [
            'id' => $pengajuan->id,
            'status' => 'Draft',
        ], 'dupak');
    }

    #[Test]
    public function dosen_cannot_submit_others_pengajuan(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->otherDosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->post(route('dupak.pengajuan.submit', $pengajuan->id));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function dosen_cannot_submit_pengajuan_with_invalid_status(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Diajukan',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->post(route('dupak.pengajuan.submit', $pengajuan->id));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function dosen_can_edit_own_draft_pengajuan(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->get(route('dupak.pengajuan.edit', $pengajuan->id));
        $response->assertStatus(200);
        $response->assertViewIs('dupak.pengajuan.edit');
        $response->assertViewHas('pengajuan');
    }

    #[Test]
    public function dosen_cannot_edit_others_pengajuan(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->otherDosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->get(route('dupak.pengajuan.edit', $pengajuan->id));
        $response->assertRedirect(route('dupak.dashboard'));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function dosen_cannot_edit_pengajuan_with_diajukan_status(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Diajukan',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->get(route('dupak.pengajuan.edit', $pengajuan->id));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function dosen_can_update_own_pengajuan(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
            'TahunAjaranAjuanAwal' => '2023/2024',
        ]);

        $response = $this->patch(route('dupak.pengajuan.update', $pengajuan->id), [
            'TahunAjaranAjuanAwal' => '2024/2025',
            'TahunAjaranAjuanAkhir' => '2025/2026',
            'semesterAjuan' => 'Ganjil',
        ]);

        $response->assertRedirect(route('dupak.pengajuan.show', $pengajuan->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan', [
            'id' => $pengajuan->id,
            'TahunAjaranAjuanAwal' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
        ], 'dupak');
    }

    #[Test]
    public function dosen_can_delete_own_draft_pengajuan(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $detail = DetailPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 10.5,
        ]);

        $response = $this->delete(route('dupak.pengajuan.destroy', $pengajuan->id));

        $response->assertRedirect(route('dupak.pengajuan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('pengajuan', ['id' => $pengajuan->id], 'dupak');
        $this->assertDatabaseMissing('detail_pengajuan', ['id' => $detail->id], 'dupak');
    }

    #[Test]
    public function dosen_cannot_delete_others_pengajuan(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->otherDosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->delete(route('dupak.pengajuan.destroy', $pengajuan->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('pengajuan', ['id' => $pengajuan->id], 'dupak');
    }

    #[Test]
    public function dosen_cannot_delete_pengajuan_with_diajukan_status(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Diajukan',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->delete(route('dupak.pengajuan.destroy', $pengajuan->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('pengajuan', ['id' => $pengajuan->id], 'dupak');
    }

    #[Test]
    public function admin_can_edit_any_pengajuan(): void
    {
        $this->actingAs($this->adminUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->get(route('dupak.pengajuan.edit', $pengajuan->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_delete_any_pengajuan(): void
    {
        $this->actingAs($this->adminUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $response = $this->delete(route('dupak.pengajuan.destroy', $pengajuan->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('pengajuan', ['id' => $pengajuan->id], 'dupak');
    }

    #[Test]
    public function destroy_cascade_deletes_related_data(): void
    {
        $this->actingAs($this->dosenUser);

        $pengajuan = Pengajuan::create([
            'idDosen' => $this->dosen->id,
            'status' => 'Draft',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
        ]);

        $detail = DetailPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 10.5,
        ]);

        PenunjukanTPAKModel::create([
            'pengajuan_id' => $pengajuan->id,
            'idDosenTpak' => $this->otherDosen->id,
        ]);

        HasilEvaluasi::create([
            'detail_pengajuan_id' => $detail->id,
            'idUserPemeriksa' => $this->otherDosen->id,
            'peran_pemeriksa' => 'TPAK',
            'status_evaluasi' => 'OK',
            'nilai_angka_kredit' => 10.5,
        ]);

        $response = $this->delete(route('dupak.pengajuan.destroy', $pengajuan->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('pengajuan', ['id' => $pengajuan->id], 'dupak');
        $this->assertDatabaseMissing('detail_pengajuan', ['id' => $detail->id], 'dupak');
        $this->assertDatabaseMissing('penunjukan_tpak', ['pengajuan_id' => $pengajuan->id], 'dupak');
        $this->assertDatabaseMissing('hasil_evaluasi', ['detail_pengajuan_id' => $detail->id], 'dupak');
    }
}
