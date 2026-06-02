<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Padosoft\Rebel\Core\Audit\DatabaseAuditLogger;
use Padosoft\Rebel\Core\Clock\SystemClock;
use Padosoft\Rebel\Core\Config\CoreConfigValidator;
use Padosoft\Rebel\Core\Console\ValidateConfigCommand;
use Padosoft\Rebel\Core\Contracts\AuditLogger;
use Padosoft\Rebel\Core\Contracts\KeyedHasher;
use Padosoft\Rebel\Core\Hashing\HmacKeyedHasher;
use Padosoft\Rebel\Core\Tenancy\CurrentTenant;
use Psr\Clock\ClockInterface;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service provider del package core di Laravel Rebel.
 *
 * Il core è volutamente piccolo e stabile: espone value object, contratti e il
 * "linguaggio condiviso" della suite. Non registra route, non dipende da
 * Fortify/Twilio/AI. Vedi docs/adr/ADR-0005-design-lock.md.
 */
final class RebelCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-rebel-core')
            ->hasConfigFile('rebel-core')
            ->hasMigration('create_rebel_auth_events_table')
            ->hasCommand(ValidateConfigCommand::class);
    }

    public function packageRegistered(): void
    {
        // Orologio PSR-20: in produzione SystemClock; i test possono rebindare un FakeClock.
        $this->app->singleton(ClockInterface::class, SystemClock::class);

        $this->app->singleton(KeyedHasher::class, function (Application $app): HmacKeyedHasher {
            // make(Repository::class) è tipizzato da Larastan come Repository (niente @var).
            $config = $app->make(Repository::class);

            // Normalizziamo la config in modo type-safe (niente cast su mixed):
            // teniamo solo coppie [int => string], scartando voci malformate.
            $peppers = [];
            $rawPeppers = $config->get('rebel-core.peppers', []);

            if (is_array($rawPeppers)) {
                foreach ($rawPeppers as $version => $secret) {
                    if (is_int($version) && is_string($secret)) {
                        $peppers[$version] = $secret;
                    }
                }
            }

            $current = $config->get('rebel-core.pepper_current', 1);
            $algo = $config->get('rebel-core.hmac_algo', 'sha256');

            return new HmacKeyedHasher(
                peppers: $peppers,
                currentVersion: is_int($current) ? $current : 1,
                algo: is_string($algo) ? $algo : 'sha256',
            );
        });

        $this->app->singleton(AuditLogger::class, function (Application $app): DatabaseAuditLogger {
            return new DatabaseAuditLogger(
                $app->make(DatabaseManager::class)->connection(),
                $app->make(ClockInterface::class),
            );
        });

        // Tenant corrente (lo imposta il TenantResolver dell'app) + validatori di config.
        $this->app->singleton(CurrentTenant::class);
        $this->app->tag([CoreConfigValidator::class], 'rebel.config_validators');
    }
}
