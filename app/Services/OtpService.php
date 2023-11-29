<?php
namespace App\Services;

use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class OtpService
{
    public static function generateOtp()
    {
        $user = Auth::user();

        $otp = $user->generateNewOtp();

        Notification::sendNow(
                [$user], 
                new OtpNotification($otp)
            );
    }
}