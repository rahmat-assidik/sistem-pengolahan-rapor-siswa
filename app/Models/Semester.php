<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $table = 'semester';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    /**
     * Relasi ke tahun ajaran.
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Relasi ke penempatan kelas siswa.
     */
    public function kelasSiswa(): HasMany
    {
        return $this->hasMany(RiwayatKelasSiswa::class);
    }

    /**
     * Relasi ke pengampu.
     */
    public function pengampu(): HasMany
    {
        return $this->hasMany(Pengampu::class);
    }

    /**
     * Scope: hanya semester aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Accessor: label lengkap, misal "Ganjil 2025/2026".
     */
    public function getLabelAttribute(): string
    {
        return $this->semester . ' ' . ($this->tahunAjaran->nama ?? '');
    }
}
