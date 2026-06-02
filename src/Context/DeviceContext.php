<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Context;

/**
 * Informazioni sul dispositivo da cui arriva la richiesta.
 *
 * `deviceId` identifica un device "ricordato"/fidato (vedi modulo sessions);
 * `fingerprintHash` è l'impronta del browser/dispositivo (già hashata, mai raw).
 */
final readonly class DeviceContext
{
    public function __construct(
        public ?string $deviceId = null,
        public ?string $fingerprintHash = null,
    ) {}
}
