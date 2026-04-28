<?php

namespace App\Models;

use App\Http\Controllers\RiwayatJabatanFungsionalAkademikController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    // biar ndak error saat query ke dupak juga karena eloquent mikirnya itu ini punyanya dbnya dupak
    protected $connection = 'mysql';
    /**
     * Non-incrementing ID (UUID)
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Fillable attributes
     */
    protected $fillable = [
        'nama_lengkap',
        'telepon',
        // 'emergency_contact_phone',
        'alamat',
        'nik',
        'email_institusi',
        'jenis_kelamin',
        'tipe_pegawai',
        'tempat_lahir',
        'tgl_lahir',
        'tgl_bergabung',
        'email_pribadi',
        'email_verified_at',
        'username',
        'password',
        'is_admin',
        'is_new',
        'remember_token',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attribute casting definitions for the model.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'id' => 'string',
    ];

    /**
     * Relationships
     */
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'users_id');
    }

    public function riwayatNip()
    {
        return $this->hasMany(RiwayatNip::class, 'users_id', 'id');
    }

    public function active_nip()
    {
        return $this->hasMany(RiwayatNip::class, 'users_id', 'id')
            ->whereNull('tmt_selesai');
    }

    public function last_studi()
    {
        // Relasi ke riwayat jenjang pendidikan terakhir berdasarkan urutan tertinggi
        return $this->hasOne(RiwayatJenjangPendidikan::class, 'users_id')
            ->join('ref_jenjang_pendidikans as ref', 'ref.id', '=', 'riwayat_jenjang_pendidikans.jenjang_pendidikan_id')
            ->orderByDesc('ref.urutan')
            ->select('riwayat_jenjang_pendidikans.*', 'ref.jenjang_pendidikan', 'ref.urutan');
    }

    public function riwayatNipFirst()
    {
        return $this->hasOne(RiwayatNip::class, 'users_id');
    }

    public function riwayatJenjangPendidikan()
    {
        return $this->hasMany(RiwayatJenjangPendidikan::class, 'users_id');
    }

    public function jabatan()
    {
        return $this->hasMany(Pengawakan::class, 'users_id', 'id');
    }

    public function tpa()
    {
        return $this->hasOne(Tpa::class, 'users_id');
    }

    public function bagian()
    {
        return $this->hasOne(RefBagian::class, 'id', 'bagian_id');
    }

    /**
     * Auto-generate UUID when creating new User
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
