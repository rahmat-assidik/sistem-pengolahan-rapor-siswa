<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengampu extends Model
{
    protected $table = 'pengampu';

    protected $fillable = [
        'guru_id',
        'mapel_id',
        'kelas_id',
        'semester_id',
        'kkm',
        'status',
    ];

    /**
     * Relasi ke guru.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'nip');
    }

    /**
     * Relasi ke mata pelajaran.
     */
    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id', 'kode_mapel');
    }

    /**
     * Relasi ke kelas.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi ke semester.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relasi ke nilai siswa.
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Relasi ke presensi.
     */
    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }
}
