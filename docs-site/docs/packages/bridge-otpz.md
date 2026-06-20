---
title: laravel-rebel-bridge-otpz
description: Bridges benbjurstrom/otpz into Rebel as an email magic-code step-up driver (AAL2, AMR otp) — graded on the assurance model and fully audited.
---

# laravel-rebel-bridge-otpz

[GitHub repository](https://github.com/padosoft/laravel-rebel-bridge-otpz) · Composer: `padosoft/laravel-rebel-bridge-otpz` · MIT

> **Email magic codes as a first-class factor.** Bring benbjurstrom/otpz into Rebel as an AAL2 step-up driver — graded, swappable and fully audited, with the code never written to the log.

## What it is

A bridge that registers benbjurstrom/otpz as a Rebel step-up driver (`OtpzStepUpDriver`). It issues and verifies an email magic-code through the `OtpzBroker` contract — backed by `OtpzBrokerImpl` — and reports an AAL2 / AMR `otp` outcome into Rebel's assurance model and audit trail. otpz keeps owning code generation, delivery and expiry.

## The problem it solves

otpz is a clean way to send email magic codes, but on its own it has no shared assurance grade and no cross-package audit trail, and the code can easily end up in logs. The bridge maps each verification to an AAL2 grade, records every send and outcome once in `rebel_auth_events`, and routes the secret through the core redactor so it never leaks.

## What you get

| Capability | What it does |
|---|---|
| **Magic-code step-up driver** | `OtpzStepUpDriver` exposes email magic codes as an AAL2 (AMR `otp`) Rebel factor. |
| **Broker abstraction** | `OtpzBroker` contract with `OtpzBrokerImpl` as the default otpz-backed implementation. |
| **Full audit telemetry** | Every challenge and verification is recorded through the core audit trail — never the code. |
| **Test double** | `FakeOtpzBroker` for deterministic issue/verify tests. |

## When to use it

- You already use **benbjurstrom/otpz** and want it graded and audited inside Rebel.
- You want **email magic codes** as a low-friction step-up factor.
- You want a familiar email-based fallback alongside stronger factors, on one assurance scale.
- You're consolidating mixed providers into a single audit trail.

## When *not* to use it

Email magic codes are AAL2, not phishing-resistant — a code can be relayed to an attacker. For high-value actions, pair it with **laravel-rebel-bridge-passkeys** (AAL3).

## Worked example

```bash
composer require padosoft/laravel-rebel-bridge-otpz
php artisan vendor:publish
```

The bridge auto-registers `OtpzStepUpDriver`; `config/rebel-bridge-otpz.php` exposes its options. Bind your own `OtpzBroker` to customize issuing and delivery, or use `FakeOtpzBroker` in tests.

## How it fits

This package wraps **benbjurstrom/otpz** (the upstream email magic-code implementation) and registers it with **laravel-rebel-step-up** (the step-up consumer). It maps each verification onto the AAL/AMR model and audit contract from **laravel-rebel-core**, placing email magic codes at AAL2 (AMR `otp`) — below the phishing-resistant passkey bridge in audit reasoning.

A standalone OTP package emails a code; this one grades it AAL2, redacts the secret and audits it alongside every other factor. See **[Why Rebel](/ecosystem/why-rebel)**.

## Reference

### Runtime files

- `src\Contracts\OtpzBroker.php`
- `src\Drivers\OtpzStepUpDriver.php`
- `src\Testing\FakeOtpzBroker.php`
- `src\OtpzBrokerImpl.php`
- `src\RebelOtpzBridgeServiceProvider.php`

### Service providers

- `src\RebelOtpzBridgeServiceProvider.php`

### Services and managers

- `src\Contracts\OtpzBroker.php`
- `src\Testing\FakeOtpzBroker.php`
- `src\OtpzBrokerImpl.php`
- `src\RebelOtpzBridgeServiceProvider.php`

### Contracts

- `src\Contracts\OtpzBroker.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bridge-otpz.php`

### Migrations

None detected in the package tree.

### Routes

None detected in the package tree.

### Commands

None detected in the package tree.

### Composer requirements

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### Development requirements

| Dependency | Constraint |
|---|---|
| `benbjurstrom/otpz` | `^0.7.0` |
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

### ADR

::: collapsible "Problem: keep laravel-rebel-bridge-otpz replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test and verification surface

- `tests\Feature\OtpzDriverTest.php`
- `tests\Fixtures\User.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
