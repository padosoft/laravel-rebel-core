<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Risk;

/**
 * Livello di rischio qualitativo derivato dallo score numerico.
 *
 * Soglie di default (configurabili a valle): 0-19 Low, 20-49 Medium,
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
