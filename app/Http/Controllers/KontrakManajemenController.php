<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KontrakManajemen;
use App\Models\KontrakUnit;
use App\Models\KinerjaUnit;
use App\Models\PelaporanPekerjaan;
use Illuminate\Support\Facades\Redirect;

class KontrakManajemenController extends Controller
{
    public function index()
    {
        $items = KontrakManajemen::with('kontrakUnit')->orderBy('id', 'desc')->get();
        return view('kelola_data.kontrak_manajemen.list', ['kontrakManajemen' => $items]);
    }

    public function create()
    {
        return view('kelola_data.kontrak_manajemen.input');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'bobot' => 'nullable|integer',
            'responsibility' => 'nullable|string',
            'satuan' => 'nullable|string',
            'target_percent' => 'nullable|numeric',
            'status' => 'nullable|string',
            'unit_penanggung_jawab' => 'nullable|string',
            'periode' => 'nullable|string',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);

        $kontrakManajemen = KontrakManajemen::create($validated);

        return redirect()
            ->route('manage.kontrak-manajemen.view', $kontrakManajemen->id)
            ->with('success', 'Kontrak Manajemen berhasil dibuat');
    }

    public function show($id)
    {
        $kontrakManajemen = KontrakManajemen::with(['kontrakUnit.kinerjaUnit.pelaporanPekerjaan'])->findOrFail($id);
        return view('kelola_data.kontrak_manajemen.view', compact('kontrakManajemen'));
    }

    public function edit($id)
    {
        $kontrakManajemen = KontrakManajemen::findOrFail($id);
        return view('kelola_data.kontrak_manajemen.edit', compact('kontrakManajemen'));
    }

    public function update(Request $request, $id)
    {
        $kontrakManajemen = KontrakManajemen::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'bobot' => 'nullable|integer',
            'responsibility' => 'nullable|string',
            'satuan' => 'nullable|string',
            'target_percent' => 'nullable|numeric',
            'status' => 'nullable|string',
            'unit_penanggung_jawab' => 'nullable|string',
            'periode' => 'nullable|string',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);

        $kontrakManajemen->update($validated);

        return redirect()
            ->route('manage.kontrak-manajemen.view', $id)
            ->with('success', 'Kontrak Manajemen berhasil diupdate');
    }

    public function destroy($id)
    {
        $kontrakManajemen = KontrakManajemen::findOrFail($id);
        $kontrakManajemen->delete();

        return redirect()
            ->route('manage.kontrak-manajemen.list')
            ->with('success', 'Kontrak Manajemen berhasil dihapus');
    }

    public function laporan(Request $request)
    {
        $query = KontrakManajemen::with(['kontrakUnit.kinerjaUnit.pelaporanPekerjaan']);

        // Filter opsional
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }

        $kontrakManajemenList = $query->get();

        return view('kelola_data.kontrak_manajemen.laporan', compact('kontrakManajemenList'));
    }
}
