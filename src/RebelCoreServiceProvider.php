<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Queue\Events\JobProcessing;
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
 * Service provider for the Laravel Rebel core package.
 *
 * The core is deliberately small and stable: it exposes value objects, contracts
 * and the suite's "shared language". It registers no routes and does not depend on
 * Fortify/Twilio/AI. See docs/adr/ADR-0005-design-lock.md.
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
        // PSR-20 clock: SystemClock in production; tests can rebind a FakeClock.
        $this->app->singleton(ClockInterface::class, SystemClock::class);

        $this->app->singleton(KeyedHasher::class, function (Application $app): HmacKeyedHasher {
            // make(Repository::class) is typed by Larastan as Repository (no @var needed).
            $config = $app->make(Repository::class);

            // Normalize the config in a type-safe way (no cast on mixed):
            // keep only [int => string] pairs, discarding malformed entries.
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

        // Current tenant (set by the app's TenantResolver) + config validators.
        $this->app->singleton(CurrentTenant::class);
        $this->app->tag([CoreConfigValidator::class], 'rebel.config_validators');
    }

    public function packageBooted(): void
    {
        // Prevents cross-tenant leakage between jobs in the same long-running worker:
        // reset the current tenant BEFORE processing each job.
        $this->app->make(Dispatcher::class)->listen(JobProcessing::class, function (): void {
            $this->app->make(CurrentTenant::class)->reset();
        });
    }
}
