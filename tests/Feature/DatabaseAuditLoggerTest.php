<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Padosoft\Rebel\Core\Audit\AuditEvent;
use Padosoft\Rebel\Core\Contracts\AuditLogger;

it('records an event on rebel_auth_events and redacts sensitive metadata', function (): void {
    app(AuditLogger::class)->record(new AuditEvent(
        type: 'email_otp.verified',
        guard: 'customers',
        identifierHmac: 'abc123',
        keyVersion: 1,
        purpose: 'customer-login',
        amr: ['otp', 'email'],
        riskScore: 12,
        metadata: ['otp' => '123456', 'attempt' => 1, 'nested' => ['secret' => 's']],
    ));

    $row = DB::table('rebel_auth_events')->first();

    expect($row)->not->toBeNull();
    expect($row->event_type)->toBe('email_otp.verified')
        ->and($row->identifier_hmac)->toBe('abc123')
        ->and((int) $row->key_version)->toBe(1)
        ->and((int) $row->risk_score)->toBe(12);

    $meta = json_decode((string) $row->metadata, true);
    expect($meta['otp'])->toBe('[REDACTED]')
        ->and($meta['attempt'])->toBe(1)
        ->and($meta['nested']['secret'])->toBe('[REDACTED]');

    expect(json_decode((string) $row->amr, true))->toBe(['otp', 'email']);

    // L'OTP in chiaro non deve MAI comparire nella riga salvata.
    expect(DB::table('rebel_auth_events')->where('metadata', 'like', '%123456%')->count())->toBe(0);
});
