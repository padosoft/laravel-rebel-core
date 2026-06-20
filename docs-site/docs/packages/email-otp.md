---
title: laravel-rebel-email-otp
description: Enterprise passwordless email-OTP login for web and mobile — anti-enumeration, multi-dimensional rate limiting and Sanctum token issuance, all audited.
---

# laravel-rebel-email-otp

[GitHub repository](https://github.com/padosoft/laravel-rebel-email-otp) · Composer: `padosoft/laravel-rebel-email-otp` · MIT

> **Passwordless login that holds up in production.** A one-time code by email, with anti-enumeration baked in, rate limiting across four dimensions, and a Sanctum token pair for your mobile clients — every step recorded in the audit trail.

::: callout info
Email-OTP is an **AAL1** factor (NIST 800-63B). It is great for low-friction login, but it is not phishing-resistant — pair it with passkeys and **[step-up](/packages/step-up)** for sensitive actions.
:::

---

## What it is

`laravel-rebel-email-otp` delivers passwordless login: a user enters their email, receives a numeric one-time code, and verifies it. On success it returns a web login result or a Sanctum **`TokenPair`** (access + refresh) for API and mobile clients. The whole flow is tenant-, purpose- and risk-aware and emits audit events through the core vocabulary.

## The problem it solves

Rolling your own email-OTP looks simple until you hit the hard parts: an attacker can probe which addresses exist (account enumeration), a single rate limit is trivially bypassed, and OTPs end up in logs. This package closes those gaps by design — the start endpoint always returns the same response whether or not the address exists, rate limiting is enforced across **IP, identifier, tenant and purpose**, and the code never reaches the audit log in cleartext.

## What you get

- **Passwordless email-OTP** for web and mobile, with a Sanctum **`TokenPair`** for API clients.
- **Anti-enumeration:** the same response is returned regardless of whether the identifier exists.
- **Multi-dimensional rate limiting:** IP + identifier + tenant + purpose.
- **Multi-tenant / multi-purpose / risk-aware** challenges out of the box.
- **Audited outcomes:** verified logins are recorded as AAL1 with AMR `['otp', 'email']`.
- **Lifecycle hygiene:** a console command prunes expired challenges.

## When to use it

- You want **passwordless login** for a web app, a mobile app, or both.
- You need a login flow that is **safe against account enumeration** by default.
- You serve **multiple tenants or purposes** and need challenges scoped per dimension.
- You want login outcomes **in the audit trail** without wiring it yourself.

## Worked example

```bash
composer require padosoft/laravel-rebel-email-otp
php artisan vendor:publish
php artisan migrate
```

The package registers its web routes (`routes/web.php`) and an `EmailOtpController` that drives the start / resend / verify actions, plus a console command (`PruneChallengesCommand`) you can schedule to clear expired challenges.

::: callout tip
A verified challenge emits an audit event at **AAL1** with AMR `['otp', 'email']` — so an admin or risk engine can later tell that this session was established by email-OTP and not by a stronger factor.
:::

## How it fits

This package builds directly on `padosoft/laravel-rebel-core`: it issues the Sanctum `TokenPair`, stores identifiers as keyed HMACs, and records verifications through the core audit trail. Because the verified factor carries its assurance level (AAL1, non-phishing-resistant), **[step-up](/packages/step-up)** can later require a stronger factor before a sensitive action proceeds.

A purpose-built, audited, anti-enumeration OTP flow beats a hand-rolled one — see **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Actions\ResendEmailOtpChallenge.php`
- `src\Actions\StartEmailOtpChallenge.php`
- `src\Actions\VerifyEmailOtpChallenge.php`
- `src\Console\PruneChallengesCommand.php`
- `src\Enums\ChallengeStatus.php`
- `src\Http\Controllers\EmailOtpController.php`
- `src\Models\EmailOtpChallenge.php`
- `src\Notifications\EmailOtpNotification.php`
- `src\Otp\NumericOtpGenerator.php`
- `src\Otp\OtpHasher.php`
- `src\Resolvers\NullSubjectResolver.php`
- `src\Results\StartEmailOtpResult.php`
- `src\Results\VerifyEmailOtpResult.php`
- `src\RebelEmailOtp.php`
- `src\RebelEmailOtpServiceProvider.php`

### Service providers

- `src\RebelEmailOtpServiceProvider.php`

### Services and managers

- `src\Resolvers\NullSubjectResolver.php`
- `src\RebelEmailOtpServiceProvider.php`

### Contracts

None detected in the package tree.

### Controllers

- `src\Http\Controllers\EmailOtpController.php`

### Middleware

None detected in the package tree.

### Models

- `src\Models\EmailOtpChallenge.php`

### Config

- `config\rebel-email-otp.php`

### Migrations

- `database\migrations\create_rebel_email_otp_challenges_table.php`

### Routes

- `routes\web.php`

### Commands

- `src\Console\PruneChallengesCommand.php`

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

### ADR

::: collapsible "Problem: keep laravel-rebel-email-otp replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\EmailOtpFlowTest.php`
- `tests\Feature\EmailOtpResendTest.php`
- `tests\Feature\EmailOtpSubjectTest.php`
- `tests\Feature\EmailOtpTenantTest.php`
- `tests\Feature\EmailOtpWebFlowTest.php`
- `tests\Feature\MigrationTest.php`
- `tests\Feature\PruneChallengesTest.php`
- `tests\Unit\NumericOtpGeneratorTest.php`
- `tests\Unit\SkeletonTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
