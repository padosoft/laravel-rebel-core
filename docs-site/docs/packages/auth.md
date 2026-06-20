---
title: laravel-rebel-auth
description: The one-line install for the whole Laravel Rebel suite — a curated meta-package that pulls in core plus the recommended feature packages and wires them together.
---

# laravel-rebel-auth

[GitHub repository](https://github.com/padosoft/laravel-rebel-auth) · Composer: `padosoft/laravel-rebel-auth` · MIT

> **Start here.** One `composer require` brings in the recommended Rebel stack — passwordless email-OTP, passkey-first login, risk-based step-up, channels, sessions, recovery, anomaly detection and the web admin panel — already wired to work together.

::: callout info
`laravel-rebel-auth` is a **meta-package**: it holds no business logic of its own. It is the curated bundle plus the service-provider wiring that ties the suite together.
:::

---

## What it is

`laravel-rebel-auth` is the opinionated entry point to the Laravel Rebel suite. Instead of choosing and aligning a dozen `padosoft/laravel-rebel-*` packages by hand, you install this one. It declares the recommended set as dependencies and registers a single service provider that bootstraps the suite on top of Laravel Fortify.

## The problem it solves

A complete enterprise authentication control plane has many moving parts: passwordless login, step-up confirmation, delivery channels, session tracking, account recovery, anomaly detection, an admin API and an admin UI. Assembling them one by one — and keeping their versions and bindings in sync — is tedious and easy to get wrong. This package collapses that into a single install with a known-good combination, so you get a coherent stack on the first try.

## What you get

- **One install** for the recommended Rebel suite — no manual dependency picking.
- **Suite wiring** through a single service provider (`RebelAuthServiceProvider`).
- **Passwordless email-OTP** login for web and mobile.
- **Passkey-first** authentication with risk-based **step-up** confirmation (PSD2/SCA dynamic linking).
- **Channels, sessions, recovery** and **anomaly detection**.
- A **web admin panel** plus its admin API.
- The shared **core** vocabulary — assurance model, keyed hashing, redacting audit trail — underneath it all.

## When to use it

- You are starting a new app and want the **full recommended Rebel stack** in one step.
- You want the packages **pre-aligned** on compatible versions and container bindings.
- You prefer a curated bundle over hand-picking individual feature packages.
- You do **not** need it if you only want one capability — install that single feature package instead.

## Worked example

```bash
composer require padosoft/laravel-rebel-auth
php artisan vendor:publish
php artisan migrate
```

## How it fits

This is the top of the Laravel Rebel stack. It depends on `padosoft/laravel-rebel-core` (the shared value objects, assurance model, keyed hashing and audit trail) and pulls in the recommended feature packages — email-OTP, step-up, channels, sessions, recovery, the AI guard, the Fortify bridge, and the admin UI/API — then wires them together. Everything it installs ultimately speaks the same core vocabulary, so the suite stays auditable end-to-end.

A curated bundle beats hand-assembling a dozen packages — see the full breakdown in **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\RebelAuthServiceProvider.php`

### Service providers

- `src\RebelAuthServiceProvider.php`

### Services and managers

- `src\RebelAuthServiceProvider.php`

### Contracts

None detected in the package tree.

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

None detected in the package tree.

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
| `padosoft/laravel-rebel-admin` | `^0.1` |
| `padosoft/laravel-rebel-admin-api` | `^0.1` |
| `padosoft/laravel-rebel-ai-guard` | `^0.1` |
| `padosoft/laravel-rebel-bridge-fortify` | `^0.1` |
| `padosoft/laravel-rebel-channels` | `^0.1` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
| `padosoft/laravel-rebel-recovery` | `^0.1` |
| `padosoft/laravel-rebel-sessions` | `^0.1` |
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

### ADR

::: collapsible "Problem: keep laravel-rebel-auth replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\SuiteWiringTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
