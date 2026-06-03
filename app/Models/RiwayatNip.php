<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RiwayatNip extends Model
{
    use HasFactory;

    protected $table = 'riwayat_nips';

    protected $fillable = [
        'nip',
        'status_pegawai_id',
        'users_id',
        'tmt_mulai',
        'is_active',
        // 'sk_ypt_id',
        'sk_ypt_or_amandemen',
    ];

    protected $casts = [
        'tmt_mulai' => 'date',
        'is_active' => 'boolean',
        'id' => 'string',
        'status_pegawai_id' => 'string',
        'users_id' => 'string',
        // 'sk_ypt_id' => 'string',
        'sk_ypt_or_amandemen' => 'string',

    ];



    public function pegawai()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function statusPegawai()
    {
        return $this->belongsTo(RefStatusPegawai::class, 'status_pegawai_id');
    }

    public function sk_or_amandemen()
    {
        return $this->belongsTo(SK::class, 'sk_ypt_or_amandemen', 'id');
    }

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
