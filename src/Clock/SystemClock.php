<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Clock;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * Orologio reale (PSR-20). In produzione si usa questo; nei test si usa FakeClock
 * per controllare il tempo (TTL OTP, scadenze step-up, finestre di rate-limit).
 */
final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable;
    }
}
