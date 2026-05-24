<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    protected $table = 'nilai';

    protected $fillable = [
        'kelas_siswa_id',
        'pengampu_id',
        'jenis_nilai',
        'skor',
        'komponen_nilai_id',
    ];

    protected function casts(): array
    {
        return [
            'skor' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke pengampu (guru + mapel + kelas + semester).
     */
    public function pengampu(): BelongsTo
    {
        return $this->belongsTo(Pengampu::class);
    }

    /**
     * Relasi ke riwayat kelas siswa.
     */
    public function kelasSiswa(): BelongsTo
    {
        return $this->belongsTo(RiwayatKelasSiswa::class, 'kelas_siswa_id');
    }

    public function komponenNilai(): BelongsTo
    {
        return $this->belongsTo(KomponenNilai::class, 'komponen_nilai_id');
    }

    /**
     * Helper: konversi nilai ke predikat.
     */
    public static function hitungPredikat(?float $nilai): string
    {
        if ($nilai === null) return '-';
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        return 'D';
    }
}
