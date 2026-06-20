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
        'signature_path',
    ];

//   Relation account to user
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'guru_id', 'nip');
    }

//   Relation Teacher to User
    public function pengampu(): HasMany
    {
        return $this->hasMany(Pengampu::class, 'guru_id', 'nip');
    }
}
