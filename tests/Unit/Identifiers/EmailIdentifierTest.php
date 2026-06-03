<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Identifiers\EmailIdentifier;

it('normalizes email (trim + lowercase)', function (): void {
    expect(EmailIdentifier::from('  Mario.Rossi@Example.IT ')->normalized())
        ->toBe('mario.rossi@example.it');
});

it('masks the local part hiding all but the first letter', function (): void {
    expect(EmailIdentifier::from('mario.rossi@example.it')->masked())
        ->toBe('m***@example.it');
});

it('exposes the email type', function (): void {
    expect(EmailIdentifier::from('a@b.it')->type())->toBe('email');
});

it('fully masks a single-character local part (no PII leak)', function (): void {
    // 'a@example.it' must NOT become 'a***@...': it would reveal the entire local part.
    expect(EmailIdentifier::from('a@example.it')->masked())->toBe('***@example.it');
});

it('rejects invalid emails', function (string $bad): void {
    EmailIdentifier::from($bad);
})->throws(InvalidArgumentException::class)->with([
    'vuota' => '',
    'senza @' => 'notanemail',
    'senza local' => '@example.it',
    'senza dominio' => 'mario@',
    'dominio senza punto' => 'mario@nodot',
]);
