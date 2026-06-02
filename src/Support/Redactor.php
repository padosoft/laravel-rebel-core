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
    /**
     * Termini sensibili cercati come SOTTOSTRINGA (chiaramente segreti).
     *
     * @var list<string>
     */
    private const SENSITIVE_CONTAINS = [
        'otp', 'password', 'passwd', 'secret', 'token', 'bearer',
        'authorization', 'pepper', 'api_key', 'apikey', 'auth_token', 'private',
    ];

    /**
     * Chiavi sensibili confrontate in modo ESATTO. Termini corti/ambigui come
     * 'code' qui NON oscurano 'country_code'/'postal_code'/'error_code' ecc.
     *
     * @var list<string>
     */
    private const SENSITIVE_EXACT = [
        'code', 'pin', 'cvv', 'cvc', 'otp_code', 'verification_code',
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

        if (in_array($key, self::SENSITIVE_EXACT, true)) {
            return true;
        }

        foreach (self::SENSITIVE_CONTAINS as $needle) {
            if (str_contains($key, $needle)) {
                return true;
            }
        }

        return false;
    }
}
