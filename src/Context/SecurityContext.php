<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Context;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Padosoft\Rebel\Core\Contracts\KeyedHasher;
use Padosoft\Rebel\Core\Identifiers\AuthIdentifier;

/**
 * Contesto di sicurezza condiviso da OTP, step-up, audit e risk engine.
 *
 * È un value object immutabile: i metodi with*() ritornano una NUOVA istanza.
 * IP e User-Agent sono salvati come HMAC (mai in chiaro) per privacy/GDPR; l'hashing
 * è delegato al KeyedHasher (passato a fromRequest), così il VO resta puro e testabile.
 *
 * Nota su rotazione: ip/ua usano la versione di pepper CORRENTE al momento della
 * creazione. Quando si registra un AuditEvent, la sua singola `key_version` (quella
 * dell'identifier, anch'esso hashato con la versione corrente) vale anche per ip/ua.
 * La correlazione su ip_hmac/user_agent_hash è quindi valida ENTRO un'epoca di pepper;
 * dopo una rotazione, gli hash vecchi non sono confrontabili con quelli nuovi (sono
 * usati per correlazione/raggruppamento, non per autenticazione: limite accettato).
 *
 * Esempio:
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
