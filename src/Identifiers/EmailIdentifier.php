<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Identifiers;

use InvalidArgumentException;
use Stringable;

/**
 * Identificatore email.
 *
 *   EmailIdentifier::from(' Mario.Rossi@Example.IT ')->normalized(); // mario.rossi@example.it
 *
 *   EmailIdentifier::from('mario.rossi@example.it')->masked();       // m***@example.it
 *
 * La normalizzazione (trim + lowercase) rende stabile l'HMAC e il lookup.
 * Il masking nasconde la parte locale (mostra solo la prima lettera) per UI/log.
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

        // Con local part di 1 carattere, mostrare la prima lettera rivelerebbe TUTTA
        // la parte locale: in quel caso mascheriamo completamente.
        $maskedLocal = mb_strlen($local) <= 1 ? '***' : mb_substr($local, 0, 1).'***';

        return $maskedLocal.'@'.$domain;
    }

    public function __toString(): string
    {
        return $this->masked();
    }
}
