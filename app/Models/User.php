<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'guru_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke data guru (jika user adalah guru).
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'nip');
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah guru.
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Cek apakah user adalah Wali Kelas.
     */
    public function isWaliKelas(): bool
    {
        if (!$this->isGuru() || !$this->guru_id) {
            return false;
        }

        return \App\Models\WaliKelas::where('guru_id', $this->guru_id)->exists();
    }

    /**
     * Ambil data kelas yang diampu sebagai Wali Kelas (via bridge WaliKelas).
     */
    public function managedKelas()
    {
        return Kelas::whereIn('id', WaliKelas::where('guru_id', $this->guru_id)->pluck('kelas_id'));
    }
}
