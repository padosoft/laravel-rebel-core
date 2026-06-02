<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Padosoft\Rebel\Core\Contracts\KeyedHasher;
use Padosoft\Rebel\Core\Hashing\HmacKeyedHasher;
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
            ->hasConfigFile('rebel-core');
    }

    public function packageRegistered(): void
    {
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
    }
}
