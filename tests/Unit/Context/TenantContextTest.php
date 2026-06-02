<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Context\TenantContext;

it('exposes id and attributes', function (): void {
    $tenant = new TenantContext('site-1', ['nazione' => 'IT', 'brand' => 'acme']);

    expect($tenant->id)->toBe('site-1')
        ->and($tenant->attribute('nazione'))->toBe('IT')
        ->and($tenant->attribute('brand'))->toBe('acme')
        ->and($tenant->attribute('missing'))->toBeNull();
});
