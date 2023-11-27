<?php

namespace Tests\Feature\App\Models;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Otp;
use App\Models\User;
use Tests\TestCase;

class UserOtpTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testUserOtpRelationship(): void
    {
        $user = User::factory()
                    ->create()
                    ->toArray();
        $otp = Otp::factory()
                    ->create([
                            'user_id' => $user['id']
                    ])
                    ->toArray();

        $dbUser = User::find($user['id'])
                    ->with('otp')
                    ->first();
                    
        $dbOtp = $dbUser->otp->toArray();
        $dbUser = $dbUser->toArray();
        unset($dbUser['otp']);
        
        $this->assertEquals($user, $dbUser);
        $this->assertEquals($otp, $dbOtp);
    }

    /**
     * A basic test example.
     */
    public function testUserOtpLatest(): void
    {
        $user = User::factory()
                    ->create()
                    ->toArray();
        $otp1 = Otp::factory()
                    ->create([
                            'user_id' => $user['id'],
                            'created_at' => '2023-11-27 12:05:00',
                    ])
                    ->toArray();
        $otp2 = Otp::factory()
                    ->create([
                            'user_id' => $user['id'],
                            'created_at' => '2023-11-27 12:00:00',
                            'valid' => false,
                    ])
                    ->toArray();

        $dbUser = User::find($user['id'])
                    ->with('otp')
                    ->first();
        $dbOtp = $dbUser->otp->toArray();
        $dbUser = $dbUser->toArray();
        unset($dbUser['otp']);
        
        $this->assertEquals($user, $dbUser);
        $this->assertEquals($otp1, $dbOtp);
    }

    /**
     * A basic test example.
     */
    public function testUserOtpValid(): void
    {
        $user = User::factory()
                    ->create()
                    ->toArray();
        $otp1 = Otp::factory()
                    ->create([
                            'user_id' => $user['id'],
                            'created_at' => '2023-11-27 12:05:00',
                            'valid' => false,
                    ])
                    ->toArray();
        $otp2 = Otp::factory()
                    ->create([
                            'user_id' => $user['id'],
                            'created_at' => '2023-11-27 12:00:00',
                            'valid' => true,
                    ])
                    ->toArray();

        $dbUser = User::find($user['id'])
                    ->with('otp')
                    ->first();
        $dbOtp = $dbUser->otp->toArray();
        $dbUser = $dbUser->toArray();
        unset($dbUser['otp']);
        
        $this->assertEquals($user, $dbUser);
        $this->assertEquals($otp2, $dbOtp);
    }

    /**
     * A basic test example.
     */
    public function testUserOtpScopeLatest(): void
    {
        $user = User::factory()
                    ->create()
                    ->toArray();
        $otp1 = Otp::factory()
                    ->create([
                            'user_id' => $user['id'],
                            'created_at' => '2023-11-27 12:05:00',
                    ])
                    ->toArray();
        $otp2 = Otp::factory()
                    ->create([
                            'user_id' => $user['id'],
                            'created_at' => '2023-11-27 12:00:01',
                    ])
                    ->toArray();

        $dbUser = User::find($user['id'])
                    ->with('otp')
                    ->first();
        $dbOtp = $dbUser->otp->toArray();
        $dbUser = $dbUser->toArray();
        unset($dbUser['otp']);
        
        $this->assertEquals($user, $dbUser);
        $this->assertEquals($otp1, $dbOtp);
    }
}
