<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'kelas_siswa_id',
        'pengampu_id',
        'tugas',
        'ulangan',
        'uts',
        'uas',
        'nilai_akhir',
    ];

    /**
     * Hitung predikat berdasarkan nilai rata-rata (skala 0-100)
     */
    public static function hitungPredikat($skor)
    {
        if ($skor === null) return null;
        
        if ($skor >= 90) return 'A';
        if ($skor >= 80) return 'B';
        if ($skor >= 70) return 'C';
        return 'D';
    }

    public function kelasSiswa(): BelongsTo
    {
        return $this->belongsTo(RiwayatKelasSiswa::class, 'kelas_siswa_id');
    }

    public function pengampu(): BelongsTo
    {
        return $this->belongsTo(Pengampu::class);
    }
}
