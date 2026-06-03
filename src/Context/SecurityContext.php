<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Context;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Padosoft\Rebel\Core\Contracts\KeyedHasher;
use Padosoft\Rebel\Core\Identifiers\AuthIdentifier;

/**
 * Security context shared by OTP, step-up, audit and the risk engine.
 *
 * It is an immutable value object: the with*() methods return a NEW instance.
 * IP and User-Agent are stored as HMAC (never cleartext) for privacy/GDPR; hashing
 * is delegated to the KeyedHasher (passed to fromRequest), so the VO stays pure and
 * testable.
 *
 * Note on rotation: ip/ua use the CURRENT pepper version at creation time. When an
 * AuditEvent is recorded, its single `key_version` (that of the identifier, also
 * hashed with the current version) applies to ip/ua as well. Correlation on
 * ip_hmac/user_agent_hash is therefore valid WITHIN a pepper epoch; after a
 * rotation, old hashes are not comparable with new ones (they are used for
 * correlation/grouping, not for authentication: an accepted limitation).
 *
 * Example:
 *   $ctx = SecurityContext::fromRequest($request, $hasher)
 *       ->withGuard('customers')
 *       ->withPurpose('customer-login')
 *       ->withIdentifier(EmailIdentifier::from($request->input('email')));
 */
final readonly class SecurityContext
{
    /**
     * @param  array<string, mixed>  $riskContext
     */
    public function __construct(
        public string $requestId,
        public ?TenantContext $tenant = null,
        public ?string $guard = null,
        public ?string $purpose = null,
        public ?AuthIdentifier $identifier = null,
        public ?string $ipHmac = null,
        public ?string $userAgentHash = null,
        public ?DeviceContext $device = null,
        public array $riskContext = [],
    ) {}

    public static function fromRequest(Request $request, KeyedHasher $hasher): self
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        return new self(
            requestId: Str::uuid()->toString(),
            ipHmac: $ip !== null && $ip !== '' ? $hasher->hash($ip)->hash : null,
            userAgentHash: $userAgent !== null && $userAgent !== '' ? $hasher->hash($userAgent)->hash : null,
        );
    }

    public function withTenant(?TenantContext $tenant): self
    {
        return new self($this->requestId, $tenant, $this->guard, $this->purpose, $this->identifier, $this->ipHmac, $this->userAgentHash, $this->device, $this->riskContext);
    }

    public function withGuard(?string $guard): self
    {
        return new self($this->requestId, $this->tenant, $guard, $this->purpose, $this->identifier, $this->ipHmac, $this->userAgentHash, $this->device, $this->riskContext);
    }

    public function withPurpose(?string $purpose): self
    {
        return new self($this->requestId, $this->tenant, $this->guard, $purpose, $this->identifier, $this->ipHmac, $this->userAgentHash, $this->device, $this->riskContext);
    }

    public function withIdentifier(?AuthIdentifier $identifier): self
    {
        return new self($this->requestId, $this->tenant, $this->guard, $this->purpose, $identifier, $this->ipHmac, $this->userAgentHash, $this->device, $this->riskContext);
    }

    public function withDevice(?DeviceContext $device): self
    {
        return new self($this->requestId, $this->tenant, $this->guard, $this->purpose, $this->identifier, $this->ipHmac, $this->userAgentHash, $device, $this->riskContext);
    }

    /**
     * @param  array<string, mixed>  $riskContext
     */
    public function withRiskContext(array $riskContext): self
    {
        return new self($this->requestId, $this->tenant, $this->guard, $this->purpose, $this->identifier, $this->ipHmac, $this->userAgentHash, $this->device, $riskContext);
    }
}
