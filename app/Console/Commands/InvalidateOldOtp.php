<?php

namespace App\Console\Commands;

use App\Models\Otp;
use Illuminate\Console\Command;

class InvalidateOldOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:sanitize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invalidate expired OTPs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Otp::expired()
            ->update([
                'valid' => false,
            ]);
    }
}
