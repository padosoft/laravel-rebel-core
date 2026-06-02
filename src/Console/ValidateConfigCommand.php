<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Console;

use Illuminate\Console\Command;
use Padosoft\Rebel\Core\Contracts\ConfigValidator;

/**
 * `php artisan rebel:validate-config`
 *
 * Esegue tutti i validatori registrati nel tag 'rebel.config_validators' e fallisce
 * (exit code != 0) se la configurazione non è valida. Pensato per girare in CI/deploy
 * così gli errori di config (es. pepper vuoto, purpose con driver sotto assurance)
 * si vedono SUBITO e non a runtime.
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
