<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class VoiceChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $notification->toVoice($notifiable);
    }
}