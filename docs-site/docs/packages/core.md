---
title: laravel-rebel-core
description: The foundation of the Laravel Rebel suite — shared value objects, the NIST AAL/AMR assurance model, security context, keyed hashing, the redacting audit trail and the contracts every other package builds on.
---

# laravel-rebel-core

![Laravel Rebel](/assets/laravel-rebel-banner.png)

[GitHub repository](https://github.com/padosoft/laravel-rebel-core) · Composer: `padosoft/laravel-rebel-core` · MIT

> **The heart of the ecosystem.** Small, stable, and depended on by everything else: `-core` defines
> the shared *language* — value objects, the assurance model, security context, keyed hashing, the
> audit trail and the contracts — that the rest of the Laravel Rebel suite is built on.

::: callout info
You usually don't install `-core` on its own — it comes in as a dependency of the feature packages.
But you can use it stand-alone for its value objects and contracts.
:::

---

## What it is

The core is deliberately **small and slow-changing**. It contains no routes, no UI, and no hard
dependency on Fortify, Twilio or any AI provider. What it *does* provide is the vocabulary that lets
20+ other packages speak to each other and stay auditable end-to-end:

- a typed **assurance model** (NIST AAL/AMR) with a guard that enforces it,
- **keyed hashing** so no PII is ever stored in cleartext,
- a **redacting audit trail** that can't leak secrets,
- and a set of **contracts** you can swap to bind your own infrastructure.

## The problem it solves

Most Laravel apps treat "is this user authenticated strongly enough?" as a boolean and store IPs,
emails and user-agents in cleartext. That falls apart the moment you have sensitive actions, regulated
payments, or a GDPR audit. The core fixes the substrate: assurance becomes a first-class type, PII
becomes a keyed HMAC, and every security-significant outcome flows through one audit contract.

---

## What's inside

| Area | What you get |
|---|---|
| **Identifiers** | `EmailIdentifier`, `PhoneIdentifier`, `GenericIdentifier` — normalize and mask email/phone. |
| **Keyed hashing** | `KeyedHasher` / `HmacKeyedHasher` — HMAC with a **versioned pepper** and rotation (for email/IP/OTP), constant-time compare. |
| **Assurance** | `Aal`, `AssuranceLevel` — the model that stops an email-OTP (AAL1) from "covering" an action that needs a passkey. |
| **Context** | `SecurityContext`, `TenantContext`, `DeviceContext` — request context with IP/UA already hashed. |
| **Risk** | `RiskAssessment`, `RiskLevel`, `RecommendedAction`. |
| **Auth** | `LoginResult` (web \| token), `TokenPair` (Sanctum access + refresh). |
| **Audit** | `AuditEvent`, `DatabaseAuditLogger` (+ `rebel_auth_events`), `Redactor` (never logs OTP/secret). |
| **Contracts** | `TokenIssuer`, `SubjectResolver`, `TenantResolver`, `RiskEvaluator`, `AuditLogger`, `SessionRegistry`, `DeviceTrust`, `BotProtection`, `RateLimiter`, `Clock` (PSR-20). |
| **Tenancy** | `CurrentTenant` + `BelongsToTenant` trait — per-tenant isolation. |
| **Config** | `php artisan rebel:validate-config` — fail-fast in CI. |

## When to use it directly

- You're building **another Rebel package** or a custom integration and need the shared contracts.
- You want **GDPR-safe keyed hashing** or the **assurance model** in your own code, without the rest of the suite.
- You're **swapping an implementation** (audit sink, risk engine, session registry) by binding your own contract.

---

## Worked example — the central security rule

```php
use Padosoft\Rebel\Core\Assurance\Aal;
use Padosoft\Rebel\Core\Assurance\AssuranceLevel;

$emailOtp = new AssuranceLevel(Aal::Aal1, phishingResistant: false, amr: ['otp', 'email']);
$passkey  = new AssuranceLevel(Aal::Aal2, phishingResistant: true,  amr: ['webauthn']);

// An action that requires AAL2 phishing-resistant:
$emailOtp->satisfies(Aal::Aal2, requirePhishingResistant: true); // false ← email-OTP is not enough
$passkey->satisfies(Aal::Aal2, requirePhishingResistant: true);  // true
```

```php
// Audit with automatic secret redaction — the OTP never reaches the database.
use Padosoft\Rebel\Core\Audit\AuditEvent;
use Padosoft\Rebel\Core\Audit\AuthEventType;
use Padosoft\Rebel\Core\Contracts\AuditLogger;

app(AuditLogger::class)->record(new AuditEvent(
    type: AuthEventType::EmailOtpVerified->value,
    guard: 'customers',
    identifierHmac: $h->hash, keyVersion: $h->keyVersion,
    purpose: 'customer-login',
    aal: Aal::Aal1, amr: ['otp', 'email'],
    metadata: ['otp' => '123456'], // ← stored as "[REDACTED]"
));
```

## How to extend it

Most extension happens by **binding your own implementation of a core contract** in the container —
swap the `AuditLogger` to ship events to a SIEM, decorate it (`ContextEnrichingAuditLogger`) or queue
it (`QueuedAuditLogger`); implement `RiskEvaluator`, `SessionRegistry`, `DeviceTrust`, `TokenIssuer`,
etc. Each ships a sane default and is meant to be overridden per app.

## Why the core, vs. the alternatives

There is no drop-in package that gives you a shared auth **core** with first-class NIST assurance,
keyed hashing with rotation and built-in audit redaction. See the full breakdown in
**[Why Rebel → Matrix 3](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Assurance\Aal.php`
- `src\Assurance\AssuranceLevel.php`
- `src\Audit\AuditEvent.php`
- `src\Audit\AuthEventType.php`
- `src\Audit\ContextEnrichingAuditLogger.php`
- `src\Audit\DatabaseAuditLogger.php`
- `src\Audit\QueuedAuditLogger.php`
- `src\Audit\RecordAuditEventJob.php`
- `src\Auth\LoginResult.php`
- `src\Auth\TokenPair.php`
- `src\Clock\FakeClock.php`
- `src\Clock\SystemClock.php`
- `src\Concerns\BelongsToTenant.php`
- `src\Config\CoreConfigValidator.php`
- `src\Console\ValidateConfigCommand.php`
- `src\Context\DeviceContext.php`
- `src\Context\SecurityContext.php`
- `src\Context\TenantContext.php`
- `src\Contracts\AuditLogger.php`
- `src\Contracts\BotProtection.php`
- `src\Contracts\ConfigValidator.php`
- `src\Contracts\DeviceTrust.php`
- `src\Contracts\KeyedHasher.php`
- `src\Contracts\RateLimiter.php`
- `src\Contracts\RiskEvaluator.php`
- `src\Contracts\SessionRegistry.php`
- `src\Contracts\SubjectResolver.php`
- `src\Contracts\TenantResolver.php`
- `src\Contracts\TokenIssuer.php`
- `src\Hashing\HashedValue.php`
- `src\Hashing\HmacKeyedHasher.php`
- `src\Identifiers\AuthIdentifier.php`
- `src\Identifiers\EmailIdentifier.php`
- `src\Identifiers\GenericIdentifier.php`
- `src\Identifiers\PhoneIdentifier.php`

### Service provider

- `src\RebelCoreServiceProvider.php`

### Contracts

- `src\Contracts\AuditLogger.php`
- `src\Contracts\BotProtection.php`
- `src\Contracts\ConfigValidator.php`
- `src\Contracts\DeviceTrust.php`
- `src\Contracts\KeyedHasher.php`
- `src\Contracts\RateLimiter.php`
- `src\Contracts\RiskEvaluator.php`
- `src\Contracts\SessionRegistry.php`
- `src\Contracts\SubjectResolver.php`
- `src\Contracts\TenantResolver.php`
- `src\Contracts\TokenIssuer.php`

### Models

- `src\Models\RebelAuthEvent.php`

### Config

- `config\rebel-core.php`

### Migrations

- `database\migrations\create_rebel_auth_events_table.php`

### Commands

- `src\Console\ValidateConfigCommand.php`

### Composer requirements

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `php` | `^8.3` |
| `psr/clock` | `^1.0` |
| `spatie/laravel-package-tools` | `^1.92` |

### Development requirements

| Dependency | Constraint |
|---|---|
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

### Test & verification surface

- `tests\Feature\AuditDispatchTest.php`
- `tests\Feature\BelongsToTenantTest.php`
- `tests\Feature\DatabaseAuditLoggerTest.php`
- `tests\Feature\RebelAuthEventModelTest.php`
- `tests\Feature\ValidateConfigCommandTest.php`
- `tests\Unit\Assurance\AssuranceLevelTest.php`
- `tests\Unit\Audit\AuditEventTest.php`
- `tests\Unit\Auth\LoginResultTest.php`
- `tests\Unit\Clock\FakeClockTest.php`
- `tests\Unit\Context\SecurityContextTest.php`
- `tests\Unit\Context\TenantContextTest.php`
- `tests\Unit\Hashing\HmacKeyedHasherTest.php`
- `tests\Unit\Identifiers\EmailIdentifierTest.php`
- `tests\Unit\Identifiers\GenericIdentifierTest.php`
- `tests\Unit\Identifiers\PhoneIdentifierTest.php`
- `tests\Unit\Risk\RiskAssessmentTest.php`
- `tests\Unit\Support\RedactorTest.php`

::: callout warning
File lists are a **source map** for maintainers and auditors — not an installation recipe. Don't copy
internal test-only classes into an application.
:::

::: callout tip
Next: see how the core fits the suite in the **[Package Map](/ecosystem/package-map)**, or read the
**[Configuration reference](/reference/configuration)** for every `config/rebel-core.php` key.
:::
