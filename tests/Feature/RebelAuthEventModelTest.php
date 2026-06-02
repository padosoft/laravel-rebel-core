<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Assurance\Aal;
use Padosoft\Rebel\Core\Audit\AuditEvent;
use Padosoft\Rebel\Core\Contracts\AuditLogger;
use Padosoft\Rebel\Core\Models\RebelAuthEvent;

it('reads a recorded event via the Eloquent model with proper casts', function (): void {
    app(AuditLogger::class)->record(new AuditEvent(
        type: 'login.succeeded',
        guard: 'customers',
        aal: Aal::Aal2,
        amr: ['webauthn'],
        metadata: ['x' => 1],
    ));

    $event = RebelAuthEvent::query()->firstOrFail();

    expect($event->getAttribute('event_type'))->toBe('login.succeeded')
        ->and($event->getAttribute('aal'))->toBe(Aal::Aal2)
        ->and($event->getAttribute('amr'))->toBe(['webauthn'])
        ->and($event->getAttribute('metadata'))->toBe(['x' => 1]);
});
