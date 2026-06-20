---
title: laravel-rebel-bridge-passkeys
description: Bridges spatie/laravel-passkeys into Rebel's step-up registry as a phishing-resistant AAL3 WebAuthn driver — the strongest factor in the suite, fully audited.
---

# laravel-rebel-bridge-passkeys

[GitHub repository](https://github.com/padosoft/laravel-rebel-bridge-passkeys) · Composer: `padosoft/laravel-rebel-bridge-passkeys` · MIT

> **The strongest factor you can offer.** Passkeys are the only NIST-recognized phishing-resistant credential — this bridge turns spatie/laravel-passkeys into an AAL3 Rebel step-up driver that no phishing kit or replayed code can defeat.

## What it is

A focused bridge that registers a single WebAuthn step-up driver (`PasskeysStepUpDriver`) backed by spatie/laravel-passkeys. It issues a passkey challenge through the `PasskeyChallenger` contract, verifies the assertion, and reports a phishing-resistant AAL3 outcome into Rebel's assurance model and audit trail. The challenge mechanics stay with spatie; the bridge only translates the result into Rebel's language.

## The problem it solves

Most "2FA" — SMS codes, email OTPs, even authenticator apps — can be phished or relay-attacked, because the user can be tricked into handing the secret to an attacker. Passkeys can't: the credential is cryptographically bound to your domain and never leaves the device. But spatie/laravel-passkeys, on its own, has no shared assurance grade and no cross-package audit trail. This bridge gives it both, so a passkey counts as the AAL3 ceiling everywhere in your app.

## What you get

| Capability | What it does |
|---|---|
| **Passkey step-up driver** | `PasskeysStepUpDriver` exposes WebAuthn assertions as a Rebel step-up factor at phishing-resistant AAL3. |
| **Challenge abstraction** | `PasskeyChallenger` contract with `SpatiePasskeyChallenger` as the default spatie-backed implementation. |
| **Phishing resistance** | The only factor in the suite that satisfies `requirePhishingResistant: true`. |
| **Full audit telemetry** | Every challenge outcome is recorded through the core audit trail — never the credential. |
| **Test double** | `FakePasskeyChallenger` for deterministic challenge/verify tests. |

## When to use it

- You want the **highest assurance** Rebel can offer — gate admin, payments or destructive actions behind it.
- You need a **phishing-resistant** factor to satisfy AAL3 step-up requirements.
- You already use **spatie/laravel-passkeys** and want it graded and audited inside Rebel.
- You're offering users a passwordless, replay-proof second factor.

## Worked example

```bash
composer require padosoft/laravel-rebel-bridge-passkeys
php artisan vendor:publish
```

The bridge auto-registers `PasskeysStepUpDriver`; `config/rebel-bridge-passkeys.php` exposes its options. Bind your own `PasskeyChallenger` to customize the WebAuthn ceremony, or swap in `FakePasskeyChallenger` for tests.

## How it fits

This package wraps **spatie/laravel-passkeys** (the upstream WebAuthn implementation) and registers it with **laravel-rebel-step-up** (the step-up consumer). It maps the assertion result onto the AAL/AMR model and audit contract from **laravel-rebel-core**, marking the only `phishingResistant: true`, AAL3 factor — so it sits at the top of the same ladder where TOTP and OTP bridges sit lower.

A standalone passkey package authenticates; this one grades that authentication AAL3-phishing-resistant and audits it next to every other factor. See **[Why Rebel](/ecosystem/why-rebel)**.

## Reference

### Runtime files

- `src\Challengers\SpatiePasskeyChallenger.php`
- `src\Contracts\PasskeyChallenger.php`
- `src\Drivers\PasskeysStepUpDriver.php`
- `src\Testing\FakePasskeyChallenger.php`
- `src\RebelPasskeysBridgeServiceProvider.php`

### Service providers

- `src\RebelPasskeysBridgeServiceProvider.php`

### Services and managers

- `src\RebelPasskeysBridgeServiceProvider.php`

### Contracts

- `src\Contracts\PasskeyChallenger.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bridge-passkeys.php`

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
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |
| `spatie/laravel-passkeys` | `^1.0` |

### ADR

::: collapsible "Problem: keep laravel-rebel-bridge-passkeys replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test and verification surface

- `tests\Feature\PasskeysDriverTest.php`
- `tests\Fixtures\User.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
