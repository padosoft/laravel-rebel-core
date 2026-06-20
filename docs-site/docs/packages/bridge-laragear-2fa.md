---
title: laravel-rebel-bridge-laragear-2fa
description: Bridges laragear/two-factor into Rebel as an AAL2 TOTP step-up driver — authenticator apps and recovery codes, graded and fully audited.
---

# laravel-rebel-bridge-laragear-2fa

[GitHub repository](https://github.com/padosoft/laravel-rebel-bridge-laragear-2fa) · Composer: `padosoft/laravel-rebel-bridge-laragear-2fa` · MIT

> **Authenticator-app 2FA, graded and audited.** Bring laragear/two-factor's TOTP and recovery codes into Rebel as a proper AAL2 step-up factor — no secret ever touches the audit log.

## What it is

A bridge that registers laragear/two-factor as a Rebel step-up driver (`LaragearTotpStepUpDriver`). It validates a time-based one-time code (or a recovery code) through the `TwoFactorValidator` contract — backed by `LaragearTwoFactorValidator` — and reports an AAL2 / AMR `otp` outcome into Rebel's assurance model and audit trail. laragear keeps owning the TOTP secrets and QR provisioning.

## The problem it solves

Authenticator apps are a solid second factor, but laragear/two-factor on its own treats verification as a boolean and keeps its own state. Your app can't ask "was this user TOTP-verified strongly enough, and when?", and the outcome never reaches a shared audit trail. The bridge maps each verification to an AAL2 grade, folds in recovery codes, and records every result once in `rebel_auth_events` — without ever logging the code.

## What you get

| Capability | What it does |
|---|---|
| **TOTP step-up driver** | `LaragearTotpStepUpDriver` exposes authenticator-app codes as an AAL2 (AMR `otp`) Rebel factor. |
| **Recovery codes** | Recovery-code redemption is integrated into the same step-up flow. |
| **Validator abstraction** | `TwoFactorValidator` contract with `LaragearTwoFactorValidator` as the default. |
| **Full audit telemetry** | Every verification and recovery-code use is recorded through the core audit trail — never the secret. |
| **Test double** | `FakeTwoFactorValidator` for deterministic tests. |

## When to use it

- You already use **laragear/two-factor** and want its TOTP graded and audited inside Rebel.
- You want **authenticator-app 2FA** as a step-up factor for sensitive actions, without SMS costs.
- You need **recovery codes** handled in the same audited step-up flow.
- You're consolidating mixed factors onto one assurance scale and one audit trail.

## When *not* to use it

TOTP is AAL2, not phishing-resistant — a user can be tricked into entering a code on a fake page. For the highest assurance, pair it with **laravel-rebel-bridge-passkeys** (AAL3).

## Worked example

```bash
composer require padosoft/laravel-rebel-bridge-laragear-2fa
php artisan vendor:publish
```

The bridge auto-registers `LaragearTotpStepUpDriver`; `config/rebel-bridge-laragear-2fa.php` exposes its options. Bind your own `TwoFactorValidator` to customize verification, or use `FakeTwoFactorValidator` in tests.

## How it fits

This package wraps **laragear/two-factor** (the upstream TOTP implementation) and registers it with **laravel-rebel-step-up** (the step-up consumer). It maps each verification onto the AAL/AMR model and audit contract from **laravel-rebel-core**, placing it at AAL2 — above email/SMS OTP equivalents in audit reasoning, below the phishing-resistant passkey bridge.

A standalone 2FA package verifies a code; this one grades it AAL2 and audits it alongside every other factor. See **[Why Rebel](/ecosystem/why-rebel)**.

## Reference

### Runtime files

- `src\Contracts\TwoFactorValidator.php`
- `src\Drivers\LaragearTotpStepUpDriver.php`
- `src\Support\LaragearBridge.php`
- `src\Support\LaragearTwoFactorValidator.php`
- `src\Testing\FakeTwoFactorValidator.php`
- `src\RebelLaragear2faBridgeServiceProvider.php`

### Service providers

- `src\RebelLaragear2faBridgeServiceProvider.php`

### Services and managers

- `src\RebelLaragear2faBridgeServiceProvider.php`

### Contracts

- `src\Contracts\TwoFactorValidator.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bridge-laragear-2fa.php`

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
| `laragear/two-factor` | `^4.0` |
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

### ADR

::: collapsible "Problem: keep laravel-rebel-bridge-laragear-2fa replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test and verification surface

- `tests\Feature\FakeTwoFactorValidatorTest.php`
- `tests\Feature\LaragearTotpDriverTest.php`
- `tests\Feature\ServiceProviderTest.php`
- `tests\Fixtures\User.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
