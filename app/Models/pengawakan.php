<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengawakan extends Model
{
    /** @use HasFactory<\Database\Factories\PengawakanFactory> */
    use HasFactory;

    protected $table = 'pengawakans';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = [
        'users_id',
        'formasi_id',
        'sk_ypt_id',
        'is_main_position',
        'tmt_mulai',
        'tmt_selesai',
    ];

    protected $casts = [
        'id' => 'string',
        'users_id' => 'string',
        'formasi_id' => 'string',
        'sk_ypt_id' => 'string',
        'tmt_mulai' => 'date',
        'tmt_selesai' => 'date',
    ];


    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function formasi()
    {
        return $this->belongsTo(Formation::class, 'formasi_id', 'id');
    }

    public function sk_ypt()
    {
        return $this->belongsTo(Sk::class, 'sk_ypt_id', 'id');
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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\PengawakanFactory::new();
    }
}
