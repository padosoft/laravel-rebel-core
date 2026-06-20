---
title: laravel-rebel-recovery
description: High-assurance account recovery for Laravel Rebel — single-use, HMAC-hashed backup codes generated once at enrolment, with anti-ATO checks.
---

# laravel-rebel-recovery

[GitHub repository](https://github.com/padosoft/laravel-rebel-recovery) · Composer: `padosoft/laravel-rebel-recovery` · MIT

> **Recovery done right — a step *up*, not a side door.** Single-use, HMAC-hashed backup codes
> generated once at enrolment, each redeemable exactly once, gated by anti-ATO checks. The way back
> in is held to a *higher* assurance bar than logging in, not a lower one.

## What it is

The account-recovery package for the Rebel suite. At enrolment it generates a set of recovery
(backup) codes, shows them to the user once, and stores only their **HMAC hashes** — so the database
never holds a usable code. When a user is locked out, redeeming a valid code recovers the account,
but only after **anti-ATO checks**, and each code is **single-use**: spent once, it's dead.

## The problem it solves

Account recovery is where most authentication systems quietly undo all their hard work. "Email me a
reset link" turns a phishing-resistant login into a one-click takeover for anyone who controls the
inbox. Real recovery has to be a **step-up at higher assurance than login** — proof you held a secret
issued ahead of time — not a weaker bypass. And those secrets must never sit in cleartext: a leaked
backup-codes table is a master key to every account. This package implements recovery the careful
way: codes hashed with the core keyed hasher, redeemable once, and guarded by anti-ATO checks before
they count.

## What you get

- **Single-use recovery codes** — `RecoveryCodeGenerator` mints them once at enrolment; each redeems exactly once.
- **HMAC-hashed at rest** — codes are stored as keyed hashes (via core hashing); the plaintext is shown once and never persisted.
- **Anti-ATO checks** — recovery is treated as a high-assurance step-up, not an email-a-code shortcut.
- **A clean redemption path** — `RecoveryCodeManager` orchestrates generation, verification, and consumption.
- **Persistence + telemetry** — `RebelRecoveryCode` over `rebel_recovery_codes`, with outcomes auditable through the core vocabulary.

## When to use it

- You offer MFA and need a **safe fallback** when the primary factor (phone, passkey) is unavailable.
- You want recovery that **raises** the assurance bar instead of lowering it to "whoever owns the inbox."
- You need backup codes stored as **HMAC hashes**, single-use, with no cleartext on disk.

## Worked example

```bash
composer require padosoft/laravel-rebel-recovery
php artisan vendor:publish
php artisan migrate
```

Publishing and migrating creates the `rebel_recovery_codes` table. Drive enrolment and redemption
through `RecoveryCodeManager`: generate a batch at enrolment, surface the plaintext codes to the user
exactly once, and verify a submitted code at recovery time — the manager checks it against the stored
HMAC, runs the anti-ATO checks, and consumes the code so it can't be reused.

## How it fits

This package depends only on `padosoft/laravel-rebel-core`, leaning on its keyed hashing for the
stored codes and its audit vocabulary for the outcomes. It contributes one bounded capability —
high-assurance recovery — without redefining the suite's assurance model, which stays in the core.
Add it when you need a recovery factor; the rest of the suite is unaffected.

Recovery as a deliberate high-assurance step-up — single-use HMAC codes with anti-ATO checks, not a
"reset link" — is what generic auth packages get wrong. See [Why Rebel](/ecosystem/why-rebel).

---

## Reference

### Runtime files

- `src\Models\RebelRecoveryCode.php`
- `src\RebelRecoveryServiceProvider.php`
- `src\RecoveryCodeGenerator.php`
- `src\RecoveryCodeManager.php`

### Service providers

- `src\RebelRecoveryServiceProvider.php`

### Services and managers

- `src\RebelRecoveryServiceProvider.php`
- `src\RecoveryCodeManager.php`

### Contracts

None detected in the package tree.

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

- `src\Models\RebelRecoveryCode.php`

### Config

- `config\rebel-recovery.php`

### Migrations

- `database\migrations\create_rebel_recovery_codes_table.php`

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

### Architecture decisions

::: collapsible "Problem: keep laravel-rebel-recovery replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\RecoveryCodeManagerTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
