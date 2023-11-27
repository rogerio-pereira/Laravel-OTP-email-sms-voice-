<?php

namespace Tests\Feature\App\Observers;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OtpObserverTest extends TestCase
{
    private $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    
    /**
     * A basic feature test example.
     */
    public function testObserverShouldCreateFirstOtpWithoutInvalidating(): void
    {
        Otp::factory()
            ->create([
                'user_id' => $this->user->id
            ]);

        $this->assertDatabaseHas('otps', [
            'id' => 1,
            'user_id' => $this->user->id,
            'valid' => true,
        ]);
    }
    
    /**
     * A basic feature test example.
     */
    public function testObserverShouldCreateOtpForSecondUserWithoutInvalidatingBoth(): void
    {
        $user2 = User::factory()->create();
        Otp::factory()->create([
                        'user_id' => $this->user->id
                    ])
                    ->toArray();
        Otp::factory()->create([
                        'user_id' => $user2->id
                    ])
                    ->toArray();

        $this->assertDatabaseHas('otps', [
            'id' => 1,
            'user_id' => $this->user->id,
            'valid' => true,
        ]);

        $this->assertDatabaseHas('otps', [
            'id' => 2,
            'user_id' => $user2->id,
            'valid' => true,
        ]);
    }
    
    /**
     * A basic feature test example.
     */
    public function testObserverShouldCreateSecondOtpInvalidatingFirstRecord(): void
    {
        Otp::factory()->create([
                        'user_id' => $this->user->id
                    ])
                    ->toArray();
        $this->assertDatabaseHas('otps', [
            'id' => 1,
            'user_id' => $this->user->id,
            'valid' => true,
        ]);


        Otp::factory()->create([
            'user_id' => $this->user->id
        ])
        ->toArray();
        $this->assertDatabaseHas('otps', [
            'id' => 1,
            'user_id' => $this->user->id,
            'valid' => false,
        ]);
        $this->assertDatabaseHas('otps', [
            'id' => 2,
            'user_id' => $this->user->id,
            'valid' => true,
        ]);
    }

    public function testObserverShouldAddExpiredAtAfter15Minutes() : void
    {
        Carbon::setTestNow('2023-11-26 13:00:30');

        $otp = Otp::factory()->create([
                    'user_id' => $this->user->id
                ])
                ->toArray();

        $this->assertDatabaseHas('otps', [
            'id' => 1,
            'user_id' => $this->user->id,
            'expire_at' => '2023-11-26 13:15:30'
        ]);
    }
}
