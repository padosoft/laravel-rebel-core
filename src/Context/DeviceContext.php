<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Context;

/**
 * Information about the device the request comes from.
 *
 * `deviceId` identifies a "remembered"/trusted device (see the sessions module);
 * `fingerprintHash` is the browser/device fingerprint (already hashed, never raw).
 */
final readonly class DeviceContext
{
    public function __construct(
        public ?string $deviceId = null,
        public ?string $fingerprintHash = null,
    ) {}
}
