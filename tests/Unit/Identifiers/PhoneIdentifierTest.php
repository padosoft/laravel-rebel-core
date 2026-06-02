<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Identifiers\PhoneIdentifier;

it('normalizes phone removing separators and keeping the leading +', function (): void {
    expect(PhoneIdentifier::from('+39 111 222 3334')->normalized())->toBe('+391112223334')
        ->and(PhoneIdentifier::from('011-1234567')->normalized())->toBe('0111234567');
});

it('masks all but the last two digits', function (): void {
    // 12 cifre dopo il '+': 10 mascherate + ultime 2 in chiaro
    expect(PhoneIdentifier::from('+391112223334')->masked())
        ->toBe('+'.str_repeat('*', 10).'34');
});

it('exposes the phone type', function (): void {
    expect(PhoneIdentifier::from('+391112223334')->type())->toBe('phone');
});

it('rejects too-short numbers', function (): void {
    PhoneIdentifier::from('123');
})->throws(InvalidArgumentException::class);
