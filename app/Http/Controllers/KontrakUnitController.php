<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KontrakManajemen;
use App\Models\KontrakUnit;
use App\Models\KinerjaUnit;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class KontrakUnitController extends Controller
{
    public function index()
    {
        $items = KontrakUnit::with(['kontrakManajemen', 'kinerjaUnit'])->orderBy('id', 'desc')->get();
        return view('kelola_data.kontrak_unit.list', ['kontrakUnit' => $items]);
    }

    public function create()
    {
        $kontrakManajemenList = KontrakManajemen::where('is_active', true)->orderBy('nama')->get();
        return view('kelola_data.kontrak_unit.input', compact('kontrakManajemenList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kontrak_manajemen_id' => 'required|exists:kontrak_manajemen,id',
            'nama_unit' => 'required|string|max:255',
            'pekerjaan' => 'required|string',
            'kontrak_type' => 'nullable|string',
            'result' => 'nullable|string',
            'jumlah' => 'nullable|integer',
            'waktu_minutes' => 'nullable|integer',
            'bobot' => 'nullable|integer',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);

        $kontrakUnit = KontrakUnit::create($validated);

        // Automatically create KinerjaUnit for this KontrakUnit
        KinerjaUnit::create([
            'kontrak_unit_id' => $kontrakUnit->id,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('manage.kontrak-unit.view', $kontrakUnit->id)
            ->with('success', 'Kontrak Unit berhasil dibuat');
    }

    public function show($id)
    {
        $kontrakUnit = KontrakUnit::with([
            'kontrakManajemen',
            'kinerjaUnit.pelaporanPekerjaan',
            'pegawai'
        ])->findOrFail($id);
        
        return view('kelola_data.kontrak_unit.view', compact('kontrakUnit'));
    }

    public function edit($id)
    {
        $kontrakUnit = KontrakUnit::findOrFail($id);
        $kontrakManajemenList = KontrakManajemen::where('is_active', true)->orderBy('nama')->get();
        return view('kelola_data.kontrak_unit.edit', compact('kontrakUnit', 'kontrakManajemenList'));
    }

    public function update(Request $request, $id)
    {
        $kontrakUnit = KontrakUnit::findOrFail($id);

        $validated = $request->validate([
            'kontrak_manajemen_id' => 'required|exists:kontrak_manajemen,id',
            'nama_unit' => 'required|string|max:255',
            'pekerjaan' => 'required|string',
            'kontrak_type' => 'nullable|string',
            'result' => 'nullable|string',
            'jumlah' => 'nullable|integer',
            'waktu_minutes' => 'nullable|integer',
            'bobot' => 'nullable|integer',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);

        $kontrakUnit->update($validated);

        return redirect()
            ->route('manage.kontrak-unit.view', $id)
            ->with('success', 'Kontrak Unit berhasil diupdate');
    }

    public function destroy($id)
    {
        $kontrakUnit = KontrakUnit::findOrFail($id);
        $kontrakUnit->delete();

        return redirect()
            ->route('manage.kontrak-unit.list')
            ->with('success', 'Kontrak Unit berhasil dihapus');
    }

    public function assign($id)
    {
        $kontrakUnit = KontrakUnit::with('pegawai')->findOrFail($id);
        $allUsers = User::orderBy('nama_lengkap')->get();
        
        return view('kelola_data.kontrak_unit.assign', compact('kontrakUnit', 'allUsers'));
    }

    public function storeAssignment(Request $request, $id)
    {
        $kontrakUnit = KontrakUnit::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'status' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $kontrakUnit->pegawai()->attach($validated['user_id'], [
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'status' => $validated['status'] ?? 'assigned',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()
            ->route('manage.kontrak-unit.assign', $id)
            ->with('success', 'Pegawai berhasil ditugaskan');
    }

    public function updateAssignmentStatus(Request $request, $id, $userId)
    {
        $kontrakUnit = KontrakUnit::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $kontrakUnit->pegawai()->updateExistingPivot($userId, [
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()
            ->route('manage.kontrak-unit.assign', $id)
            ->with('success', 'Status penugasan berhasil diupdate');
    }

    public function detachPegawai($id, $userId)
    {
        $kontrakUnit = KontrakUnit::findOrFail($id);
        $kontrakUnit->pegawai()->detach($userId);

        return redirect()
            ->route('manage.kontrak-unit.assign', $id)
            ->with('success', 'Pegawai berhasil dilepaskan dari penugasan');
    }
}
