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
