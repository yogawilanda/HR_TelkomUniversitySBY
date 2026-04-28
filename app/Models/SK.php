<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SK extends Model
{
    /** @use HasFactory<\Database\Factories\SKFactory> */
    use HasFactory;
    protected $table = 'sks';
    protected $fillable = [
        // 'users_id',
        'no_sk',
        'tmt_mulai',
        'file_sk',
        'tipe_sk',
        'keterangan'
    ];
    
    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'keterangan' => 'string',
    ];

    protected static function newFactory()
    {
        return \Database\Factories\SKFactory::new();
    }
    
    public static function Sk_Dikti()
    {
        return self::where('tipe_sk', 'LLDIKTI')->get();
    }
    

    // public static function user_data()
    // {
    //     return self::where('users_id', 'id')->get();
    // }

    public static function Sk_Ypt()
    {
        return self::where('tipe_sk', 'Pengakuan YPT')->get();
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
