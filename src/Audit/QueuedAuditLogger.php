<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

use Illuminate\Contracts\Bus\Dispatcher;
use Padosoft\Rebel\Core\Contracts\AuditLogger;

/**
 * Audit logger that writes asynchronously by dispatching a queued job, for
 * high-volume / enterprise workloads. The connection + queue are configurable
 * (`rebel-core.audit.connection` / `.queue`); leave them null to use the app's
 * default queue. The event is enriched (country, etc.) before it reaches here,
 * so nothing request-scoped is lost when the job runs later.
 */
final class QueuedAuditLogger implements AuditLogger
{
    public function __construct(
        private readonly Dispatcher $bus,
        private readonly ?string $connection = null,
        private readonly ?string $queue = null,
    ) {}

    public function record(AuditEvent $event): void
    {
        $job = new RecordAuditEventJob($event);

        if ($this->connection !== null) {
            $job->onConnection($this->connection);
        }

        if ($this->queue !== null) {
            $job->onQueue($this->queue);
        }

        $this->bus->dispatch($job);
    }
}
