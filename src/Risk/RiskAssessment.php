<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Risk;

use InvalidArgumentException;

/**
 * Standard output of the risk engine.
 *
 *  - score (0-100): higher = riskier;
 *  - level: the qualitative band derived from the score;
 *  - reasons: machine-readable codes (e.g. 'new_device', 'impossible_travel'),
 *    NOT translated text (human labels live in the translation files);
 *  - recommendedAction: suggestion (allow/step_up/block/review).
 *
 * Example:
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
