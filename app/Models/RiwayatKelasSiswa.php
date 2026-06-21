<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKelasSiswa extends Model
{
    protected $table = 'riwayat_kelas_siswa';

    protected $fillable = [
        'nis',
        'kode_kelas',
        'semester_id',
        'catatan_wali',

        'status_rapor',
    ];

    protected $appends = ['kelas_id'];

    /**
     * Accessor for kelas_id, for Alpine.js compatibility.
     */
    public function getKelasIdAttribute()
    {
        return $this->kelas?->id;
    }

    /**
     * Relasi ke siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }

    /**
     * Relasi ke kelas.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kode_kelas', 'kode_kelas');
    }

    /**
     * Relasi ke semester.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relasi ke daftar nilai.
     */
    public function nilai(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Nilai::class, 'kelas_siswa_id');
    }

    /**
     * Relasi ke daftar presensi.
     */
    public function presensi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Presensi::class, 'kelas_siswa_id');
    }
}
