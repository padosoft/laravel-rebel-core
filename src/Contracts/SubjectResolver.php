<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Padosoft\Rebel\Core\Context\SecurityContext;
use Padosoft\Rebel\Core\Identifiers\AuthIdentifier;

/**
 * Resolves an identifier (e.g. email) to the application user, given the context
 * (tenant/guard). Returns null if it does not exist (the OTP flow's "deferred user
 * resolution" goes through here). The app provides its own implementation.
 */
interface SubjectResolver
{
    public function resolve(AuthIdentifier $identifier, SecurityContext $context): ?Authenticatable;
}
