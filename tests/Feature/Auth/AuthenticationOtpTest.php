<?php

namespace Tests\Feature\Auth;

use App\Models\Otp;
use App\Models\User;
use App\Notifications\OtpNotification;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationOtpTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $validOtp;
    protected $invalidOtp;
    protected $expiredOtp;

    public function setUp() : void
    {
        parent::setUp();

        Otp::unsetEventDispatcher();    //Disable OtpObserver

        $now = Carbon::setTestNow('2023-11-28 16:00:00');

        $this->user = User::factory()->create();
        $this->validOtp = Otp::factory()->create([
                'user_id' => $this->user->id,
                'otp' => '111111',
                'expire_at' => '2023-11-28 16:15:00',
                'valid' => true
            ]);
        $this->invalidOtp = Otp::factory()->create([
                'user_id' => $this->user->id,
                'otp' => '222222',
                'expire_at' => '2023-11-28 16:15:00',
                'valid' => false
            ]);
        $this->expiredOtp = Otp::factory()->create([
                'user_id' => $this->user->id,
                'otp' => '333333',
                'expire_at' => '2023-11-28 15:50:00',
                'valid' => true
            ]);
    }

    public function testLoginWithValidOtp()
    {
        $this->assertGuest();

        $response = $this->login(
                            $this->user->email, 
                            $this->validOtp->otp
                        );

        $this->assertAuthenticatedAs($this->user);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function testLoginWithInvalidOtp()
    {
        $this->assertGuest();

        $this->login(
                $this->user->email, 
                $this->invalidOtp->otp
            );
        
        $this->assertGuest();
    }

    public function testLoginWithExpiredOtp()
    {
        $this->assertGuest();

        $this->login(
                $this->user->email, 
                $this->expiredOtp->otp
            );
        
        $this->assertGuest();
    }

    public function testLoginWithWrongCombination()
    {
        $this->assertGuest();

        $this->login(
                'email@otp.com',
                '999999',
            );
        
        $this->assertGuest();
    }

    /*
     * =================================================================================================================
     * PRIVATE METHODS
     * =================================================================================================================
     */
    private function login(string $email, string $otp)
    {
        return $this->post('/otp', [
                        'email' => $email,
                        'otp' => $otp,
                    ]);
    }
}
