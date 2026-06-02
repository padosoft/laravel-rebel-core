<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Clock\FakeClock;
use Padosoft\Rebel\Core\Clock\SystemClock;
use Psr\Clock\ClockInterface;

it('returns the fixed time and can advance/rewind', function (): void {
    $clock = new FakeClock(new DateTimeImmutable('2026-01-01 10:00:00'));

    expect($clock->now()->format('H:i:s'))->toBe('10:00:00');

    $clock->advance(90);
    expect($clock->now()->format('H:i:s'))->toBe('10:01:30');

    $clock->advance(-30);
    expect($clock->now()->format('H:i:s'))->toBe('10:01:00');

    $clock->set(new DateTimeImmutable('2030-12-31 23:59:59'));
    expect($clock->now()->format('Y-m-d'))->toBe('2030-12-31');
});

it('binds SystemClock as the PSR-20 ClockInterface', function (): void {
    expect(app(ClockInterface::class))->toBeInstanceOf(SystemClock::class);
});
