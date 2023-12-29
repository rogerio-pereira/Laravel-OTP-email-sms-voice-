<?php

namespace App\Notifications;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\VoiceChannel;
use Aws\Connect\ConnectClient;
use Aws\Result;
use Aws\Sns\SnsClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct(Otp $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail', 
            SmsChannel::class,
            VoiceChannel::class,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        $mail = new OtpMail($this->otp);
        $address = $this->otp
                        ->user
                        ->email;
        
        return $mail->to($address);
    }

    /**
     * Get the sms representation of the notification.
     */
    public function toSms(object $notifiable): Result
    {
        $sns = new SnsClient([
                'version' => 'latest',
                'region' => config('services.sns.region'),
                'credentials' => [
                    'key' => config('services.sns.key'),
                    'secret' => config('services.sns.secret'),
                ],
            ]);

        $otp = $this->otp->otp;
        $app = config('app.name');
        $message = "Hello, new OTP (OneTimePassword) {$otp} for application {$app}";

        $phoneNumber = $this->otp->user->sanitizedPhoneNumber();

        return $sns->publish([
                        'Message' => $message,
                        'PhoneNumber' => $phoneNumber,
                    ]);
    }

    /**
     * Get the voice representation of the notification.
     */
    public function toVoice(object $notifiable): Result
    {
        $otp = $this->otp->otp;
        $otpArray = str_split($otp);
        $otpNumbers = implode(' ', $otpArray);
        $app = config('app.name');
        $message = "<speak>
                        Hello, new One Time Password for application {$app}. 
                        Your new password is: <break />
                        <prosody rate='50%'>
                            {$otpNumbers}
                        </prosody><break />
                    </speak>";

        $phoneNumber = $this->otp->user->sanitizedPhoneNumber();

        $connect = new ConnectClient([
            'version'  => 'latest',
            'region' => config('services.connect.region'),
            'credentials' => [
                'key' => config('services.connect.key'),
                'secret' => config('services.connect.secret'),
            ],
        ]);
        
        return $connect->startOutboundVoiceContact([
                        'DestinationPhoneNumber' => $phoneNumber,
                        'ContactFlowId'          => config('services.connect.contactFlowId'),
                        'InstanceId'             => config('services.connect.instanceId'),
                        'QueueId'                => config('services.connect.queueId'),
                        'Attributes' => [
                            'message' => $message,
                        ]
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
