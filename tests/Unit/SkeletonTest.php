<?php

declare(strict_types=1);

it('boots the service provider and loads the core config', function (): void {
    expect(config('rebel-core'))->toBeArray()
        ->and(config('rebel-core.hmac_algo'))->toBe('sha256')
        ->and(config('rebel-core.pepper_current'))->toBe(1);
});
