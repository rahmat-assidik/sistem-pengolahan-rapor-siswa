<?php

namespace App\Observers;

use App\Models\Guru;
use App\Models\User;

/**
 * Observer untuk Guru Model
 * Memastikan konsistensi email antara guru dan user
 */
class GuruObserver
{
    /**
     * Ketika guru email diupdate, update juga user email yang terkait
     */
    public function updating(Guru $guru): void
    {
        if ($guru->isDirty('email')) {
            $oldEmail = $guru->getOriginal('email');
            $newEmail = $guru->email;
            
            // Update user email yang terkait
            if ($guru->user) {
                $guru->user->email = $newEmail;
                $guru->user->save();
            }
        }
    }
}
