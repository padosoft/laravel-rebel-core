<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Clock;

use DateInterval;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * Orologio finto per i test: tempo fisso e avanzabile a piacere.
 *
 *   $clock = new FakeClock(new DateTimeImmutable('2026-01-01 10:00:00'));
 *   $clock->advance(60);            // +60 secondi
 *   $clock->now();                  // 2026-01-01 10:01:00
 */
final class FakeClock implements ClockInterface
{
    public function __construct(private DateTimeImmutable $now) {}

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }

    public function set(DateTimeImmutable $now): void
    {
        $this->now = $now;
    }

    public function advance(int $seconds): void
    {
        $interval = new DateInterval('PT'.abs($seconds).'S');

        $this->now = $seconds >= 0
            ? $this->now->add($interval)
            : $this->now->sub($interval);
    }
}
