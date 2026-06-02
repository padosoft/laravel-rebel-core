<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Padosoft\Rebel\Core\Context\SecurityContext;
use Padosoft\Rebel\Core\Identifiers\AuthIdentifier;

/**
 * Risolve un identificatore (es. email) nell'utente applicativo, dato il contesto
 * (tenant/guard). Ritorna null se non esiste (la "user resolution ritardata" del
 * flusso OTP passa di qui). L'app/Gescat fornisce la propria implementazione.
 */
interface SubjectResolver
{
    public function resolve(AuthIdentifier $identifier, SecurityContext $context): ?Authenticatable;
}
