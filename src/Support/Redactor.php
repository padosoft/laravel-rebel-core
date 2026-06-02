<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Support;

/**
 * Oscura i campi sensibili prima di scriverli (audit/log). Regola d'oro Rebel:
 * MAI OTP/secret/token/password nei log. Il match è per sottostringa, case-insensitive,
 * e ricorsivo sugli array annidati.
 *
 *   Redactor::sanitize(['otp' => '123456', 'attempt' => 1]);
 *   // ['otp' => '[REDACTED]', 'attempt' => 1]
 */
final class Redactor
{
    /** @var list<string> */
    private const SENSITIVE = [
        'otp', 'code', 'password', 'passwd', 'secret', 'token', 'bearer',
        'authorization', 'pepper', 'api_key', 'apikey', 'auth_token', 'private',
    ];

    public const REDACTED = '[REDACTED]';

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    public static function sanitize(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            if (self::isSensitiveKey((string) $key)) {
                $clean[$key] = self::REDACTED;

                continue;
            }

            $clean[$key] = is_array($value) ? self::sanitize($value) : $value;
        }

        return $clean;
    }

    private static function isSensitiveKey(string $key): bool
    {
        $key = strtolower($key);

        foreach (self::SENSITIVE as $needle) {
            if (str_contains($key, $needle)) {
                return true;
            }
        }

        return false;
    }
}
