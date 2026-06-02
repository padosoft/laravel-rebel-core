<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Str;
use Padosoft\Rebel\Core\Contracts\AuditLogger;
use Padosoft\Rebel\Core\Support\Redactor;
use Psr\Clock\ClockInterface;

/**
 * Audit logger di default: scrive su `rebel_auth_events`.
 *
 * I metadata vengono SEMPRE sanitizzati (Redactor) prima di salvare: niente
 * OTP/secret nel DB. Il tempo viene dal Clock (testabile).
 */
final class DatabaseAuditLogger implements AuditLogger
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly ClockInterface $clock,
        private readonly string $table = 'rebel_auth_events',
    ) {}

    public function record(AuditEvent $event): void
    {
        $this->connection->table($this->table)->insert([
            'id' => (string) Str::ulid(),
            'tenant_id' => $event->tenantId,
            'event_type' => $event->type,
            'guard' => $event->guard,
            'subject_type' => $event->subjectType,
            'subject_id' => $event->subjectId,
            'identifier_hmac' => $event->identifierHmac,
            'key_version' => $event->keyVersion,
            'ip_hmac' => $event->ipHmac,
            'user_agent_hash' => $event->userAgentHash,
            'channel' => $event->channel,
            'provider' => $event->provider,
            'purpose' => $event->purpose,
            'aal' => $event->aal?->value,
            'amr' => $event->amr !== null ? json_encode($event->amr) : null,
            'risk_score' => $event->riskScore,
            'metadata' => json_encode(Redactor::sanitize($event->metadata)),
            'created_at' => $this->clock->now()->format('Y-m-d H:i:s'),
        ]);
    }
}
