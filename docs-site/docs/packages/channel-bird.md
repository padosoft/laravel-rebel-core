---
title: laravel-rebel-channel-bird
description: The Bird (formerly MessageBird) provider for Laravel Rebel Channels — phone verification via the Bird Verify API (SMS), plain SMS delivery, and signed delivery-status webhooks.
---

# laravel-rebel-channel-bird

[GitHub repository](https://github.com/padosoft/laravel-rebel-channel-bird) · Composer: `padosoft/laravel-rebel-channel-bird` · MIT

> **Bird, plugged into Rebel.** Drop this in behind `-channels` and the Bird Verify API (formerly
> MessageBird) becomes one of your verification providers — SMS verification and delivery, with signed
> delivery-status webhooks normalized into the Rebel audit trail. Interchangeable with the Twilio and
> Vonage providers.

::: callout info
This is a **provider package**. It implements the `-channels` contracts; the routing, fallback,
cooldown, rate limiting and fraud defense live in `padosoft/laravel-rebel-channels`. Install that too.
:::

---

## What it is

A concrete Bird implementation of the Rebel Channels provider contract. It wraps the Bird Verify API
and Bird SMS behind a `BirdGateway`, exposes a `BirdVerifyProvider` that the `-channels` router can
select, and ships a webhook controller that ingests Bird's delivery-status callbacks — with signature
validation so forged callbacks are rejected.

- **`BirdVerifyProvider`** — the provider the `VerificationRouter` routes to.
- **`BirdGateway`** / **`RestBirdGateway`** — the contract and its REST-backed implementation.
- **`BirdStatusController`** — receives signed delivery-status webhooks.
- **`BirdSignatureValidator`** — verifies the Bird signature on every inbound webhook.

## The problem it solves

Talking to Bird directly couples your auth flow to Bird's API, its callback format and its signature
scheme — and makes switching or adding a fallback vendor a rewrite. This package isolates all of that
behind the Rebel Channels contracts, so Bird is just *one configured provider*. Delivery statuses, cost
and country are normalized and reported into the Rebel audit trail via the core `AuditLogger`, exactly
like every other provider.

---

## What you get

| Area | What you get |
|---|---|
| **Verification** | `BirdVerifyProvider` — phone verification via the **Bird Verify API** (SMS). |
| **Gateway** | `BirdGateway` + `RestBirdGateway` — typed boundary over the Bird API, including plain **SMS delivery**. |
| **Delivery webhooks** | `BirdStatusController` — ingests Bird's **signed delivery-status** callbacks. |
| **Webhook security** | `BirdSignatureValidator` — rejects callbacks without a valid Bird signature. |
| **Test double** | `FakeBirdGateway` — exercise verification and delivery flows without calling Bird. |

## When to use it

- You already use **Bird** (formerly MessageBird) and want it behind Rebel's routing and fraud
  defenses.
- You need **signed, verifiable delivery-status webhooks** that feed the Rebel audit trail.
- You want Bird as a **primary or fallback** provider that you can swap without touching call sites.

---

## Worked example

```bash
composer require padosoft/laravel-rebel-channels
composer require padosoft/laravel-rebel-channel-bird
php artisan vendor:publish
```

Point your Bird delivery-status webhook at the route exposed by `BirdStatusController`; every callback
is checked by `BirdSignatureValidator` before it is trusted. Remember: a Bird delivery status means the
message was *sent/delivered* — it is **not** authentication success. Assurance is decided only when the
user returns a valid code, and OTPs are never written to the audit trail.

## How it fits

This package sits below `padosoft/laravel-rebel-channels` (whose router selects it) and above the Bird
API. It depends on `laravel-rebel-core` for the audit and hashing vocabulary, so its telemetry lands in
the same `rebel_auth_events` stream as every other channel.

A raw Bird integration gives you delivery but no cross-vendor fallback, shared rate limiting or IRSF
defense — those come from `-channels`. See **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Contracts\BirdGateway.php`
- `src\Gateway\RestBirdGateway.php`
- `src\Http\Controllers\BirdStatusController.php`
- `src\Http\BirdSignatureValidator.php`
- `src\Testing\FakeBirdGateway.php`
- `src\Verification\BirdVerifyProvider.php`
- `src\RebelBirdServiceProvider.php`

### Service providers

- `src\Verification\BirdVerifyProvider.php`
- `src\RebelBirdServiceProvider.php`

### Services and managers

- `src\RebelBirdServiceProvider.php`

### Contracts

- `src\Contracts\BirdGateway.php`

### Controllers

- `src\Http\Controllers\BirdStatusController.php`

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-channel-bird.php`

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

::: collapsible "Problem: keep laravel-rebel-channel-bird replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Test and verification surface

- `tests\Feature\BirdStatusWebhookTest.php`
- `tests\Feature\BirdVerifyProviderTest.php`
- `tests\Feature\ChannelsIntegrationTest.php`
- `tests\Feature\RestBirdGatewayTest.php`
- `tests\Live\BirdVerifyLiveTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
