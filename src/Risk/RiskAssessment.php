<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Risk;

use InvalidArgumentException;

/**
 * Output standard del risk engine.
 *
 *  - score (0-100): più alto = più rischioso;
 *  - level: la fascia qualitativa derivata dallo score;
 *  - reasons: codici machine-readable (es. 'new_device', 'impossible_travel'),
 *    NON testi tradotti (le label umane stanno nei file di traduzione);
 *  - recommendedAction: suggerimento (allow/step_up/block/review).
 *
 * Esempio:
 *   RiskAssessment::fromScore(65, ['new_device', 'high_value_order'], RecommendedAction::StepUp);
 */
final readonly class RiskAssessment
{
    /**
     * @param  list<string>  $reasons
     */
    public function __construct(
        public int $score,
        public RiskLevel $level,
        public array $reasons,
        public RecommendedAction $recommendedAction,
    ) {
        if ($score < 0 || $score > 100) {
            throw new InvalidArgumentException("Score di rischio fuori range (0-100): {$score}.");
        }
    }

    /**
     * @param  list<string>  $reasons
     */
    public static function fromScore(
        int $score,
        array $reasons = [],
        RecommendedAction $recommendedAction = RecommendedAction::Allow,
    ): self {
        return new self($score, RiskLevel::fromScore($score), $reasons, $recommendedAction);
    }
}
