<?php

namespace App\Models\Dupak;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Pengajuan extends DupakModel
{
    protected $table = 'pengajuan';

    protected $casts = [
        'idDosen' => 'string',
    ];

    protected $fillable = [
        'idDosen',
        'start',
        'end',
        'TahunAjaranAjuanAwal',
        'TahunAjaranAjuanAkhir',
        'semesterAjuan',
        'jfaAsal',
        'jfaTujuan',
        'status'
    ];

    /**
     * Accessor untuk mendapatkan nama dosen secara manual dari DB sdm_tus.
     * Dapat diakses sebagai $pengajuan->nama_dosen
     * Asumsi: Koneksi sdm_tus diberi nama 'mysql'
     */
    public function getNamaDosenAttribute()
    {
        // Gunakan DB::connection() untuk query di database sdm_tus ('mysql')
        // Ini adalah cara PEREQUESTAN manual yang Anda minta.
        $dosenData = DB::connection('mysql')
            ->table('dosens') // Cari di tabel dosens
            ->select('users.nama') // Ambil kolom nama dari tabel users
            ->join('users', 'dosens.users_id', '=', 'users.id') // Join ke tabel users
            ->where('dosens.id', $this->idDosen) // Cocokkan id dosen
            ->first();

        // Kembalikan nama atau pesan default jika tidak ditemukan
        return $dosenData ? $dosenData->nama : 'Nama Dosen Tidak Ditemukan (ID: ' . $this->idDosen . ')';
    }


    public function dosen()
    {
        // model Dosen adalah milik database sdm_tus, bukan database dupak
        return $this->belongsTo(Dosen::class, 'idDosen');
    }

    public function userDosen()
    {
        return $this->hasOneThrough(
            User::class,  // Target akhir
            Dosen::class, // Model perantara
            'id',        // Foreign key di tabel dosens (relasi ke Pengajuan)
            'id',        // Foreign key di tabel users (primary)
            'idDosen',   // FK di Pengajuan
            'users_id'   // FK di tabel dosens
        );
    }

    public function rekapitulasiKomponen()
    {
        return $this->hasMany(InputRekapitulasiKomponen::class, 'idPengajuan');
    }

    public function jabatanAsal()
    {
        return $this->belongsTo(RefJabatanFungsionalAkademik::class, 'jfaAsal');
    }

    public function jabatanTujuan()
    {
        return $this->belongsTo(RefJabatanFungsionalAkademik::class, 'jfaTujuan');
    }

    public function details()
    {
        return $this->hasMany(DetailPengajuan::class, 'pengajuan_id');
    }
}
