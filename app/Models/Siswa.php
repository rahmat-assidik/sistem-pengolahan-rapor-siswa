<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'angkatan',
        'nama_siswa',
        'jenis_kelamin',
        'status',
    ];

    protected function casts(): array
    {
        return [
        ];
    }

    /**
     * Relasi ke penempatan kelas (pivot riwayat_kelas_siswa).
     */
    public function kelasSiswa(): HasMany
    {
        return $this->hasMany(RiwayatKelasSiswa::class, 'nis', 'nis');
    }

    /**
     * Relasi many-to-many ke kelas melalui pivot.
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'riwayat_kelas_siswa', 'nis', 'kode_kelas', 'nis', 'kode_kelas')
                    ->withPivot('semester_id')
                    ->withTimestamps();
    }

    /**
     * Relasi ke nilai.
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }


}
