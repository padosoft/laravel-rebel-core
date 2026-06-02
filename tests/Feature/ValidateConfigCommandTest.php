<?php

declare(strict_types=1);

it('passes validation with a non-empty current pepper', function (): void {
    config()->set('rebel-core.peppers', [1 => 'a-real-pepper']);
    config()->set('rebel-core.pepper_current', 1);

    $this->artisan('rebel:validate-config')
        ->expectsOutputToContain('[OK] core')
        ->assertExitCode(0);
});

it('fails validation when the current pepper is empty', function (): void {
    config()->set('rebel-core.peppers', [1 => '']);
    config()->set('rebel-core.pepper_current', 1);

    $this->artisan('rebel:validate-config')->assertExitCode(1);
});

it('fails validation when the current pepper version is missing', function (): void {
    config()->set('rebel-core.peppers', [1 => 'x']);
    config()->set('rebel-core.pepper_current', 2);

    $this->artisan('rebel:validate-config')->assertExitCode(1);
});
