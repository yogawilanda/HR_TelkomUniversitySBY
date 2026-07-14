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

class ValidasiFlowTest extends TestCase
{
    protected User $tpakUser;
    protected Dosen $tpakDosen;
    protected User $pengajuUser;
    protected Dosen $pengajuDosen;
    protected User $adminUser;
    protected Pengajuan $pengajuan;

    protected function setUp(): void
    {
        parent::setUp();

        // konfigurasi untuk memuat driver dan database per masing masing db,
        // jika hanya memanggil database tanpa memanggil data yang perlu digunakan, maka bisa jadi memanggil 1 koneksi yang sama.
        config(['database.connections.mysql.driver' => 'sqlite']);
        config(['database.connections.mysql.database' => ':memory:']);
        config(['database.connections.dupak.driver' => 'sqlite']);
        config(['database.connections.dupak.database' => ':memory:']);

        // memutus koneksi dahulu sebelum memanggil ulang koneksi
        DB::purge('mysql');
        DB::purge('dupak');
        DB::reconnect('mysql');
        DB::reconnect('dupak');

        // proses migrasi melalui jalur testing untuk memastikan bahwa migrasi sudah disiapkan dan dialokasikan ke database yang sudah benar.
        $this->artisan('migrate', [
            '--path' => 'database/migrations/default',
            '--database' => 'mysql',
        ])->run();

        // sama dengan yang atas, namun untuk database dupak.
        $this->artisan('migrate', [
            '--path' => 'database/migrations/dupak',
            '--database' => 'dupak',
        ])->run();

        // testing untuk membuat user yang menjadi TPAK dan belum tentu adalah dosen.
        $this->tpakUser = User::factory()->create([
            'nama_lengkap' => 'TPAK Test',
            'email_institusi' => 'tpak@test.com',
            'is_admin' => false,
        ]);

        // membuat dosen berdasarkan factor dan model yang ada yaitu dosen.
        $this->tpakDosen = Dosen::factory()->create([
            'users_id' => $this->tpakUser->id,
            'nidn' => '1111111111',
        ]);

        $this->pengajuUser = User::factory()->create([
            'nama_lengkap' => 'Pengaju Test',
            'email_institusi' => 'pengaju@test.com',
            'is_admin' => false,
        ]);

        $this->pengajuDosen = Dosen::factory()->create([
            'users_id' => $this->pengajuUser->id,
            'nidn' => '2222222222',
        ]);

        $this->adminUser = User::factory()->create([
            'nama_lengkap' => 'Admin Test',
            'email_institusi' => 'admin@test.com',
            'is_admin' => true,
        ]);

        $this->pengajuan = Pengajuan::create([
            'idDosen' => $this->pengajuDosen->id,
            'status' => 'Diajukan',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
        ]);

        PenunjukanTPAKModel::create([
            'pengajuan_id' => $this->pengajuan->id,
            'idDosenTpak' => $this->tpakDosen->id,
        ]);
    }

    #[Test]
    public function tpak_can_access_validasi_index(): void
    {
        $this->actingAs($this->tpakUser);

        $response = $this->get(route('dupak.validasi.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dupak.validasi.index');
    }

    #[Test]
    public function tpak_can_view_assigned_pengajuan(): void
    {
        $this->actingAs($this->tpakUser);

        $response = $this->get(route('dupak.validasi.show', $this->pengajuan->id));
        $response->assertStatus(200);
        $response->assertViewIs('dupak.validasi.show');
        $response->assertViewHas('pengajuan');
        $response->assertViewHas('myEvaluations');
        $response->assertViewHas('otherEvaluations');
    }

    #[Test]
    public function non_assigned_tpak_cannot_view_pengajuan(): void
    {
        $otherTpakUser = User::factory()->create([
            'nama_lengkap' => 'Other TPAK',
            'email_institusi' => 'othertpak@test.com',
            'is_admin' => false,
        ]);

        Dosen::factory()->create([
            'users_id' => $otherTpakUser->id,
            'nidn' => '3333333333',
        ]);

        $this->actingAs($otherTpakUser);

        $response = $this->get(route('dupak.validasi.show', $this->pengajuan->id));
        $response->assertStatus(403);
    }

    #[Test]
    public function tpak_can_save_evaluation(): void
    {
        $this->actingAs($this->tpakUser);

        $detail = DetailPengajuan::create([
            'pengajuan_id' => $this->pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 100.00,
        ]);

        $response = $this->patch(route('dupak.validasi.update', $this->pengajuan->id), [
            'scores' => [$detail->id => 80],
            'flags' => [$detail->id => 'OK'],
            'notes' => [$detail->id => 'Bagus'],
            'status' => 'Approved',
            'overall_notes' => 'Semua OK',
        ]);

        $response->assertRedirect(route('dupak.validasi.show', $this->pengajuan->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('hasil_evaluasi', [
            'detail_pengajuan_id' => $detail->id,
            'idUserPemeriksa' => $this->tpakUser->id,
            'status_evaluasi' => 'OK',
            'nilai_angka_kredit' => 80.00,
        ], 'dupak');

        $this->assertDatabaseHas('detail_pengajuan', [
            'id' => $detail->id,
            'status' => 'approved',
        ], 'dupak');

        $this->assertDatabaseHas('pengajuan', [
            'id' => $this->pengajuan->id,
            'status' => 'Diterima',
        ], 'dupak');
    }

    #[Test]
    public function tpak_can_update_existing_evaluation(): void
    {
        $this->actingAs($this->tpakUser);

        $detail = DetailPengajuan::create([
            'pengajuan_id' => $this->pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 100.00,
        ]);

        HasilEvaluasi::create([
            'detail_pengajuan_id' => $detail->id,
            'idUserPemeriksa' => $this->tpakUser->id,
            'peran_pemeriksa' => 'TPAK',
            'status_evaluasi' => 'OK',
            'nilai_angka_kredit' => 50.00,
            'catatan' => 'Initial',
        ]);

        $response = $this->patch(route('dupak.validasi.update', $this->pengajuan->id), [
            'scores' => [$detail->id => 90],
            'flags' => [$detail->id => 'Doubt'],
            'notes' => [$detail->id => 'Updated note'],
        ]);

        $response->assertSessionHas('success');

        $evalCount = HasilEvaluasi::where('detail_pengajuan_id', $detail->id)
            ->where('idUserPemeriksa', $this->tpakUser->id)
            ->count();
        $this->assertEquals(1, $evalCount);

        $this->assertDatabaseHas('hasil_evaluasi', [
            'detail_pengajuan_id' => $detail->id,
            'idUserPemeriksa' => $this->tpakUser->id,
            'status_evaluasi' => 'Doubt',
            'nilai_angka_kredit' => 90.00,
            'catatan' => 'Updated note',
        ], 'dupak');
    }

    #[Test]
    public function evaluation_with_fake_flag_sets_detail_rejected(): void
    {
        $this->actingAs($this->tpakUser);

        $detail = DetailPengajuan::create([
            'pengajuan_id' => $this->pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 100.00,
        ]);

        $response = $this->patch(route('dupak.validasi.update', $this->pengajuan->id), [
            'scores' => [$detail->id => 0],
            'flags' => [$detail->id => 'Fake'],
            'notes' => [$detail->id => 'Palsu'],
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('detail_pengajuan', [
            'id' => $detail->id,
            'status' => 'rejected',
        ], 'dupak');
    }

    #[Test]
    public function evaluation_with_doubt_flag_sets_detail_revision(): void
    {
        $this->actingAs($this->tpakUser);

        $detail = DetailPengajuan::create([
            'pengajuan_id' => $this->pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 100.00,
        ]);

        $response = $this->patch(route('dupak.validasi.update', $this->pengajuan->id), [
            'scores' => [$detail->id => 50],
            'flags' => [$detail->id => 'Doubt'],
            'notes' => [$detail->id => 'Perlu klarifikasi'],
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('detail_pengajuan', [
            'id' => $detail->id,
            'status' => 'revision',
        ], 'dupak');
    }

    #[Test]
    public function status_update_maps_correctly(): void
    {
        $this->actingAs($this->tpakUser);

        $detail = DetailPengajuan::create([
            'pengajuan_id' => $this->pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 100.00,
        ]);

        $this->patch(route('dupak.validasi.update', $this->pengajuan->id), [
            'scores' => [$detail->id => 0],
            'flags' => [$detail->id => 'Fake'],
            'status' => 'Rejected',
        ]);

        $this->assertDatabaseHas('pengajuan', [
            'id' => $this->pengajuan->id,
            'status' => 'Ditolak',
        ], 'dupak');

        $this->pengajuan->update(['status' => 'Diajukan']);

        $this->patch(route('dupak.validasi.update', $this->pengajuan->id), [
            'scores' => [$detail->id => 50],
            'flags' => [$detail->id => 'Doubt'],
            'status' => 'Revision',
        ]);

        $this->assertDatabaseHas('pengajuan', [
            'id' => $this->pengajuan->id,
            'status' => 'Revisi',
        ], 'dupak');
    }

    #[Test]
    public function overall_notes_saved_to_penunjukan_tpak(): void
    {
        $this->actingAs($this->tpakUser);

        $detail = DetailPengajuan::create([
            'pengajuan_id' => $this->pengajuan->id,
            'deskripsi_kegiatan' => 'Test Kegiatan',
            'angka_kredit_total' => 100.00,
        ]);

        $response = $this->patch(route('dupak.validasi.update', $this->pengajuan->id), [
            'scores' => [$detail->id => 100],
            'flags' => [$detail->id => 'OK'],
            'overall_notes' => 'Catatan umum dari TPAK',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penunjukan_tpak', [
            'pengajuan_id' => $this->pengajuan->id,
            'idDosenTpak' => $this->tpakDosen->id,
            'catatan' => 'Catatan umum dari TPAK',
        ], 'dupak');
    }

    #[Test]
    public function validasi_index_shows_progress_statistics(): void
    {
        $this->actingAs($this->tpakUser);

        $detail1 = DetailPengajuan::create([
            'pengajuan_id' => $this->pengajuan->id,
            'deskripsi_kegiatan' => 'Kegiatan 1',
            'angka_kredit_total' => 100.00,
        ]);

        $detail2 = DetailPengajuan::create([
            'pengajuan_id' => $this->pengajuan->id,
            'deskripsi_kegiatan' => 'Kegiatan 2',
            'angka_kredit_total' => 50.00,
        ]);

        HasilEvaluasi::create([
            'detail_pengajuan_id' => $detail1->id,
            'idUserPemeriksa' => $this->tpakUser->id,
            'peran_pemeriksa' => 'TPAK',
            'status_evaluasi' => 'OK',
            'nilai_angka_kredit' => 80.00,
        ]);

        $response = $this->get(route('dupak.validasi.index'));
        $response->assertStatus(200);

        $response->assertViewHas('selesaiCount', 1);
        $response->assertViewHas('totalTugas', 2);
    }

    #[Test]
    public function filter_by_status_works_on_validasi_index(): void
    {
        $this->actingAs($this->tpakUser);

        $pengajuan2 = Pengajuan::create([
            'idDosen' => $this->pengajuDosen->id,
            'status' => 'Diterima',
            'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111',
            'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
            'start' => '2024-01-01',
            'end' => '2024-12-31',
            'TahunAjaranAjuanAwal' => '2023/2024',
            'TahunAjaranAjuanAkhir' => '2024/2025',
            'semesterAjuan' => 'Ganjil',
        ]);

        PenunjukanTPAKModel::create([
            'pengajuan_id' => $pengajuan2->id,
            'idDosenTpak' => $this->tpakDosen->id,
        ]);

        DetailPengajuan::create([
            'pengajuan_id' => $pengajuan2->id,
            'deskripsi_kegiatan' => 'Kegiatan Diterima',
            'angka_kredit_total' => 100.00,
        ]);

        $response = $this->get(route('dupak.validasi.index', ['status' => 'Diterima']));
        $response->assertStatus(200);
    }
}
