<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodis';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'fakultas_id',
        'prodi_id'
        // 'nama_prodi',
        // 'kode'
    ];

    protected $casts = [
        'id' => 'string',
        'fakultas_id' => 'string',
        'prodi_id' => 'string',

    ];

    // Relationships
    public function fakultas()
    {
        return $this->belongsTo(Work_Position::class,'fakultas_id', 'id');
    }

    public function data_prodi()
    {
        return $this->belongsTo(Work_Position::class,'prodi_id', 'id');
    }

    public function formasi()
    {
        return $this->belongsTo(Formation::class,'prodi_id', 'work_position_id');
    }

    public function dosen()
    {
        return $this->hasMany(Pengawakan::class);
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
