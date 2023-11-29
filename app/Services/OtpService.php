<?php
namespace App\Services;

use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class OtpService
{
    public static function generateOtp(User $user)
    {
        $otp = $user->generateNewOtp();

        Notification::sendNow(
            [$user], 
            new OtpNotification($otp)
        );
    }
}