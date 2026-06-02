<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Contracts\KeyedHasher;
use Padosoft\Rebel\Core\Hashing\HmacKeyedHasher;

it('hashes deterministically with the current pepper', function (): void {
    $hasher = new HmacKeyedHasher([1 => 'pepper-v1'], 1);

    $a = $hasher->hash('mario.rossi@example.it');
    $b = $hasher->hash('mario.rossi@example.it');

    expect($a->hash)->toBe($b->hash)
        ->and($a->keyVersion)->toBe(1)
        ->and($a->hash)->not->toBe('mario.rossi@example.it');
});

it('produces different hashes for different peppers', function (): void {
    $h1 = new HmacKeyedHasher([1 => 'pepper-A'], 1);
    $h2 = new HmacKeyedHasher([1 => 'pepper-B'], 1);

    expect($h1->hash('x')->hash)->not->toBe($h2->hash('x')->hash);
});

it('matches a value against its stored hash (constant-time)', function (): void {
    $hasher = new HmacKeyedHasher([1 => 'pepper-v1'], 1);
    $hashed = $hasher->hash('+391112223334');

    expect($hasher->matches('+391112223334', $hashed->hash, 1))->toBeTrue()
        ->and($hasher->matches('+390000000000', $hashed->hash, 1))->toBeFalse();
});

it('supports key rotation: a hash made with an old version still verifies', function (): void {
    $old = (new HmacKeyedHasher([1 => 'pepper-v1'], 1))->hash('value');

    // Il corrente è v2, ma v1 resta nel registro per la verifica.
    $rotated = new HmacKeyedHasher([1 => 'pepper-v1', 2 => 'pepper-v2'], 2);

    expect($rotated->hash('value')->keyVersion)->toBe(2)
        ->and($rotated->matches('value', $old->hash, 1))->toBeTrue();
});

it('returns false for an unknown or empty key version', function (): void {
    $hasher = new HmacKeyedHasher([1 => 'pepper-v1', 2 => ''], 1);

    expect($hasher->matches('value', 'whatever', 9))->toBeFalse()
        ->and($hasher->matches('value', 'whatever', 2))->toBeFalse();
});

it('throws when hashing with an empty pepper', function (): void {
    (new HmacKeyedHasher([1 => ''], 1))->hash('value');
})->throws(InvalidArgumentException::class);

it('throws immediately for an unsupported HMAC algorithm', function (): void {
    new HmacKeyedHasher([1 => 'pepper'], 1, 'notanalgo');
})->throws(InvalidArgumentException::class);

it('is resolvable from the container as KeyedHasher', function (): void {
    config()->set('rebel-core.peppers', [1 => 'container-pepper']);
    config()->set('rebel-core.pepper_current', 1);

    $hasher = app(KeyedHasher::class);

    expect($hasher)->toBeInstanceOf(HmacKeyedHasher::class)
        ->and($hasher->hash('a')->keyVersion)->toBe(1);
});
