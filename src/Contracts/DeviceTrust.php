<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Padosoft\Rebel\Core\Context\DeviceContext;

/**
 * Gestisce i dispositivi "fidati" (remembered device) per ridurre l'attrito dello
 * step-up. Un device fidato decade dopo N giorni o su segnali di rischio.
 */
interface DeviceTrust
{
    public function isTrusted(Authenticatable $user, DeviceContext $device): bool;

    public function trust(Authenticatable $user, DeviceContext $device, int $days): void;

    public function untrust(Authenticatable $user, DeviceContext $device): void;
}
