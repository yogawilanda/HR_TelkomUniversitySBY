<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\work_position;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fakultas = work_position::where('type_work_position', 'Fakultas')->withCount('children as prodi_count')->paginate(15);
        return view('kelola_data.fakultas.index', compact('fakultas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelola_data.fakultas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'kode' => [
                    'required',
                    'string',
                    'max:5',
                    'unique:work_positions,kode'
                ],
                'position_name' => [
                    'required',
                    'string',
                    'max:100'
                ],
            ],
            [
                'required' => ':attribute wajib diisi.',
                'string'   => ':attribute harus berupa text.',
                'max'      => ':attribute maksimal :max karakter.',
                'unique'   => ':attribute sudah digunakan.',

                'kode.required' => 'Kode Posisi wajib diisi.',
                'kode.unique'   => 'Kode Posisi sudah terdaftar.',
            ],
            [
                'kode' => 'Kode Posisi',
                'position_name' => 'Nama Posisi',
            ]
        );

        $validated['singkatan'] = $validated['kode'];

        $validated['type_work_position'] = 'Fakultas';

        work_position::create($validated);

        return redirect()->route('manage.fakultas.index')
            ->with('success', 'Fakultas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $fakultas = work_position::where('id', $id)->where('type_work_position', 'Fakultas')->with('children')->firstOrFail();
        return view('kelola_data.fakultas.show', compact('fakultas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $fakulta = work_position::where('id', $id)->where('type_work_position', 'Fakultas')->firstOrFail();
        return view('kelola_data.fakultas.edit', compact('fakulta'));
    }

    /**
     * Update the specified resource in storage.
     * 
     */
    public function update(Request $request, $id)
    {
        $fakulta = work_position::where('id', $id)->where('type_work_position', 'Fakultas')->firstOrFail();

        $validated = $request->validate([
            'kode' => 'required|string|max:100|unique:work_positions,kode,' . $fakulta->id,
            'position_name' => 'required|string|max:100',
            // 'singkatan' => 'nullable|string|max:20',
        ]);

        $validated['singkatan'] = $validated['kode'];

        $fakulta->update($validated);

        return redirect()->route('manage.fakultas.index')
            ->with('success', 'Fakultas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fakulta = work_position::where('id', $id)->where('type_work_position', 'Fakultas')->firstOrFail();
        $fakulta->delete();

        return redirect()->route('manage.fakultas.index')
            ->with('success', 'Fakultas berhasil dihapus.');
    }
}
