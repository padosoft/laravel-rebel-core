<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Risk;

/**
 * Azione consigliata dal risk engine. È un SUGGERIMENTO: la decisione finale
 * spetta alle policy deterministiche (e mai all'AI, vedi ai-guard).
 */
enum RecommendedAction: string
{
    case Allow = 'allow';
    case StepUp = 'step_up';
    case Block = 'block';
    case Review = 'review';
}
