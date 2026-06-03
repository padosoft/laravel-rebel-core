<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Padosoft\Rebel\Core\Context\DeviceContext;

/**
 * Manages "trusted" devices (remembered device) to reduce step-up friction.
 * A trusted device expires after N days or on risk signals.
 */
interface DeviceTrust
{
    public function isTrusted(Authenticatable $user, DeviceContext $device): bool;

    public function trust(Authenticatable $user, DeviceContext $device, int $days): void;

    public function untrust(Authenticatable $user, DeviceContext $device): void;
}
