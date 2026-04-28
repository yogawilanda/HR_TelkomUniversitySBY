<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class work_position extends Model
{
    /** @use HasFactory<\Database\Factories\WorkPositionFactory> */
    use HasFactory;
    public $timestamps = true;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'work_positions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'kode',
        'position_name',
        'type_work_position',
        'parent_id',
        'type_pekerja',
    ];
    public function refWorkPosition()
    {
        return $this->belongsTo(ref_work_position::class, 'type_work_position', 'position_name');
    }

    public function children()
    {
        return $this->hasMany(work_position::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(work_position::class, 'parent_id', 'id');
    }

    public function prodi_parent()
    {
        return $this->belongsToMany(
            work_position::class,  // target model self
            'prodis',              // table yang menyimpan relasi
            'prodi_id',            // kolom di prodis yang menunjuk ke anak (this->id)
            'fakultas_id'          // kolom di prodis yang menunjuk ke parent
        );
    }

    // Relasi ke children via table prodis
    public function fakultas_children()
    {
        return $this->belongsToMany(
            work_position::class,  // target model self
            'prodis',              // table yang menyimpan relasi
            'fakultas_id',         // kolom di prodis yang menunjuk ke parent (this->id)
            'prodi_id'             // kolom di prodis yang menunjuk ke anak
        );
    }

    public function dosen()
    {
        return $this->hasMany(Dosen::class, 'prodi_id', 'id');
    }

    public function tpa()
    {
        return $this->hasMany(Tpa::class, 'bagian_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
