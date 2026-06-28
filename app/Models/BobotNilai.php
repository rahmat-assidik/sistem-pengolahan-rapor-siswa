<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BobotNilai extends Model
{
    protected $table = 'bobot_nilai';

    protected $fillable = [
        'pengampu_id',
        'bobot_tugas',
        'bobot_ulangan',
        'bobot_uts',
        'bobot_uas',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    /**
     * Get the settings for a specific pengampu or global.
     */
    public static function forPengampu($pengampuId = null)
    {
        $query = self::query();
        if ($pengampuId) {
            $query->where('pengampu_id', $pengampuId);
        } else {
            $query->whereNull('pengampu_id');
        }
        
        $setting = $query->first();

        // If not found for specific, try global (where pengampu_id is null)
        if (!$setting && $pengampuId) {
            $setting = self::whereNull('pengampu_id')->first();
        }

        return $setting;
    }
    
    /**
     * Keep current() for legacy/global access.
     */
    public static function current()
    {
        return self::whereNull('pengampu_id')->first();
    }
}
