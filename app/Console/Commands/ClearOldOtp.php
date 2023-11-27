<?php

namespace App\Console\Commands;

use App\Models\Otp;
use Illuminate\Console\Command;

class ClearOldOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired and inactive OTPs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Otp::invalid()
            ->delete();
    }
}
