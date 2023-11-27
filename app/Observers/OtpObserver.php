<?php

namespace App\Observers;

use App\Models\Otp;
use Carbon\Carbon;

class OtpObserver
{
    /**
     * Handle the Otp "created" event.
     */
    public function creating(Otp $otp): void
    {
        $userId = $otp->user_id;
        $otp->expire_at = Carbon::now()->addMinutes(15);

        Otp::where('user_id', $userId)
            ->update([
                'valid' => false
            ]);
    }
}
