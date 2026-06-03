<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

/**
 * A Rebel configuration validator. Each package (core, step-up, channels...)
 * registers its own under the 'rebel.config_validators' tag; the
 * `rebel:validate-config` command runs them all and fails on errors (fail-fast).
 */
interface ConfigValidator
{
    /** Short name of the validator (e.g. 'core', 'step-up'). */
    public function name(): string;

    /**
     * Runs the validation and returns the list of errors (empty = valid config).
     *
     * @return list<string>
     */
    public function validate(): array;
}
