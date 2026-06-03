<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Risk;

/**
 * Action recommended by the risk engine. It is a SUGGESTION: the final decision
 * belongs to the deterministic policies (and never to the AI, see ai-guard).
 */
enum RecommendedAction: string
{
    case Allow = 'allow';
    case StepUp = 'step_up';
    case Block = 'block';
    case Review = 'review';
}
