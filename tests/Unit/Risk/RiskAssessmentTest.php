<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Risk\RecommendedAction;
use Padosoft\Rebel\Core\Risk\RiskAssessment;
use Padosoft\Rebel\Core\Risk\RiskLevel;

it('derives the qualitative level from the score', function (): void {
    expect(RiskLevel::fromScore(0))->toBe(RiskLevel::Low)
        ->and(RiskLevel::fromScore(19))->toBe(RiskLevel::Low)
        ->and(RiskLevel::fromScore(20))->toBe(RiskLevel::Medium)
        ->and(RiskLevel::fromScore(50))->toBe(RiskLevel::High)
        ->and(RiskLevel::fromScore(80))->toBe(RiskLevel::Critical)
        ->and(RiskLevel::fromScore(100))->toBe(RiskLevel::Critical);
});

it('builds an assessment from a score with machine-readable reasons', function (): void {
    $assessment = RiskAssessment::fromScore(65, ['new_device', 'high_value_order'], RecommendedAction::StepUp);

    expect($assessment->score)->toBe(65)
        ->and($assessment->level)->toBe(RiskLevel::High)
        ->and($assessment->reasons)->toBe(['new_device', 'high_value_order'])
        ->and($assessment->recommendedAction)->toBe(RecommendedAction::StepUp);
});

it('rejects out-of-range scores', function (int $bad): void {
    RiskAssessment::fromScore($bad);
})->throws(InvalidArgumentException::class)->with([-1, 101, 1000]);
