<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    protected $table = 'guru';

    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'nama_guru',
        'email',
        'jenis_kelamin',
        'no_hp',
        'status',
    ];

    /**
     * Relasi ke akun user.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'guru_id', 'nip');
    }

    /**
     * Relasi ke data pengampu.
     */
    public function pengampu(): HasMany
    {
        return $this->hasMany(Pengampu::class, 'guru_id', 'nip');
    }
}
