---
title: laravel-rebel-bridge-fortify
description: Turns Laravel Fortify into Rebel step-up drivers — password-confirm, passkey and TOTP — maps Fortify events into the audit trail and enables passkey-first login.
---

# laravel-rebel-bridge-fortify

[GitHub repository](https://github.com/padosoft/laravel-rebel-bridge-fortify) · Composer: `padosoft/laravel-rebel-bridge-fortify` · MIT

> **Fortify, promoted to a Rebel control plane.** Your existing Fortify password-confirm, passkey and TOTP flows become first-class step-up drivers — graded on the NIST assurance model and fully audited — without rewriting a single Fortify line.

## What it is

A thin bridge that wires Laravel Fortify into the Rebel step-up registry. It registers three drivers (`PasswordConfirmStepUpDriver`, `PasskeyConfirmStepUpDriver`, `TotpStepUpDriver`), subscribes to Fortify's events and replays their security-significant outcomes into the Rebel audit trail, and ships an opt-in `PasskeyFirstLogin` so users can start a session with a passkey. It does **not** reimplement Fortify — Fortify keeps owning the screens and the credential store.

## The problem it solves

Fortify gives you the mechanics of password confirmation, two-factor and passkeys, but it has no notion of *how strong* a confirmation was, no shared audit trail, and no way for the rest of your app to ask "has this user re-proven themselves strongly enough for this action?" The bridge fills that gap: every Fortify outcome is mapped to an AAL/AMR level and recorded once, in the same `rebel_auth_events` stream every other Rebel package reads.

## What you get

| Capability | What it does |
|---|---|
| **Password-confirm driver** | `PasswordConfirmStepUpDriver` exposes Fortify's `password.confirm` as a Rebel step-up factor (`fortify_password_confirm`, **web-only** — mobile uses a token-native step-up instead). |
| **Passkey driver** | `PasskeyConfirmStepUpDriver` re-confirms with a WebAuthn passkey for phishing-resistant assurance, via the `PasskeyAuthenticator` / `PasskeyConfirmer` contracts. |
| **TOTP driver** | `TotpStepUpDriver` accepts Fortify's authenticator-app code as an AAL2 factor. |
| **Event mapping** | `FortifyEventSubscriber` translates Fortify events into Rebel audit events — never logging the secret. |
| **Passkey-first login** | `PasskeyFirstLogin` lets users open a session with a passkey instead of a password. |
| **Test doubles** | `FakePasskeyAuthenticator` / `FakePasskeyConfirmer` for deterministic tests. |

## When to use it

- You already run **Laravel Fortify** and want its flows graded and audited by Rebel instead of treated as opaque booleans.
- You want **passkey-first login** on top of Fortify without building the ceremony yourself.
- You need Fortify's password-confirm and TOTP to count as proper **step-up factors** for sensitive actions.
- You're standardizing audit telemetry across mixed auth providers and want Fortify in the same stream.

## Worked example

```bash
composer require padosoft/laravel-rebel-bridge-fortify
php artisan vendor:publish
```

The bridge auto-registers its drivers; `config/rebel-bridge-fortify.php` lets you tune which factors are exposed. Bind your own `PasskeyAuthenticator` / `PasskeyConfirmer` to plug in a custom WebAuthn stack, or use the shipped fakes in tests.

## How it fits

This package sits between **laravel-fortify** (the upstream credential and ceremony layer) and **laravel-rebel-step-up** (the consumer that runs step-up challenges). It maps Fortify outcomes into the AAL/AMR assurance model and audit contract defined by **laravel-rebel-core**, so a Fortify password-confirm and a passkey from any other bridge are compared on the same scale and land in the same audit trail.

A drop-in 2FA package gives you a screen; this gives you a graded, audited, swappable step-up factor. See **[Why Rebel](/ecosystem/why-rebel)**.

## Reference

### Runtime files

- `src\Contracts\PasskeyAuthenticator.php`
- `src\Contracts\PasskeyConfirmer.php`
- `src\Drivers\PasskeyConfirmStepUpDriver.php`
- `src\Drivers\PasswordConfirmStepUpDriver.php`
- `src\Drivers\TotpStepUpDriver.php`
- `src\Listeners\FortifyEventSubscriber.php`
- `src\Support\FortifyBridge.php`
- `src\Testing\FakePasskeyAuthenticator.php`
- `src\Testing\FakePasskeyConfirmer.php`
- `src\PasskeyFirstLogin.php`
- `src\RebelFortifyBridgeServiceProvider.php`

### Service providers

- `src\RebelFortifyBridgeServiceProvider.php`

### Services and managers

- `src\RebelFortifyBridgeServiceProvider.php`

### Contracts

- `src\Contracts\PasskeyAuthenticator.php`
- `src\Contracts\PasskeyConfirmer.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bridge-fortify.php`

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
| `laravel/fortify` | `^1.21` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

### ADR

::: collapsible "Problem: keep laravel-rebel-bridge-fortify replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test and verification surface

- `tests\Feature\EventMappingTest.php`
- `tests\Feature\PasskeyConfirmDriverTest.php`
- `tests\Feature\PasskeyFirstLoginTest.php`
- `tests\Feature\PasswordConfirmDriverTest.php`
- `tests\Feature\StepUpIntegrationTest.php`
- `tests\Feature\TotpDriverTest.php`
- `tests\Fixtures\User.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
