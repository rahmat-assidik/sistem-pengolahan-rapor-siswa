<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    protected $table = 'mapel';

    protected $primaryKey = 'kode_mapel';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kelompok',
        'status',
    ];

    /**
     * Relasi ke data pengampu.
     */
    public function pengampu(): HasMany
    {
        return $this->hasMany(Pengampu::class);
    }
}
