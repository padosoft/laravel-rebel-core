---
title: laravel-rebel-channel-twilio
description: The Twilio provider for Laravel Rebel Channels — phone verification via Twilio Verify (SMS, WhatsApp, voice), message delivery, and signed delivery-status webhooks.
---

# laravel-rebel-channel-twilio

[GitHub repository](https://github.com/padosoft/laravel-rebel-channel-twilio) · Composer: `padosoft/laravel-rebel-channel-twilio` · MIT

> **Twilio, plugged into Rebel.** Drop this in behind `-channels` and Twilio Verify becomes one of
> your verification providers — SMS, WhatsApp and voice — with delivery and signed status webhooks
> normalized into the Rebel audit trail. No vendor lock-in: it's interchangeable with the Vonage and
> Bird providers.

::: callout info
This is a **provider package**. It implements the `-channels` contracts; the routing, fallback,
cooldown, rate limiting and fraud defense live in `padosoft/laravel-rebel-channels`. Install that too.
:::

---

## What it is

A concrete Twilio implementation of the Rebel Channels provider contract. It wraps Twilio Verify and
the Twilio messaging API behind a `TwilioVerifyGateway`, exposes a `TwilioVerifyProvider` that the
`-channels` router can select, and ships a webhook controller that ingests Twilio's delivery-status
callbacks — with signature validation so forged callbacks are rejected.

- **`TwilioVerifyProvider`** — the provider the `VerificationRouter` routes to.
- **`TwilioVerifyGateway`** / **`RestTwilioVerifyGateway`** — the contract and its REST-backed
  implementation over Twilio's SDK.
- **`TwilioStatusController`** — receives signed delivery-status webhooks.
- **`TwilioSignatureValidator`** — verifies the Twilio signature on every inbound webhook.

## The problem it solves

Talking to Twilio directly couples your auth flow to Twilio's SDK, its callback format and its
signature scheme — and makes switching or adding a fallback vendor a rewrite. This package isolates
all of that behind the Rebel Channels contracts, so Twilio is just *one configured provider*. Delivery
receipts, cost and country are normalized and reported into the Rebel audit trail via the core
`AuditLogger`, exactly like every other provider.

---

## What you get

| Area | What you get |
|---|---|
| **Verification** | `TwilioVerifyProvider` — phone verification via **Twilio Verify** (SMS, WhatsApp, voice). |
| **Gateway** | `TwilioVerifyGateway` + `RestTwilioVerifyGateway` — typed boundary over the Twilio SDK. |
| **Delivery webhooks** | `TwilioStatusController` — ingests Twilio's **signed delivery-status** callbacks. |
| **Webhook security** | `TwilioSignatureValidator` — rejects callbacks without a valid Twilio signature. |
| **Test double** | `FakeTwilioVerifyGateway` — exercise verification flows without calling Twilio. |

## When to use it

- You already use **Twilio** (or want its global SMS/WhatsApp/voice reach) and want it behind Rebel's
  routing and fraud defenses.
- You need **signed, verifiable delivery-status webhooks** that feed the Rebel audit trail.
- You want Twilio as a **primary or fallback** provider that you can swap without touching call sites.

---

## Worked example

```bash
composer require padosoft/laravel-rebel-channels
composer require padosoft/laravel-rebel-channel-twilio
php artisan vendor:publish
```

Point your Twilio delivery-status webhook at the route exposed by `TwilioStatusController`; every
callback is checked by `TwilioSignatureValidator` before it is trusted. Remember: a Twilio delivery
status means the message was *sent/delivered* — it is **not** authentication success. Assurance is
decided only when the user returns a valid code, and OTPs are never written to the audit trail.

## How it fits

This package sits below `padosoft/laravel-rebel-channels` (whose router selects it) and above the
Twilio SDK. It depends on `laravel-rebel-core` for the audit and hashing vocabulary, so its telemetry
lands in the same `rebel_auth_events` stream as every other channel.

A raw Twilio SDK integration gives you delivery but no cross-vendor fallback, shared rate limiting or
IRSF defense — those come from `-channels`. See **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Contracts\TwilioVerifyGateway.php`
- `src\Gateway\RestTwilioVerifyGateway.php`
- `src\Http\Controllers\TwilioStatusController.php`
- `src\Http\TwilioSignatureValidator.php`
- `src\Testing\FakeTwilioVerifyGateway.php`
- `src\Verification\TwilioVerifyProvider.php`
- `src\RebelTwilioServiceProvider.php`

### Service providers

- `src\Verification\TwilioVerifyProvider.php`
- `src\RebelTwilioServiceProvider.php`

### Services and managers

- `src\RebelTwilioServiceProvider.php`

### Contracts

- `src\Contracts\TwilioVerifyGateway.php`

### Controllers

- `src\Http\Controllers\TwilioStatusController.php`

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-channel-twilio.php`

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
| `twilio/sdk` | `^8.3` |

## Development requirements

| Dependency | Constraint |
|---|---|
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

## ADR

::: collapsible "Problem: keep laravel-rebel-channel-twilio replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Test and verification surface

- `tests\Feature\ChannelsIntegrationTest.php`
- `tests\Feature\TwilioStatusWebhookTest.php`
- `tests\Feature\TwilioVerifyProviderTest.php`
- `tests\Live\TwilioVerifyLiveTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
