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
        $otp->expire_at = Carbon::now()->addMinutes(15);
        $otp->otp = rand(100000, 999999);

        $userId = $otp->user_id;

        Otp::where('user_id', $userId)
            ->update([
                'valid' => false
            ]);
    }
}
