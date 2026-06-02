<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Assurance\Aal;
use Padosoft\Rebel\Core\Audit\AuditEvent;
use Padosoft\Rebel\Core\Audit\AuthEventType;

it('captures a verified email-OTP event without raw PII', function (): void {
    $event = new AuditEvent(
        type: AuthEventType::EmailOtpVerified->value,
        guard: 'customers',
        identifierHmac: 'deadbeef',
        keyVersion: 1,
        tenantId: 'site-1',
        purpose: 'customer-login',
        aal: Aal::Aal1,
        amr: ['otp', 'email'],
        riskScore: 12,
        metadata: ['channel_attempt' => 1],
    );

    expect($event->type)->toBe('email_otp.verified')
        ->and($event->aal)->toBe(Aal::Aal1)
        ->and($event->amr)->toBe(['otp', 'email'])
        ->and($event->keyVersion)->toBe(1)
        ->and($event->metadata)->toBe(['channel_attempt' => 1]);
});

it('accepts arbitrary type strings (for bridges, e.g. fortify.*)', function (): void {
    $event = new AuditEvent(type: 'fortify.login.succeeded', guard: 'web');

    expect($event->type)->toBe('fortify.login.succeeded')
        ->and($event->aal)->toBeNull();
});
