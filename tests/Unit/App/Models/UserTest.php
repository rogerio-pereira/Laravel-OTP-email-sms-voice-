<?php

namespace Tests\Unit\App\Models;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public static function sanitizePhoneNumberDataProvider() : array
    {
        return [
            'noChange' => [
                'dirtyNumber' => '+11234567890',
                'expected' => '+11234567890',
            ],
            'noCountryCode' => [
                'dirtyNumber' => '1234567890',
                'expected' => '+11234567890',
            ],
            'spaces' => [
                'dirtyNumber' => '+1 123 456 7890',
                'expected' => '+11234567890',
            ],
            'parenthesis' => [
                'dirtyNumber' => '+1(123)4567890',
                'expected' => '+11234567890',
            ],
            'dashes' => [
                'dirtyNumber' => '+1-123-456-7890',
                'expected' => '+11234567890',
            ],
            'all' => [
                'dirtyNumber' => '(123) 456-7890',
                'expected' => '+11234567890',
            ],
        ];
    }

    /**
     * @dataProvider sanitizePhoneNumberDataProvider
     */
    public function testSanitizePhoneNumber(string $dirtyNumber, string $expected): void
    {
        $user = User::factory()->make([
                        'phone' => $dirtyNumber
                    ]);

        $sanitized = $user->sanitizedPhoneNumber();

        $this->assertEquals($expected, $sanitized);
    }
}
