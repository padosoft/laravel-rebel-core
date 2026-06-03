<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Str;
use Padosoft\Rebel\Core\Contracts\AuditLogger;
use Padosoft\Rebel\Core\Support\Redactor;
use Psr\Clock\ClockInterface;

/**
 * Default audit logger: writes to `rebel_auth_events`.
 *
 * Metadata is ALWAYS sanitized (Redactor) before saving: no OTP/secret in the
 * DB. The time comes from the Clock (testable).
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
            'amr' => $event->amr !== null ? $this->json($event->amr) : null,
            'risk_score' => $event->riskScore,
            'country' => $event->country,
            'metadata' => $this->json(Redactor::sanitize($event->metadata)),
            'created_at' => $this->clock->now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Robust JSON encoding for the audit: substitutes invalid UTF-8 (instead of
     * returning false and corrupting the row) and has a fallback. The audit must
     * NEVER break the application flow.
     *
     * @param  array<array-key, mixed>  $value
     */
    private function json(array $value): string
    {
        return json_encode($value, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }
}
