<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Identifiers;

use InvalidArgumentException;
use Stringable;

/**
 * Generic identifier (e.g. username) when it is neither an email nor a phone.
 *
 *   GenericIdentifier::from('  Mario_Rossi ')->normalized(); // mario_rossi
 *   GenericIdentifier::from('mario_rossi')->masked();        // m***
 */
final readonly class GenericIdentifier implements AuthIdentifier, Stringable
{
    private function __construct(public string $value) {}

    public static function from(string $identifier): self
    {
        $normalized = strtolower(trim($identifier));

        if ($normalized === '') {
            throw new InvalidArgumentException('Identificatore vuoto.');
        }

        return new self($normalized);
    }

    public function type(): string
    {
        return 'generic';
    }

    public function normalized(): string
    {
        return $this->value;
    }

    public function masked(): string
    {
        return mb_substr($this->value, 0, 1).'***';
    }

    public function __toString(): string
    {
        return $this->masked();
    }
}
