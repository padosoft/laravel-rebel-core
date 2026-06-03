<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Support;

/**
 * Redacts sensitive fields before writing them (audit/log). Rebel golden rule:
 * NEVER OTP/secret/token/password in the logs. The match is by substring,
 * case-insensitive, and recursive over nested arrays.
 *
 *   Redactor::sanitize(['otp' => '123456', 'attempt' => 1]);
 *   // ['otp' => '[REDACTED]', 'attempt' => 1]
 */
final class Redactor
{
    /**
     * Sensitive terms searched as a SUBSTRING (clearly secrets).
     *
     * @var list<string>
     */
    private const SENSITIVE_CONTAINS = [
        'otp', 'password', 'passwd', 'secret', 'token', 'bearer',
        'authorization', 'pepper', 'api_key', 'apikey', 'auth_token', 'private',
    ];

    /**
     * Sensitive keys compared EXACTLY. Short/ambiguous terms like 'code' here do
     * NOT redact 'country_code'/'postal_code'/'error_code' etc.
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
