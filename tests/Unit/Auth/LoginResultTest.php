<?php

declare(strict_types=1);

use Illuminate\Auth\GenericUser;
use Padosoft\Rebel\Core\Auth\LoginResult;
use Padosoft\Rebel\Core\Auth\TokenPair;

it('models a web login (session, no tokens)', function (): void {
    $user = new GenericUser(['id' => 1]);

    $result = LoginResult::web($user);

    expect($result->isWeb())->toBeTrue()
        ->and($result->isMobile())->toBeFalse()
        ->and($result->tokenPair())->toBeNull()
        ->and($result->user)->toBe($user);
});

it('models a mobile login (token pair, no session)', function (): void {
    $user = new GenericUser(['id' => 1]);
    $tokens = new TokenPair('access-abc', 'refresh-xyz', 900);

    $result = LoginResult::token($user, $tokens);

    expect($result->isMobile())->toBeTrue()
        ->and($result->isWeb())->toBeFalse()
        ->and($result->tokenPair())->toBe($tokens)
        ->and($result->tokenPair()?->expiresIn)->toBe(900)
        ->and($result->tokenPair()?->tokenType)->toBe('Bearer');
});
