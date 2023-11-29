<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\OtpNotification;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->login($this->user->email, 'password');

        $url = route('otp');
        $response->assertRedirect($url);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $this->login($this->user->email, 'wrong_password');

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $response = $this->actingAs($this->user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function testSucessfullLoginShouldGenerateNewOTP()
    {
        Carbon::setTestNow('2023-11-29 14:00:00');

        $this->assertDatabaseMissing('otps', [
                'id' => 1,
                'user_id' => $this->user->id,
                'expire_at' => '2023-11-29 14:15:00',
            ]);

        $this->login($this->user->email, 'password');

        $this->assertDatabaseHas('otps', [
                'id' => 1,
                'user_id' => $this->user->id,
                'expire_at' => '2023-11-29 14:15:00',
            ]);
    }

    public function testOtpShouldntBeCreatedToASecondUser()
    {
        $user2 = User::factory()->create();

        //User 2 doesn't have any otp
        $this->assertDatabaseMissing('otps', [
                'user_id' => $user2->id,
            ]);

        //Login with user 1
        $this->login($this->user->email, 'password');

        //User 2 still doesn't have any otp
        $this->assertDatabaseMissing('otps', [
                'user_id' => $user2->id,
            ]);
    }

    public function testSucessfullLoginShouldSentNotification()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $this->login($this->user->email, 'password');

        Notification::assertSentTo(
                [$this->user], OtpNotification::class
            );
    }

    public function testOtpNotificationShouldntBeSentToASecondUser()
    {
        Notification::fake();

        $user2 = User::factory()->create();

        //User 2 wasn't notified
        Notification::assertNotSentTo(
                [$user2], OtpNotification::class
            );

        $this->login($this->user->email, 'password');

        //User 2 still wasn't notified
        Notification::assertNotSentTo(
                [$user2], OtpNotification::class
            );
    }

    /*
     * =================================================================================================================
     * PRIVATE METHODS
     * =================================================================================================================
     */
    private function login(string $email, string $password)
    {
        return $this->post('/login', [
                        'email' => $email,
                        'password' => $password,
                    ]);
    }
}
