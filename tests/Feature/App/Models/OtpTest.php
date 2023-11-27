<?php

namespace Tests\Feature\App\Models;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OtpTest extends TestCase
{
    private $user;
    public function setUp() : void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        Otp::unsetEventDispatcher();    //Disable OtpObserver
    }

    /**
     * A basic feature test example.
     */
    public function testInvalidScope(): void
    {
        $now = '2023-11-26 17:00:00';
        Carbon::setTestNow($now);

        //Invalid because valid is false
        $data = [
            'user_id' => $this->user->id,
            'otp' => '171000',
            'expire_at' => '2023-11-26 17:10:00',
            'valid' => false,
        ];

        //Valid, should be ignored
        $data2 = [
            'user_id' => $this->user->id,
            'otp' => '171000',
            'expire_at' => '2023-11-26 17:10:00',
            'valid' => true,
        ];

        //Invalid because expire_at is in past
        $data3 = [
            'user_id' => $this->user->id,
            'otp' => '160000',
            'expire_at' => '2023-11-26 16:00:00',
            'valid' => true,
        ];

        Otp::factory()->create($data);
        Otp::factory()->create($data2);
        Otp::factory()->create($data3);

        $this->assertDatabaseHas('otps', $data);
        $this->assertDatabaseHas('otps', $data2);
        $this->assertDatabaseHas('otps', $data3);

        $this->travel(5)->minutes();

        $otps = Otp::invalid()
                    ->get()
                    ->toArray();

        $this->assertEquals(2, count($otps));
        $this->assertEquals(1, $otps[0]['id']);
        $this->assertEquals(3, $otps[1]['id']);
    }
}
