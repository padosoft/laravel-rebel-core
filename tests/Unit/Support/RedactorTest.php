<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Support\Redactor;

it('redacts sensitive keys recursively and keeps the rest', function (): void {
    $clean = Redactor::sanitize([
        'otp' => '123456',
        'attempt' => 1,
        'email' => 'a@b.it',
        'nested' => ['password' => 'x', 'ok' => true],
        'Authorization' => 'Bearer xyz',
        'api_key' => 'k',
    ]);

    expect($clean['otp'])->toBe('[REDACTED]')
        ->and($clean['attempt'])->toBe(1)
        ->and($clean['email'])->toBe('a@b.it')
        ->and($clean['nested']['password'])->toBe('[REDACTED]')
        ->and($clean['nested']['ok'])->toBeTrue()
        ->and($clean['Authorization'])->toBe('[REDACTED]')
        ->and($clean['api_key'])->toBe('[REDACTED]');
});

it('does not over-redact diagnostic keys containing "code"', function (): void {
    $clean = Redactor::sanitize([
        'country_code' => 'IT',
        'postal_code' => '20100',
        'error_code' => 'E42',
        'status_code' => 200,
        'code' => '123456',          // chiave esatta "code" -> oscurata
        'verification_code' => '999', // esatta -> oscurata
    ]);

    expect($clean['country_code'])->toBe('IT')
        ->and($clean['postal_code'])->toBe('20100')
        ->and($clean['error_code'])->toBe('E42')
        ->and($clean['status_code'])->toBe(200)
        ->and($clean['code'])->toBe('[REDACTED]')
        ->and($clean['verification_code'])->toBe('[REDACTED]');
});
