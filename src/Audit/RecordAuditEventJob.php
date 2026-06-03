<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Queued audit write: persists one already-enriched AuditEvent off the request
 * thread (used when `rebel-core.audit.mode = queue`). The event is fully formed
 * before it is queued, so the worker just writes it via the synchronous logger —
 * Horizon and any standard Laravel queue connection work out of the box.
 */
final class RecordAuditEventJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly AuditEvent $event) {}

    public function handle(DatabaseAuditLogger $logger): void
    {
        $logger->record($this->event);
    }
}
