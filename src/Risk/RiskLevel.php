<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Risk;

/**
 * Qualitative risk level derived from the numeric score.
 *
 * Default thresholds (configurable downstream): 0-19 Low, 20-49 Medium,
 * 50-79 High, 80-100 Critical.
 */
enum RiskLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    public static function fromScore(int $score): self
    {
        return match (true) {
            $score >= 80 => self::Critical,
            $score >= 50 => self::High,
            $score >= 20 => self::Medium,
            default => self::Low,
        };
    }
}
