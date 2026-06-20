---
title: laravel-rebel-channel-vonage
description: The Vonage provider for Laravel Rebel Channels — phone verification via Vonage Verify (SMS, voice), plain SMS delivery, and signed delivery-receipt webhooks.
---

# laravel-rebel-channel-vonage

[GitHub repository](https://github.com/padosoft/laravel-rebel-channel-vonage) · Composer: `padosoft/laravel-rebel-channel-vonage` · MIT

> **Vonage, plugged into Rebel.** Drop this in behind `-channels` and Vonage Verify becomes one of
> your verification providers — SMS and voice — with plain SMS delivery and signed delivery-receipt
> webhooks normalized into the Rebel audit trail. Interchangeable with the Twilio and Bird providers.

::: callout info
This is a **provider package**. It implements the `-channels` contracts; the routing, fallback,
cooldown, rate limiting and fraud defense live in `padosoft/laravel-rebel-channels`. Install that too.
:::

---

## What it is

A concrete Vonage implementation of the Rebel Channels provider contract. It wraps Vonage Verify and
Vonage SMS behind a `VonageGateway`, exposes a `VonageVerifyProvider` that the `-channels` router can
select, and ships a webhook controller that ingests Vonage's delivery receipts — with signature
validation so forged receipts are rejected.

- **`VonageVerifyProvider`** — the provider the `VerificationRouter` routes to.
- **`VonageGateway`** / **`RestVonageGateway`** — the contract and its REST-backed implementation.
- **`VonageStatusController`** — receives signed delivery-receipt webhooks.
- **`VonageSignatureValidator`** — verifies the Vonage signature on every inbound webhook.

## The problem it solves

Talking to Vonage directly couples your auth flow to Vonage's API, its delivery-receipt format and its
signature scheme — and makes switching or adding a fallback vendor a rewrite. This package isolates all
of that behind the Rebel Channels contracts, so Vonage is just *one configured provider*. Delivery
receipts, cost and country are normalized and reported into the Rebel audit trail via the core
`AuditLogger`, exactly like every other provider.

---

## What you get

| Area | What you get |
|---|---|
| **Verification** | `VonageVerifyProvider` — phone verification via **Vonage Verify** (SMS, voice). |
| **Gateway** | `VonageGateway` + `RestVonageGateway` — typed boundary over the Vonage API, including plain **SMS delivery**. |
| **Delivery webhooks** | `VonageStatusController` — ingests Vonage's **signed delivery-receipt** callbacks. |
| **Webhook security** | `VonageSignatureValidator` — rejects receipts without a valid Vonage signature. |
| **Test double** | `FakeVonageGateway` — exercise verification and delivery flows without calling Vonage. |

## When to use it

- You already use **Vonage** (or want its SMS/voice coverage) and want it behind Rebel's routing and
  fraud defenses.
- You need **signed, verifiable delivery-receipt webhooks** that feed the Rebel audit trail.
- You want Vonage as a **primary or fallback** provider that you can swap without touching call sites.

---

## Worked example

```bash
composer require padosoft/laravel-rebel-channels
composer require padosoft/laravel-rebel-channel-vonage
php artisan vendor:publish
```

Point your Vonage delivery-receipt webhook at the route exposed by `VonageStatusController`; every
receipt is checked by `VonageSignatureValidator` before it is trusted. Remember: a Vonage delivery
receipt means the message was *sent/delivered* — it is **not** authentication success. Assurance is
decided only when the user returns a valid code, and OTPs are never written to the audit trail.

## How it fits

This package sits below `padosoft/laravel-rebel-channels` (whose router selects it) and above the
Vonage API. It depends on `laravel-rebel-core` for the audit and hashing vocabulary, so its telemetry
lands in the same `rebel_auth_events` stream as every other channel.

A raw Vonage integration gives you delivery but no cross-vendor fallback, shared rate limiting or IRSF
defense — those come from `-channels`. See **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Contracts\VonageGateway.php`
- `src\Gateway\RestVonageGateway.php`
- `src\Http\Controllers\VonageStatusController.php`
- `src\Http\VonageSignatureValidator.php`
- `src\Testing\FakeVonageGateway.php`
- `src\Verification\VonageVerifyProvider.php`
- `src\RebelVonageServiceProvider.php`

### Service providers

- `src\Verification\VonageVerifyProvider.php`
- `src\RebelVonageServiceProvider.php`

### Services and managers

- `src\RebelVonageServiceProvider.php`

### Contracts

- `src\Contracts\VonageGateway.php`

### Controllers

- `src\Http\Controllers\VonageStatusController.php`

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-channel-vonage.php`

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
| `padosoft/laravel-rebel-channels` | `^0.1` |
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

::: collapsible "Problem: keep laravel-rebel-channel-vonage replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Test and verification surface

- `tests\Feature\ChannelsIntegrationTest.php`
- `tests\Feature\RestVonageGatewayTest.php`
- `tests\Feature\VonageStatusWebhookTest.php`
- `tests\Feature\VonageVerifyProviderTest.php`
- `tests\Live\VonageVerifyLiveTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
