<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Assurance\Aal;
use Padosoft\Rebel\Core\Assurance\AssuranceLevel;

it('ranks AAL levels correctly', function (): void {
    expect(Aal::Aal2->satisfies(Aal::Aal1))->toBeTrue()
        ->and(Aal::Aal1->satisfies(Aal::Aal2))->toBeFalse()
        ->and(Aal::Aal3->satisfies(Aal::Aal3))->toBeTrue();
});

it('lets a passkey satisfy a high-assurance phishing-resistant purpose', function (): void {
    $passkey = new AssuranceLevel(Aal::Aal2, phishingResistant: true, amr: ['webauthn']);

    expect($passkey->satisfies(Aal::Aal2, requirePhishingResistant: true))->toBeTrue();
});

it('blocks email-OTP (AAL1, not phishing-resistant) on a high-assurance purpose', function (): void {
    // Questa è LA regola di sicurezza centrale: email-OTP non basta da solo per azioni forti.
    $emailOtp = new AssuranceLevel(Aal::Aal1, phishingResistant: false, amr: ['otp', 'email']);

    expect($emailOtp->satisfies(Aal::Aal2, requirePhishingResistant: true))->toBeFalse()
        ->and($emailOtp->satisfies(Aal::Aal2))->toBeFalse()
        ->and($emailOtp->satisfies(Aal::Aal1))->toBeTrue();
});

it('treats SMS as AAL2 but not phishing-resistant', function (): void {
    $sms = new AssuranceLevel(Aal::Aal2, phishingResistant: false, amr: ['sms'], restricted: true);

    expect($sms->satisfies(Aal::Aal2))->toBeTrue()
        ->and($sms->satisfies(Aal::Aal2, requirePhishingResistant: true))->toBeFalse()
        ->and($sms->restricted)->toBeTrue();
});

it('rejects a restricted authenticator when the purpose forbids it', function (): void {
    $sms = new AssuranceLevel(Aal::Aal2, phishingResistant: false, amr: ['sms'], restricted: true);
    $totp = new AssuranceLevel(Aal::Aal2, phishingResistant: false, amr: ['totp']);

    expect($sms->satisfies(Aal::Aal2, rejectRestricted: true))->toBeFalse()
        ->and($totp->satisfies(Aal::Aal2, rejectRestricted: true))->toBeTrue();
});
