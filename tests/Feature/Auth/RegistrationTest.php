<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\OtpNotification;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '(123) 123-1234',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        
        $this->assertGuest();

        $url = route('otp');
        $response->assertRedirect($url);
    }

    public function testRegisteredUserHasOtp()
    {
        Carbon::setTestNow('2023-11-30 12:00:00');
        $this->assertDatabaseMissing('otps', [
                'id' => 1,
                'expire_at' => '2023-11-30 12:15:00',
                'valid' => true,
            ]);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '(123) 123-1234',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $userId = User::where('email', 'test@example.com')
                    ->first()
                    ->id;
        $this->assertDatabaseHas('otps', [
                'id' => 1,
                'user_id' => $userId,
                'expire_at' => '2023-11-30 12:15:00',
                'valid' => true,
            ]);
    }

    public function testRegisteredUserSendOtpNotification()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '(123) 123-1234',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')
                    ->first();
        Notification::assertSentTo(
                [$user], OtpNotification::class
            );
    }
}
