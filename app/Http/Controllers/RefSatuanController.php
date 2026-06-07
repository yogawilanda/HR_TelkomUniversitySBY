<?php

namespace App\Http\Controllers;

use App\Models\RefSatuan;
use Illuminate\Http\Request;

class RefSatuanController extends Controller
{
    public function index()
    {
        $defaults = [
            'Persentase (%)',
            'Dokumen',
            'Laporan',
            'Kegiatan',
            'SKS',
            'Mahasiswa',
            'Artikel',
            'Buku',
            'Paten',
            'Prototype',
            'Jam',
            'Mata Kuliah'
        ];

        $dbSatuans = RefSatuan::orderBy('nama')->get();
        $results = $dbSatuans->toArray();
        $dbNames = $dbSatuans->pluck('nama')->toArray();

        // Use a high range for hardcoded IDs to avoid collision
        $idCounter = 1000000;
        foreach ($defaults as $name) {
            if (!in_array($name, $dbNames)) {
                $results[] = [
                    'id' => $idCounter++,
                    'nama' => $name,
                    'is_hardcoded' => true
                ];
            }
        }

        // Add is_hardcoded => false to db results
        foreach ($results as &$item) {
            if (!isset($item['is_hardcoded'])) {
                $item['is_hardcoded'] = false;
            }
        }

        // Sort alphabetically by name
        usort($results, function ($a, $b) {
            return strcasecmp($a['nama'], $b['nama']);
        });

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:ref_satuan,nama'
        ], [
            'nama.unique' => 'Satuan ini sudah ada dalam daftar.'
        ]);
        
        $satuan = RefSatuan::create(['nama' => $request->nama]);
        return response()->json($satuan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => "required|string|max:255|unique:ref_satuan,nama,$id"
        ]);
        
        $satuan = RefSatuan::findOrFail($id);
        $satuan->update(['nama' => $request->nama]);
        return response()->json($satuan);
    }

    public function destroy($id)
    {
        try {
            $satuan = RefSatuan::findOrFail($id);
            
            // Alternate Flow: Check if being used in TargetKinerja
            $isUsed = \App\Models\TargetKinerja::where('satuan', $satuan->nama)->exists();
            if ($isUsed) {
                return response()->json(['message' => 'Satuan ini tidak bisa dihapus karena sedang digunakan oleh data KM/SM.'], 422);
            }

            $satuan->delete();
            return response()->json(['message' => 'Deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus satuan.'], 500);
        }
    }
}
