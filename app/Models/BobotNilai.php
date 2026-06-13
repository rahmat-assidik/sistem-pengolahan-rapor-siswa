<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BobotNilai extends Model
{
    protected $table = 'bobot_nilai';

    protected $fillable = [
        'bobot_tugas',
        'bobot_ulangan',
        'bobot_uts',
        'bobot_uas',
    ];

    /**
     * Get the first row.
     */
    public static function current()
    {
        return self::first();
    }
}
