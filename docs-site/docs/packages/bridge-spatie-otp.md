---
title: laravel-rebel-bridge-spatie-otp
description: Bridges spatie/laravel-one-time-passwords into Rebel as an AAL2 email/SMS OTP step-up driver — graded, swappable and fully audited.
---

# laravel-rebel-bridge-spatie-otp

[GitHub repository](https://github.com/padosoft/laravel-rebel-bridge-spatie-otp) · Composer: `padosoft/laravel-rebel-bridge-spatie-otp` · MIT

> **Email and SMS one-time codes, done right.** Turn spatie/laravel-one-time-passwords into an AAL2 Rebel step-up factor — graded on the assurance model and fully audited, with the code never reaching the log.

## What it is

A bridge that registers spatie/laravel-one-time-passwords as a Rebel step-up driver (`SpatieOtpStepUpDriver`). It generates and verifies one-time passwords delivered over email or SMS through the `OneTimePasswordBroker` contract — backed by `SpatieOneTimePasswordBroker` — and reports an AAL2 / AMR `otp` outcome into Rebel's assurance model and audit trail. spatie keeps owning generation, storage and expiry.

## The problem it solves

Email and SMS OTP is the most familiar second factor, but spatie/laravel-one-time-passwords on its own has no shared assurance grade and no cross-package audit trail, and it's easy to accidentally log the code. The bridge maps each verification to an AAL2 grade, records every send and outcome once in `rebel_auth_events`, and routes the secret through the core redactor so it can never leak.

## What you get

| Capability | What it does |
|---|---|
| **OTP step-up driver** | `SpatieOtpStepUpDriver` exposes email/SMS one-time passwords as an AAL2 (AMR `otp`) Rebel factor. |
| **Broker abstraction** | `OneTimePasswordBroker` contract with `SpatieOneTimePasswordBroker` as the default. |
| **Full audit telemetry** | Every challenge and verification is recorded through the core audit trail — never the code. |
| **Test double** | `FakeOneTimePasswordBroker` for deterministic generate/verify tests. |

## When to use it

- You already use **spatie/laravel-one-time-passwords** and want it graded and audited inside Rebel.
- You need an **email or SMS OTP** step-up factor with the lowest user friction.
- You want a familiar fallback factor alongside stronger ones, on the same assurance scale.
- You're consolidating mixed providers into one audit trail.

## When *not* to use it

Email/SMS OTP is AAL2, not phishing-resistant — codes can be relayed or intercepted. For high-value actions, pair it with **laravel-rebel-bridge-passkeys** (AAL3).

## Worked example

```bash
composer require padosoft/laravel-rebel-bridge-spatie-otp
php artisan vendor:publish
```

The bridge auto-registers `SpatieOtpStepUpDriver`; `config/rebel-bridge-spatie-otp.php` exposes its options. Bind your own `OneTimePasswordBroker` to customize delivery, or use `FakeOneTimePasswordBroker` in tests.

## How it fits

This package wraps **spatie/laravel-one-time-passwords** (the upstream OTP implementation) and registers it with **laravel-rebel-step-up** (the step-up consumer). It maps each verification onto the AAL/AMR model and audit contract from **laravel-rebel-core**, placing email/SMS OTP at AAL2 — below the phishing-resistant passkey bridge in audit reasoning.

A standalone OTP package sends a code; this one grades it AAL2, redacts the secret and audits it alongside every other factor. See **[Why Rebel](/ecosystem/why-rebel)**.

## Reference

### Runtime files

- `src\Contracts\OneTimePasswordBroker.php`
- `src\Drivers\SpatieOtpStepUpDriver.php`
- `src\Support\SpatieOneTimePasswordBroker.php`
- `src\Testing\FakeOneTimePasswordBroker.php`
- `src\RebelSpatieOtpBridgeServiceProvider.php`

### Service providers

- `src\RebelSpatieOtpBridgeServiceProvider.php`

### Services and managers

- `src\Contracts\OneTimePasswordBroker.php`
- `src\Support\SpatieOneTimePasswordBroker.php`
- `src\Testing\FakeOneTimePasswordBroker.php`
- `src\RebelSpatieOtpBridgeServiceProvider.php`

### Contracts

- `src\Contracts\OneTimePasswordBroker.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bridge-spatie-otp.php`

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
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |
| `spatie/laravel-one-time-passwords` | `^1.0` |

### ADR

::: collapsible "Problem: keep laravel-rebel-bridge-spatie-otp replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test and verification surface

- `tests\Feature\SpatieOtpDriverTest.php`
- `tests\Fixtures\User.php`
- `tests\Fixtures\UserWithoutOtp.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
