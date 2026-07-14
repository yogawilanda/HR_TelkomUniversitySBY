<?php

namespace App\Http\Controllers\Dupak;

use App\Http\Controllers\Controller;
use App\Models\Dupak\Pengajuan;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $dosen = Dosen::where('users_id', Auth::id())->first();

        if (! $dosen) {
            return redirect()->back()->with('error', 'Data Dosen tidak ditemukan untuk pengguna ini.');
        }

        $query = Pengajuan::where('idDosen', $dosen->id);

        // Apply filters
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('period')) {
            // Period is stored in semesterAjuan
            $query->where('semesterAjuan', $request->period);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $riwayat = $query->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(10);

        return view('dupak.riwayat.index', compact('riwayat'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dosen = Dosen::where('users_id', Auth::id())->first();

        if (! $dosen) {
            return redirect()->back()->with('error', 'Data Dosen tidak ditemukan untuk pengguna ini.');
        }

        $pengajuan = Pengajuan::with(['details.kegiatan'])
            ->where('idDosen', $dosen->id)
            ->findOrFail($id);

        return view('dupak.riwayat.show', compact('pengajuan'));
    }
}