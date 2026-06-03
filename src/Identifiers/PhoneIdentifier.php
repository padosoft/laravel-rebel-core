<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Identifiers;

use InvalidArgumentException;
use Stringable;

/**
 * Phone identifier.
 *
 *   PhoneIdentifier::from('+39 328 214 6956')->normalized(); // +393282146956
 *   PhoneIdentifier::from('+393282146956')->masked();        // +**********56
 *
 * "Light" normalization: strips separators, keeps a possible leading '+'. It does
 * NOT perform full E.164 validation (that would need libphonenumber): a dedicated
 * bridge can add per-country validation/formatting. Here we only guarantee there
 * are enough digits to be plausible.
 */
final readonly class PhoneIdentifier implements AuthIdentifier, Stringable
{
    private function __construct(public string $value) {}

    public static function from(string $phone): self
    {
        $trimmed = trim($phone);
        $hasPlus = str_starts_with($trimmed, '+');
        $digits = preg_replace('/\D+/', '', $trimmed) ?? '';

        if (strlen($digits) < 6) {
            throw new InvalidArgumentException('Numero di telefono non valido.');
        }

        return new self(($hasPlus ? '+' : '').$digits);
    }

    public function type(): string
    {
        return 'phone';
    }

    public function normalized(): string
    {
        return $this->value;
    }

    public function masked(): string
    {
        $digits = ltrim($this->value, '+');
        $last2 = substr($digits, -2);
        $prefix = str_starts_with($this->value, '+') ? '+' : '';

        return $prefix.str_repeat('*', max(1, strlen($digits) - 2)).$last2;
    }

    public function __toString(): string
    {
        return $this->masked();
    }
}
