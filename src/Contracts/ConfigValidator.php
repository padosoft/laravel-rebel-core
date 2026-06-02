<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

/**
 * Un validatore di configurazione Rebel. Ogni package (core, step-up, channels...)
 * registra il proprio nel tag 'rebel.config_validators'; il comando
 * `rebel:validate-config` li esegue tutti e fallisce se trova errori (fail-fast).
 */
interface ConfigValidator
{
    /** Nome breve del validatore (es. 'core', 'step-up'). */
    public function name(): string;

    /**
     * Esegue la validazione e ritorna la lista degli errori (vuota = config valida).
     *
     * @return list<string>
     */
    public function validate(): array;
}
