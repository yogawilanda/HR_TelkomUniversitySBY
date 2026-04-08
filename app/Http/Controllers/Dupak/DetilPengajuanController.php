<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dupak\Pengajuan;
use Illuminate\Http\Request;

class DetilPengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // The view folder uses underscores (pengisian_detil_pengajuan). Return the correct view.
        return view('dupak.pengisian_detil_pengajuan.create');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $getPengajuan = Pengajuan::find($id);
        if (!$getPengajuan) {
            return redirect()->back()->with('error', 'Pengajuan not found.');
        }
        $id = $getPengajuan->id;
        return view('dupak.pengisian_detil_pengajuan.show', ['id' => $id]);
    }

    // show form pengajuan pendidikan, penelitian, pengabdian, penunjang
    public function showFormPendidikan()
    { 
        // jika pengajuan pendidikan, maka tampilkan form pendidikan
        return view('dupak.pengisian_detil_pengajuan.form_pendidikan');
    }

    public function showFormPenelitian()
    { 
        // jika pengajuan penelitian, maka tampilkan form penelitian
        return view('dupak.pengisian_detil_pengajuan.form_penelitian');
    }

    public function showFormPengabdian()
    { 
        // jika pengajuan pengabdian, maka tampilkan form pengabdian
        return view('dupak.pengisian_detil_pengajuan.form_pengabdian');
    }

    public function showFormPenunjang()
    { 
        // jika pengajuan penunjang, maka tampilkan form penunjang
        return view('dupak.pengisian_detil_pengajuan.form_penunjang');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}
}
