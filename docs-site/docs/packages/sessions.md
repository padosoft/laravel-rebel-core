---
title: laravel-rebel-sessions
description: A device and session registry for Laravel Rebel — track sessions and devices, log out everywhere, and rotate refresh tokens with reuse detection.
---

# laravel-rebel-sessions

[GitHub repository](https://github.com/padosoft/laravel-rebel-sessions) · Composer: `padosoft/laravel-rebel-sessions` · MIT

> **Know every session, trust every device, kill any of them.** A registry that tracks sessions and
> devices, powers "log out everywhere", and rotates refresh tokens with **reuse detection** — so a
> stolen token gets the whole family revoked, not replayed.

## What it is

The device and session registry for the Rebel suite. It implements the core `SessionRegistry` and
`DeviceTrust` contracts with a database-backed engine: every active session and known device is a
row you can list, trust, or revoke. It's the package that turns "the user is logged in" into "the
user is logged in on these three devices, and I can end any of them right now."

## The problem it solves

Laravel's stock session handling answers "is this request authenticated?" but not "where else is
this account logged in, and can I trust this device?" When a phone is lost or a token leaks, you need
to see every active session and end them — and you need refresh-token rotation that **detects reuse**:
if an old refresh token reappears, that's a signal it was stolen, and the safe move is to revoke the
whole token family rather than hand out a fresh one. Building that correctly is subtle and easy to
get wrong; this package ships it as a tested, auditable default.

## What you get

| Capability | What it does |
|---|---|
| **Session registry** | `DatabaseSessionRegistry` over `rebel_sessions` — list, track, and revoke active sessions. |
| **Device trust** | `DatabaseDeviceTrust` over `rebel_devices` — remember and trust known devices. |
| **Log out everywhere** | Revoke every session for an account in one operation. |
| **Refresh-token rotation** | Rotate refresh tokens with **reuse detection** — a replayed token revokes the family. |
| **Typed status model** | `SessionStatus` and `SessionType` enums describe each session precisely. |
| **Orchestration** | `SessionManager` ties registry, device trust, and rotation together. |

## When to use it

- You issue refresh tokens and need **rotation with reuse detection**, not just expiry.
- You want a user-facing "active sessions" / "log out everywhere" feature backed by real data.
- You need **device trust** so a recognised device can skip friction a new one shouldn't.

## Worked example

```bash
composer require padosoft/laravel-rebel-sessions
php artisan vendor:publish
php artisan migrate
```

Publishing and migrating creates the `rebel_devices` and `rebel_sessions` tables. The package binds
`DatabaseSessionRegistry` and `DatabaseDeviceTrust` to the core `SessionRegistry` and `DeviceTrust`
contracts, so the rest of the suite resolves them automatically — and you drive sessions and device
trust through `SessionManager` without coupling to the storage details.

## How it fits

This package depends only on `padosoft/laravel-rebel-core` for the contracts it implements. By
fulfilling `SessionRegistry` and `DeviceTrust`, it becomes the registry every other Rebel package
consults when it needs to know about sessions or device trust — and because those are contracts, you
can swap this database engine for your own (Redis, an external IdP) without rewiring the suite.

Session tracking, log-out-everywhere, and rotation-with-reuse-detection as one coherent package — not
three half-features bolted onto stock sessions — is the Rebel difference. See
[Why Rebel](/ecosystem/why-rebel).

---

## Reference

### Runtime files

- `src\Enums\SessionStatus.php`
- `src\Enums\SessionType.php`
- `src\Models\RebelDevice.php`
- `src\Models\RebelSession.php`
- `src\DatabaseDeviceTrust.php`
- `src\DatabaseSessionRegistry.php`
- `src\RebelSessionsServiceProvider.php`
- `src\SessionManager.php`

### Service providers

- `src\RebelSessionsServiceProvider.php`

### Services and managers

- `src\DatabaseSessionRegistry.php`
- `src\RebelSessionsServiceProvider.php`
- `src\SessionManager.php`

### Contracts

None detected in the package tree.

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

- `src\Models\RebelDevice.php`
- `src\Models\RebelSession.php`

### Config

- `config\rebel-sessions.php`

### Migrations

- `database\migrations\create_rebel_devices_table.php`
- `database\migrations\create_rebel_sessions_table.php`

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

::: collapsible "Problem: keep laravel-rebel-sessions replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\DeviceTrustTest.php`
- `tests\Feature\SessionManagerTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
