<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core;

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
}
