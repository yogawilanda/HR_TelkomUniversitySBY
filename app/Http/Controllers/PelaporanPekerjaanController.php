<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PelaporanPekerjaan;
use App\Models\TargetKinerjaHarian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PelaporanPekerjaanController extends Controller
{
    public function create($targetHarianId)
    {
        $target = TargetKinerjaHarian::findOrFail($targetHarianId);
        return view('kelola_data.pelaporan_pekerjaan.create', compact('target'));
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

    public function approvalList()
    {
        $items = PelaporanPekerjaan::with('targetHarian')->orderBy('id', 'desc')->get();
        return view('kelola_data.pelaporan_pekerjaan.list', compact('items'));
    }

    public function showApproval($id)
    {
        $item = PelaporanPekerjaan::with('targetHarian')->findOrFail($id);
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
        if (array_key_exists('evidence', $data)) {
            $item->evidence = $data['evidence'];
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

        return Redirect::route('manage.target-kinerja.harian.reports')->with('success', 'Laporan disetujui');
    }
}
