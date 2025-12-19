<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PelaporanPekerjaan;
use App\Models\TargetKinerjaHarian;
use App\Models\KinerjaUnit;
use App\Models\Tpa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PelaporanPekerjaanController extends Controller
{
    // Untuk backward compatibility dengan target kinerja harian
    public function create($targetHarianId)
    {
        $target = TargetKinerjaHarian::findOrFail($targetHarianId);
        return view('kelola_data.pelaporan_pekerjaan.create', compact('target'));
    }

    // Untuk KinerjaUnit
    public function createForKinerjaUnit($kinerjaUnitId)
    {
        $kinerjaUnit = KinerjaUnit::with('kontrakUnit')->findOrFail($kinerjaUnitId);
        $tpaList = Tpa::with('pegawai')->get();
        return view('kelola_data.pelaporan_pekerjaan.create_kinerja_unit', compact('kinerjaUnit', 'tpaList'));
    }

    public function store(Request $request, $targetHarianId)
    {
        $target = TargetKinerjaHarian::findOrFail($targetHarianId);

        $data = $request->validate([
            'realisasi' => 'nullable|string',
            'referensi_set_target_id' => 'nullable|exists:target_kinerja_harian,id',
            'realisasi_jumlah' => 'nullable|integer',
            'realisasi_waktu_minutes' => 'nullable|integer',
            'pencapaian_percent' => 'nullable|integer',
            'evidence' => 'nullable|string',
        ]);

        $report = PelaporanPekerjaan::create([
            'target_harian_id' => $target->id,
            'realisasi' => $data['realisasi'] ?? null,
            'referensi_set_target_id' => $data['referensi_set_target_id'] ?? $target->id,
            'realisasi_jumlah' => $data['realisasi_jumlah'] ?? null,
            'realisasi_waktu_minutes' => $data['realisasi_waktu_minutes'] ?? null,
            'status' => 'pending',
            'pencapaian_percent' => $data['pencapaian_percent'] ?? null,
            'evidence' => $data['evidence'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return Redirect::route('manage.target-kinerja.harian.list')->with('success', 'Laporan pekerjaan berhasil disimpan');
    }

    // Store untuk KinerjaUnit
    public function storeForKinerjaUnit(Request $request, $kinerjaUnitId)
    {
        $kinerjaUnit = KinerjaUnit::findOrFail($kinerjaUnitId);

        $data = $request->validate([
            'realisasi' => 'nullable|string',
            'realisasi_jumlah' => 'nullable|integer',
            'realisasi_waktu_minutes' => 'nullable|integer',
            'pencapaian_percent' => 'nullable|integer',
            'evidence' => 'nullable|string',
            'tpa_id' => 'nullable|exists:tpas,id',
        ]);

        $report = PelaporanPekerjaan::create([
            'kinerja_unit_id' => $kinerjaUnit->id,
            'tpa_id' => $data['tpa_id'] ?? null,
            'realisasi' => $data['realisasi'] ?? null,
            'realisasi_jumlah' => $data['realisasi_jumlah'] ?? null,
            'realisasi_waktu_minutes' => $data['realisasi_waktu_minutes'] ?? null,
            'status' => 'pending',
            'pencapaian_percent' => $data['pencapaian_percent'] ?? null,
            'evidence' => $data['evidence'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Update total realisasi di KinerjaUnit
        $this->updateKinerjaUnitTotals($kinerjaUnit);

        return Redirect::route('manage.kontrak-unit.view', $kinerjaUnit->kontrak_unit_id)
            ->with('success', 'Laporan pekerjaan berhasil disimpan');
    }

    private function updateKinerjaUnitTotals(KinerjaUnit $kinerjaUnit)
    {
        $totals = $kinerjaUnit->pelaporanPekerjaan()
            ->selectRaw('
                SUM(COALESCE(approved_jumlah, realisasi_jumlah)) as total_jumlah,
                SUM(COALESCE(approved_waktu_minutes, realisasi_waktu_minutes)) as total_waktu
            ')
            ->first();

        $kinerjaUnit->update([
            'total_realisasi_jumlah' => $totals->total_jumlah ?? 0,
            'total_realisasi_waktu_minutes' => $totals->total_waktu ?? 0,
        ]);
    }

    public function approvalList()
    {
        $items = PelaporanPekerjaan::with(['targetHarian', 'kinerjaUnit.kontrakUnit', 'tpa.pegawai'])
            ->orderBy('id', 'desc')
            ->get();
        return view('kelola_data.pelaporan_pekerjaan.list', compact('items'));
    }

    public function showApproval($id)
    {
        $item = PelaporanPekerjaan::with(['targetHarian', 'kinerjaUnit.kontrakUnit', 'tpa.pegawai'])
            ->findOrFail($id);
        return view('kelola_data.pelaporan_pekerjaan.approval', compact('item'));
    }

    public function approve(Request $request, $id)
    {
        $item = PelaporanPekerjaan::findOrFail($id);

        $data = $request->validate([
            'approved_jumlah' => 'nullable|integer',
            'approved_waktu_minutes' => 'nullable|integer',
            'assignment_status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'pencapaian_percent' => 'nullable|integer',
            'evidence' => 'nullable|string',
        ]);

        $item->approved_jumlah = $data['approved_jumlah'] ?? null;
        $item->approved_waktu_minutes = $data['approved_waktu_minutes'] ?? null;
        $item->approved_by = Auth::id();
        // set report status if provided (follow approval form)
        if (!empty($data['assignment_status'])) {
            $item->status = $data['assignment_status'];
        }
        // save pencapaian and evidence on the report
        if (array_key_exists('pencapaian_percent', $data)) {
            $item->pencapaian_percent = $data['pencapaian_percent'];
        }
        $item->save();

        // If an assignment status was provided, update the pivot status for the related target_kinerja and the report creator
        if (!empty($data['assignment_status'])) {
            $targetHarian = $item->targetHarian;
            if ($targetHarian && $targetHarian->target_kinerja_id && $item->created_by) {
                $targetKinerja = \App\Models\TargetKinerja::find($targetHarian->target_kinerja_id);
                if ($targetKinerja) {
                    // update pivot where user_id == created_by
                    $exists = $targetKinerja->pegawai()->where('users.id', $item->created_by)->exists();
                    if ($exists) {
                        $targetKinerja->pegawai()->updateExistingPivot($item->created_by, ['status' => $data['assignment_status']]);
                    }
                }
            }
        }

        // Update KinerjaUnit totals if this report is related to KinerjaUnit
        if ($item->kinerja_unit_id) {
            $this->updateKinerjaUnitTotals($item->kinerjaUnit);
        }

        return Redirect::route('manage.target-kinerja.harian.reports')->with('success', 'Laporan disetujui');
    }
}