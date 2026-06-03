<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Identifiers;

use InvalidArgumentException;
use Stringable;

/**
 * Email identifier.
 *
 *   EmailIdentifier::from(' Mario.Rossi@Example.IT ')->normalized(); // mario.rossi@example.it
 *
 *   EmailIdentifier::from('mario.rossi@example.it')->masked();       // m***@example.it
 *
 * Normalization (trim + lowercase) makes the HMAC and the lookup stable.
 * Masking hides the local part (shows only the first letter) for UI/logs.
 */
final readonly class EmailIdentifier implements AuthIdentifier, Stringable
{
    private function __construct(public string $value) {}

    public static function from(string $email): self
    {
        $normalized = strtolower(trim($email));

        $atPos = strpos($normalized, '@');
        $local = $atPos === false ? '' : substr($normalized, 0, $atPos);
        $domain = $atPos === false ? '' : substr($normalized, $atPos + 1);

        if ($local === '' || $domain === '' || ! str_contains($domain, '.')) {
            throw new InvalidArgumentException('Email non valida.');
        }

        return new self($normalized);
    }

    public function type(): string
    {
        return 'email';
    }

    public function normalized(): string
    {
        return $this->value;
    }

    public function masked(): string
    {
        [$local, $domain] = explode('@', $this->value, 2);

        // With a 1-character local part, showing the first letter would reveal the
        // ENTIRE local part: in that case we mask it completely.
        $maskedLocal = mb_strlen($local) <= 1 ? '***' : mb_substr($local, 0, 1).'***';

        return $maskedLocal.'@'.$domain;
    }

    public function __toString(): string
    {
        return $this->masked();
    }
}
