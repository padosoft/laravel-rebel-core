<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Padosoft\Rebel\Core\Context\SecurityContext;
use Padosoft\Rebel\Core\Context\TenantContext;
use Padosoft\Rebel\Core\Hashing\HmacKeyedHasher;
use Padosoft\Rebel\Core\Identifiers\EmailIdentifier;

it('builds from a request hashing ip and user-agent (never raw)', function (): void {
    $request = Request::create('/login', 'POST', server: [
        'REMOTE_ADDR' => '203.0.113.5',
        'HTTP_USER_AGENT' => 'PestUA',
    ]);
    $hasher = new HmacKeyedHasher([1 => 'pepper'], 1);

    $ctx = SecurityContext::fromRequest($request, $hasher);

    expect($ctx->ipHmac)->toBe($hasher->hash('203.0.113.5')->hash)
        ->and($ctx->ipHmac)->not->toBe('203.0.113.5')
        ->and($ctx->userAgentHash)->toBe($hasher->hash('PestUA')->hash)
        ->and($ctx->requestId)->not->toBeEmpty();
});

it('does not hash an empty user-agent', function (): void {
    // Symfony Request::create sets HTTP_USER_AGENT='Symfony' by default, so to test
    // the "absent" branch we explicitly pass an empty UA.
    $request = Request::create('/login', 'GET', server: ['HTTP_USER_AGENT' => '']);

    $ctx = SecurityContext::fromRequest($request, new HmacKeyedHasher([1 => 'p'], 1));

    expect($ctx->userAgentHash)->toBeNull()
        ->and($ctx->ipHmac)->not->toBeNull(); // Request::create sets REMOTE_ADDR 127.0.0.1
});

it('is immutable: with* returns a new instance, original untouched', function (): void {
    $ctx = new SecurityContext(requestId: 'req-1');

    $next = $ctx->withGuard('customers')
        ->withPurpose('customer-login')
        ->withTenant(new TenantContext('site-1'))
        ->withIdentifier(EmailIdentifier::from('a@b.it'));

    expect($ctx->guard)->toBeNull()
        ->and($next)->not->toBe($ctx)
        ->and($next->guard)->toBe('customers')
        ->and($next->purpose)->toBe('customer-login')
        ->and($next->tenant?->id)->toBe('site-1')
        ->and($next->identifier?->type())->toBe('email')
        ->and($next->requestId)->toBe('req-1');
});
