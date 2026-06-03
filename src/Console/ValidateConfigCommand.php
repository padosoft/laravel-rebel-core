<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Console;

use Illuminate\Console\Command;
use Padosoft\Rebel\Core\Contracts\ConfigValidator;

/**
 * `php artisan rebel:validate-config`
 *
 * Runs all validators registered under the 'rebel.config_validators' tag and fails
 * (exit code != 0) if the configuration is invalid. Meant to run in CI/deploy so
 * config errors (e.g. empty pepper, purpose with a driver below assurance) are
 * caught IMMEDIATELY and not at runtime.
 */
final class ValidateConfigCommand extends Command
{
    protected $signature = 'rebel:validate-config';

    protected $description = 'Valida la configurazione di Laravel Rebel (pepper, assurance, purpose...).';

    public function handle(): int
    {
        $hasErrors = false;

        foreach ($this->laravel->tagged('rebel.config_validators') as $validator) {
            if (! $validator instanceof ConfigValidator) {
                continue;
            }

            $errors = $validator->validate();

            if ($errors === []) {
                $this->info("[OK] {$validator->name()}");

                continue;
            }

            $hasErrors = true;

            foreach ($errors as $error) {
                $this->error("[{$validator->name()}] {$error}");
            }
        }

        if ($hasErrors) {
            $this->error('Configurazione Rebel NON valida.');

            return self::FAILURE;
        }

        $this->info('Configurazione Rebel valida.');

        return self::SUCCESS;
    }
}
