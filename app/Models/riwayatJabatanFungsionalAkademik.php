<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class riwayatJabatanFungsionalAkademik extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatJabatanFungsionalAkademikFactory> */
    use HasFactory;
    protected $connection = 'mysql';

    protected $table = 'riwayat_jabatan_fungsional_akademiks';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ref_jfa_id',
        'dosen_id',
        'tmt_mulai',
        'tmt_selesai',
        'sk_llkdikti_id',
        'sk_pengakuan_ypt_id',

    ];

    protected $casts = [
        'ref_jfk_id' => 'boolean',
        'dosen_id' => 'string',
        'id'=>'string',
    ];

    public function jfa()
    {
        return $this->belongsTo(RefJabatanFungsionalAkademik::class,'ref_jfa_id','id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class,'dosen_id','id');
    }

    public function sk_dikti()
    {
        return $this->belongsTo(SK::class,'sk_llkdikti_id','id');
    }
    public function sk_ypt()
    {
        return $this->belongsTo(SK::class,'sk_pengakuan_ypt_id','id');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\RiwayatJabatanFungsionalAkademikFactory::new();
    }
}
