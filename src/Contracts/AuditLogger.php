<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Padosoft\Rebel\Core\Audit\AuditEvent;

/**
 * Registra gli eventi di audit. L'implementazione di default scrive su DB
 * (rebel_auth_events); altre implementazioni possono spedire a un SIEM, ecc.
 */
interface AuditLogger
{
    public function record(AuditEvent $event): void;
}
