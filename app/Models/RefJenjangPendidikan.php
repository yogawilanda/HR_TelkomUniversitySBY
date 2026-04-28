<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RefJenjangPendidikan extends Model
{
    /** @use HasFactory<\Database\Factories\RefJenjangPendidikanFactory> */
    use HasFactory;
    
    protected $table = 'ref_jenjang_pendidikans';
    protected $fillable = [
        'jenjang_pendidikan',
        'tingkat',
    ];
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        
    ];
    
    
    
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
