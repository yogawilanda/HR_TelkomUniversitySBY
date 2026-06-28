<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Work_Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //DT
        $prodis = Prodi::with('fakultas', 'data_prodi' , 'formasi.people')->get()->sortBy(fn($item) => $item->data_prodi->position_name);
        // dd($prodis);

        $fakultas = Work_Position::where('type_work_position', 'Fakultas')->orderBy('position_name')->get();

        // dd($prodis);
        $route = view('kelola_data.prodi.index', compact('prodis', 'fakultas'));
        return $this->CekReview($route, '1I1', 'MELIHAT DATA PRODI', true);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //DT

        $fakultas = Work_Position::where('type_work_position', 'Fakultas')->get();
        $route = view('kelola_data.prodi.create', compact('fakultas'));
        return $this->CekReview($route, '1I1', 'MELIHAT DATA PRODI');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //DT
        try {
            $validated = $request->validate([
                'fakultas_id' => 'required|max:100',
                'kode' => 'required|string|max:100|unique:work_positions,kode',
                'position_name' => 'required|string|max:100',
            ]);


            // dd($request);
            $cek_fakultas = Fakultas::where('id', $request->fakultas_id)->where('type_work_position', 'Fakultas')->first();
            // DD('CEK', $cek_fakultas);
            if (!$cek_fakultas) {
                throw new \Exception('Gagal Menambah Prodi, Fakultas tidak terdaftar atau data salah!.');
            }

            $validated['type_pekerja'] = 'Dosen';
            $work_position = Work_Position::create([
                'kode' => $validated['kode'],
                'position_name' => $validated['position_name'],
                'type_work_position' => 'Program Studi',
                'parent_id' => $validated['fakultas_id'],
                'type_pekerja' => $validated['type_pekerja']
            ]);

            $validated['prodi_id'] = $work_position->id;
            $prodi = Prodi::create($validated);

            if ($work_position && $prodi) {
                DB::commit();
                $route = redirect()->route('manage.prodi.index')
                    ->with('success', 'Program Studi berhasil ditambahkan.');
                return $this->CekReview($route, '1I2', 'MENAMBAH DATA PRODI');

            } else {
                throw new \Exception('Terjadi masalah saat menyimpan, mohon ulangi beberapa saat lagi!.');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput($request->all())
                ->with('error_alert', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $fakultas = Work_Position::where('type_work_position', 'Fakultas')->get();
        $prodi = Work_Position::where('id', $id)->where('type_work_position', 'Program Studi')->with(['parent', 'dosen'])->firstOrFail();
        return view('kelola_data.prodi.show', compact('prodi', 'fakultas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // dd('masuk');
        $prodi_data = Prodi::where('id', $id)->first();
        $prodi = Work_Position::with('parent')->where('id', $prodi_data->prodi_id)->first();
        $prodi['fakultas_id']= $prodi_data->fakultas_id;
        $fakultas = Work_Position::where('type_work_position', 'Fakultas')->get();
        return view('kelola_data.prodi.edit', compact('prodi', 'fakultas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($id);
        $prodi = Work_Position::where('id', $id)->first();
        if(!$prodi){
            return $this->handleRedirectBack()->with('error_alert', 'Prodi tidak ditemukan!');
        }
        $prodi2 = Prodi::where('prodi_id', $id)->first();

        $validated = $request->validate([
            'fakultas_id' => 'required|exists:work_positions,id',
            'kode' => 'required|string|max:100|unique:work_positions,kode,' . $prodi->id,
            'position_name' => 'required|string|max:100',
        ],[
            'exists' => ':attribute Tidak Ditemukan'
        ],[
            'fakultas_id' => 'Fakultas',
            'kode' => 'Singkatan Program Studi',
            'position_name' => 'Nama Program Studi'
        ]);

        $prodi->update([
            'kode' => $validated['kode'],
            'position_name' => $validated['position_name'],
            'parent_id' => $validated['fakultas_id'],
        ]);

        $prodi2->update([
            'fakultas_id' => $validated['fakultas_id'],
        ]);

        $route = redirect()->route('manage.prodi.index')
            ->with('success', 'Program Studi berhasil diperbarui.');

        return $this->CekReview($route, '1I3', 'MENGUBAH DATA PRODI');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $prodi = Work_Position::where('id', $id)->where('type_work_position', 'Program Studi')->firstOrFail();
        $prodi->delete();

        return redirect()->route('manage.prodi.index')
            ->with('success', 'Program Studi berhasil dihapus.');
    }

    /**
     * Get cached statistics for a prodi
     */
    public function getCachedStats($id)
    {
        $prodi = Work_Position::where('id', $id)->where('type_work_position', 'Program Studi')->firstOrFail();
        $statsKey = 'prodi_stats_' . $prodi->id;
        $cachedStats = cache()->get($statsKey);

        if ($cachedStats) {
            return response()->json($cachedStats);
        }

        // Return default values if no cache exists
        return response()->json([
            's2' => 0,
            's3' => 0,
            'njad' => 0,
            'aa' => 0,
            'l' => 0,
            'lk' => 0,
            'gb' => 0,
            'tetap' => 0,
            'calon_tetap' => 0,
            'profesional' => 0,
            'perbantuan' => 0,
        ]);
    }

    /**
     * Update statistics for a prodi
     */
    public function updateStats(Request $request, $id)
    {
        $prodi = Work_Position::where('id', $id)->where('type_work_position', 'Program Studi')->firstOrFail();

        try {
            $validated = $request->validate([
                's2' => 'required|integer|min:0',
                's3' => 'required|integer|min:0',
                'njad' => 'required|integer|min:0',
                'aa' => 'required|integer|min:0',
                'l' => 'required|integer|min:0',
                'lk' => 'required|integer|min:0',
                'gb' => 'required|integer|min:0',
                'tetap' => 'required|integer|min:0',
                'calon_tetap' => 'required|integer|min:0',
                'profesional' => 'required|integer|min:0',
                'perbantuan' => 'required|integer|min:0',
            ]);

            // Calculate total dosen
            $totalDosen = $validated['tetap'] + $validated['calon_tetap'] +
                $validated['profesional'] + $validated['perbantuan'];

            // Validate: Total pendidikan should not exceed total dosen
            $totalPendidikan = $validated['s2'] + $validated['s3'];
            if ($totalPendidikan > $totalDosen) {
                return response()->json([
                    'success' => false,
                    'message' => "Total S2 + S3 ($totalPendidikan) melebihi Total Dosen ($totalDosen)"
                ], 422);
            }

            // Validate: Total JFA should not exceed total dosen
            $totalJFA = $validated['njad'] + $validated['aa'] + $validated['l'] +
                $validated['lk'] + $validated['gb'];
            if ($totalJFA > $totalDosen) {
                return response()->json([
                    'success' => false,
                    'message' => "Total JFA (NJAD+AA+L+LK+GB = $totalJFA) melebihi Total Dosen ($totalDosen)"
                ], 422);
            }

            // Calculate percentages and ensure they don't exceed 100%
            $persenS3 = $totalDosen > 0 ? min(($validated['s3'] / $totalDosen), 1.0) : 0;
            $llkgb = $totalDosen > 0 ? min((($validated['l'] + $validated['lk'] + $validated['gb']) / $totalDosen), 1.0) : 0;
            $jfa = $totalDosen > 0 ? min((($validated['aa'] + $validated['l'] + $validated['lk'] + $validated['gb']) / $totalDosen), 1.0) : 0;

            // Store statistics in cache with prodi id
            $statsKey = 'prodi_stats_' . $prodi->id;
            cache()->forever($statsKey, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Statistik berhasil diperbarui',
                'data' => array_merge($validated, [
                    'total_dosen' => $totalDosen,
                    'persen_s3' => round($persenS3 * 100, 2),
                    'llkgb' => round($llkgb * 100, 2),
                    'jfa' => round($jfa * 100, 2),
                ])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
