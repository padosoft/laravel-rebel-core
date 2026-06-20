---
title: laravel-rebel-channels
description: The provider-agnostic backbone for SMS, WhatsApp and voice verification — routing with automatic provider fallback, cooldown, multi-dimensional rate limiting and anti toll-fraud/IRSF defenses.
---

# laravel-rebel-channels

[GitHub repository](https://github.com/padosoft/laravel-rebel-channels) · Composer: `padosoft/laravel-rebel-channels` · MIT

> **One verification API, every provider behind it.** `-channels` is the abstraction that routes
> phone verification and message delivery across SMS, WhatsApp and voice — with provider fallback,
> cooldown, multi-dimensional rate limiting and anti toll-fraud defenses baked in. Swap or add a
> provider and your call sites never change.

::: callout info
This package defines the *contract and the routing*. The actual sending happens in a provider
package (`-channel-twilio`, `-channel-vonage`, `-channel-bird`) that plugs in behind it. Install at
least one provider to deliver anything.
:::

---

## What it is

`-channels` is the seam between "I need to verify this phone number" and "which vendor actually sent
the SMS." It owns the decisions that should *never* be re-implemented per provider:

- a **`VerificationRouter`** that picks a provider, applies cooldown and rate limits, and falls back
  to the next provider when one fails,
- a **`FraudGuard`** that defends against toll-fraud / IRSF abuse before a single message is billed,
- a **registry** of pluggable providers and delivery channels (`ProviderRegistry`,
  `DeliveryChannelRegistry`),
- typed **results** (`VerificationResult`, `DeliveryResult`) and **enums** (`Channel`,
  `VerificationStatus`, `DeliveryStatus`) so every provider speaks the same language.

## The problem it solves

A single-provider SMS SDK ties your whole auth flow to one vendor's uptime, pricing and country
coverage — and gives you nothing against international revenue-share fraud, where an attacker pumps
OTPs to premium-rate ranges and you pay the bill. Switching providers later means rewriting call
sites. `-channels` inverts that: routing, fallback, cooldown, rate limiting and fraud defense live
*once*, above the provider, and vendors become interchangeable plug-ins.

---

## What you get

| Area | What you get |
|---|---|
| **Routing & fallback** | `VerificationRouter` — selects a provider, retries with the **next** provider on failure, enforces cooldown. |
| **Rate limiting** | `CacheRateLimiter` — multi-dimensional limits to throttle abuse without blocking legitimate users. |
| **Fraud defense** | `FraudGuard` + `FraudDecision` — anti toll-fraud / IRSF gating before any message is billed. |
| **Provider registry** | `ProviderRegistry`, `DeliveryChannelRegistry` — register and resolve pluggable providers/channels. |
| **Contracts** | `VerificationProvider`, `MessageDeliveryChannel` — the two interfaces every provider implements. |
| **Typed results** | `VerificationResult`, `DeliveryResult` — never confuse a *delivery* outcome with *authentication* success. |
| **Enums** | `Channel` (SMS \| WhatsApp \| voice), `VerificationStatus`, `DeliveryStatus`. |
| **Test doubles** | `FakeVerificationProvider`, `FakeMessageDeliveryChannel` — verify your flows without sending a real OTP. |

## When to use it

- You send **phone/WhatsApp/voice OTPs** and want vendor independence with automatic fallback.
- You need **toll-fraud / IRSF protection** and rate limiting in front of every provider, not bolted
  on per vendor.
- You're **writing a provider package** and need the `VerificationProvider` / `MessageDeliveryChannel`
  contracts to plug into.

---

## Worked example

```bash
composer require padosoft/laravel-rebel-channels
# plus at least one provider, e.g.:
composer require padosoft/laravel-rebel-channel-twilio
php artisan vendor:publish
```

A delivery result is **not** an authentication result. `-channels` keeps the two separate: a sent SMS
means the message went out; assurance is decided only when the user returns a valid code. Telemetry
for every send, delivery receipt, cost and country flows into the Rebel audit trail via the core
`AuditLogger` — never logging the OTP itself.

## How it fits

`-channels` sits on top of `laravel-rebel-core` (it speaks the core's contracts and feeds the audit
trail) and underneath the provider packages that implement actual delivery. The host app talks to the
router; the router talks to whichever provider is configured — and to the next one if that fails.

One single-provider SMS SDK can't give you cross-vendor fallback, shared rate limiting and IRSF
defense in one place — see **[Why Rebel → "Rebel channels vs. a single-provider SMS SDK"](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Contracts\MessageDeliveryChannel.php`
- `src\Contracts\VerificationProvider.php`
- `src\Enums\Channel.php`
- `src\Enums\DeliveryStatus.php`
- `src\Enums\VerificationStatus.php`
- `src\Fraud\FraudDecision.php`
- `src\Fraud\FraudGuard.php`
- `src\Results\DeliveryResult.php`
- `src\Results\VerificationResult.php`
- `src\Routing\DeliveryChannelRegistry.php`
- `src\Routing\ProviderRegistry.php`
- `src\Routing\VerificationRouter.php`
- `src\Support\CacheRateLimiter.php`
- `src\Support\NullBotProtection.php`
- `src\Testing\FakeMessageDeliveryChannel.php`
- `src\Testing\FakeVerificationProvider.php`
- `src\RebelChannelsServiceProvider.php`

### Service providers

- `src\Contracts\VerificationProvider.php`
- `src\Routing\ProviderRegistry.php`
- `src\Testing\FakeVerificationProvider.php`
- `src\RebelChannelsServiceProvider.php`

### Services and managers

- `src\Fraud\FraudGuard.php`
- `src\Routing\DeliveryChannelRegistry.php`
- `src\Routing\ProviderRegistry.php`
- `src\RebelChannelsServiceProvider.php`

### Contracts

- `src\Contracts\MessageDeliveryChannel.php`
- `src\Contracts\VerificationProvider.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-channels.php`

### Migrations

None detected in the package tree.

### Routes

None detected in the package tree.

### Commands

None detected in the package tree.

## Composer requirements

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

## Development requirements

| Dependency | Constraint |
|---|---|
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

## ADR

::: collapsible "Problem: keep laravel-rebel-channels replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Test and verification surface

- `tests\Feature\DeliveryChannelRegistryTest.php`
- `tests\Feature\FraudGuardTest.php`
- `tests\Feature\VerificationRouterTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
