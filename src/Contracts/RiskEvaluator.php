<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Padosoft\Rebel\Core\Context\SecurityContext;
use Padosoft\Rebel\Core\Risk\RiskAssessment;

/**
 * Valuta il rischio di un contesto e produce un RiskAssessment (score + reasons +
 * azione consigliata). Deterministico di default; l'app può fornire segnali custom.
 */
interface RiskEvaluator
{
    public function evaluate(SecurityContext $context): RiskAssessment;
}
