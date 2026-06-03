<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Clock;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * Real clock (PSR-20). Used in production; tests use FakeClock to control time
 * (OTP TTL, step-up expirations, rate-limit windows).
 */
final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable;
    }
}
