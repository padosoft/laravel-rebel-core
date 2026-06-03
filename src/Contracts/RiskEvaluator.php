<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Padosoft\Rebel\Core\Context\SecurityContext;
use Padosoft\Rebel\Core\Risk\RiskAssessment;

/**
 * Evaluates the risk of a context and produces a RiskAssessment (score + reasons +
 * recommended action). Deterministic by default; the app can provide custom signals.
 */
interface RiskEvaluator
{
    public function evaluate(SecurityContext $context): RiskAssessment;
}
