---
title: laravel-rebel-step-up
description: Per-action step-up confirmation with AAL/AMR assurance, risk-based escalation and PSD2/SCA dynamic linking — the confirmation, not the login.
---

# laravel-rebel-step-up

[GitHub repository](https://github.com/padosoft/laravel-rebel-step-up) · Composer: `padosoft/laravel-rebel-step-up` · MIT

> **Confirm the action, not just the session.** Before a sensitive operation runs, step-up asks the user to re-prove themselves at an assurance level that matches the risk — and for payments it binds the confirmation to the exact amount and payee.

::: callout info
Step-up is **not** the login. It is the per-action confirmation layered on top of an existing session — "you are signed in, but this transfer needs a stronger check right now".
:::

---

## What it is

`laravel-rebel-step-up` gates a sensitive action behind a fresh, risk-appropriate confirmation. Each action has a *purpose* and a policy that says what assurance (AAL/AMR) it requires; the package starts a challenge, lets a driver satisfy it, and only then allows the action to proceed. For payments it adds PSD2/SCA **dynamic linking**: the confirmation is bound to the transaction details, so if the amount or payee changes, the confirmation no longer applies.

## The problem it solves

A logged-in session is not enough authority to move money or change security settings. Most apps either over-ask (re-prompt for everything) or under-protect (trust the session for everything). Step-up gives you a precise middle ground: a per-purpose policy decides *when* to escalate and *how strong* the confirmation must be, driven by risk. And because the confirmation is dynamically linked to the transaction, a confirmed €10 cart can't be silently turned into a €1,000 transfer.

## What you get

- **Per-action / per-purpose confirmation** governed by a policy, not a global flag.
- **AAL/AMR assurance enforcement** — the action states the level it needs, the challenge proves it.
- **Risk-based escalation:** step up only when the risk warrants it.
- **PSD2/SCA dynamic linking** via `TransactionContext` — confirmation bound to amount + payee; if the total changes, it expires.
- **Pluggable drivers** (`StepUpDriver`) — the bundled email-OTP driver, plus the bridge packages (fortify, passkeys, totp, otpz) as additional drivers.
- **Route protection** through the `EnsureStepUp` middleware.

## When to use it

- You have **sensitive actions** — payments, transfers, security-setting changes — that need more than a valid session.
- You must satisfy **PSD2/SCA** with dynamic linking to the transaction.
- You want confirmation strength to **scale with risk** instead of prompting for everything.
- You need to choose the confirmation factor per app via **swappable drivers**.

## Worked example

```bash
composer require padosoft/laravel-rebel-step-up
php artisan vendor:publish
php artisan migrate
```

Protect a sensitive route with the bundled `EnsureStepUp` middleware and let the per-purpose policy decide the required assurance:

```php
// routes/web.php
Route::post('/transfers', [TransferController::class, 'store'])
    ->middleware(EnsureStepUp::class);
```

::: callout tip
For payments, capture the transaction in a `TransactionContext` so the confirmation is dynamically linked: change the amount or payee and the prior confirmation no longer satisfies the challenge.
:::

## How it fits

Step-up builds on `padosoft/laravel-rebel-core` (the AAL/AMR assurance model and the audit trail) and on `padosoft/laravel-rebel-email-otp`, whose driver ships as the default confirmation factor. The bridge packages (fortify, passkeys, totp, otpz) plug in as additional `StepUpDriver`s, so you can confirm with whatever factor fits the action's required assurance. Every challenge outcome flows through the core audit trail.

A policy-driven, dynamically linked step-up beats a hand-rolled re-auth prompt — see **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Config\StepUpConfigValidator.php`
- `src\Contracts\HasStepUpEmail.php`
- `src\Contracts\StepUpDriver.php`
- `src\Drivers\EmailOtpStepUpDriver.php`
- `src\Enums\StepUpStatus.php`
- `src\Exceptions\NoAvailableDriverException.php`
- `src\Http\Middleware\EnsureStepUp.php`
- `src\Models\StepUpChallenge.php`
- `src\Results\StepUpResult.php`
- `src\Results\StepUpStartResult.php`
- `src\Sca\TransactionContext.php`
- `src\Testing\FakeStepUpDriver.php`
- `src\DriverRegistry.php`
- `src\PolicyRepository.php`
- `src\PurposePolicy.php`
- `src\RebelStepUp.php`
- `src\RebelStepUpServiceProvider.php`
- `src\StepUpContext.php`

### Service providers

- `src\RebelStepUpServiceProvider.php`

### Services and managers

- `src\DriverRegistry.php`
- `src\PolicyRepository.php`
- `src\RebelStepUpServiceProvider.php`

### Contracts

- `src\Contracts\HasStepUpEmail.php`
- `src\Contracts\StepUpDriver.php`

### Controllers

None detected in the package tree.

### Middleware

- `src\Http\Middleware\EnsureStepUp.php`

### Models

- `src\Models\StepUpChallenge.php`

### Config

- `config\rebel-step-up.php`

### Migrations

- `database\migrations\create_rebel_step_up_challenges_table.php`

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
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
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

::: collapsible "Problem: keep laravel-rebel-step-up replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\StepUpConfigValidatorTest.php`
- `tests\Feature\StepUpEmailDriverTest.php`
- `tests\Feature\StepUpManagerTest.php`
- `tests\Feature\StepUpMiddlewareTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
