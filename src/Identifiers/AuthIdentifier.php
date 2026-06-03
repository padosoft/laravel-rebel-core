<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Identifiers;

/**
 * An identifier a user authenticates with (email, phone, username...).
 *
 * Three responsibilities:
 *  - type():       the type ('email' | 'phone' | 'generic'), useful for routing/policy;
 *  - normalized(): the canonical form used for lookup and the HMAC (e.g. lowercase email);
 *  - masked():     an obfuscated form, safe to show in UI or logs (no full PII).
 *
 * Note: identifiers do NOT compute the HMAC themselves: that is the KeyedHasher's
 * job (it hashes normalized()). This keeps the value objects "pure" and testable.
 */
interface AuthIdentifier
{
    public function type(): string;

    public function normalized(): string;

    public function masked(): string;
}
