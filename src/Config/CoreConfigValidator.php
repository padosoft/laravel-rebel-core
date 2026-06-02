<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Config;

use Illuminate\Contracts\Config\Repository;
use Padosoft\Rebel\Core\Contracts\ConfigValidator;

/**
 * Valida la config del core: la versione di pepper corrente deve esistere e non
 * essere vuota (altrimenti gli HMAC non sono sicuri / l'app si rompe a runtime).
 */
final class CoreConfigValidator implements ConfigValidator
{
    public function __construct(private readonly Repository $config) {}

    public function name(): string
    {
        return 'core';
    }

    public function validate(): array
    {
        $current = $this->config->get('rebel-core.pepper_current');

        if (! is_int($current)) {
            return ['rebel-core.pepper_current deve essere un intero.'];
        }

        $peppers = $this->config->get('rebel-core.peppers');

        if (! is_array($peppers) || ! array_key_exists($current, $peppers)) {
            return ["rebel-core.peppers non contiene la versione corrente ({$current})."];
        }

        $pepper = $peppers[$current];

        if (! is_string($pepper) || $pepper === '') {
            return ["Il pepper corrente (v{$current}) è vuoto: imposta REBEL_PEPPER_V{$current} nel .env."];
        }

        return [];
    }
}
