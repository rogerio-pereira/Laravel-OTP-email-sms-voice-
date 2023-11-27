<?php

namespace App\Observers;

use App\Models\Otp;

class OtpObserver
{
    /**
     * Handle the Otp "created" event.
     */
    public function creating(Otp $otp): void
    {
        $userId = $otp->user_id;

        Otp::where('user_id', $userId)
            ->update([
                'valid' => false
            ]);
    }
}
