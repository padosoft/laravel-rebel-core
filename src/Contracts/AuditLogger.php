<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Padosoft\Rebel\Core\Audit\AuditEvent;

/**
 * Records audit events. The default implementation writes to the DB
 * (rebel_auth_events); other implementations can ship to a SIEM, etc.
 */
interface AuditLogger
{
    public function record(AuditEvent $event): void;
}
