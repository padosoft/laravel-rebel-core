---
title: laravel-rebel-bot-protection
description: A pluggable, fail-closed anti-bot gate for Laravel Rebel — server-side verification of Turnstile, reCAPTCHA v3 and hCaptcha, fully audited.
---

# laravel-rebel-bot-protection

[GitHub repository](https://github.com/padosoft/laravel-rebel-bot-protection) · Composer: `padosoft/laravel-rebel-bot-protection` · MIT

> **Stop the bots before they cost you an SMS.** A single, swappable CAPTCHA gate that verifies
> Cloudflare Turnstile, Google reCAPTCHA v3 and hCaptcha tokens **server-side**, fails **closed** by
> default, and writes every decision to the Rebel audit trail.

## What it is

A focused implementation of the core `BotProtection` contract. It takes the CAPTCHA token your
front-end already collected, verifies it against the provider's API from the server, and returns a
clean pass/fail verdict the rest of the suite can act on. Three providers ship out of the box —
**Turnstile**, **reCAPTCHA v3** and **hCaptcha** — behind one common interface, so switching vendors
is a config change, not a rewrite.

## The problem it solves

Bots don't just spam forms — on an auth flow they trigger **real OTP sends**, and every fake SMS is
money out the door plus noise in your security metrics. Client-side CAPTCHA widgets alone prove
nothing: the token has to be verified on the server, and the gate has to **fail closed** so a provider
outage can't silently wave attackers through. This package puts that verification in one auditable
place, in front of the flows that cost you money.

## What you get

- **Three providers, one contract** — `TurnstileBotProtection`, `RecaptchaBotProtection`,
  `HcaptchaBotProtection`, all behind the core `BotProtection` interface.
- **Fail-closed by default** — an unverifiable token is a denied request, not an open door.
- **Server-side verification** — `HttpCaptchaVerifier` talks to the provider; `VerificationResult`
  carries the structured outcome.
- **Fully audited** — every verdict flows through the core audit vocabulary, so the admin panel and
  AI guard can see bot pressure across the whole suite.
- **Testable end-to-end** — `FakeCaptchaVerifier` and `AlwaysPassBotProtection` let you exercise the
  gate without hitting a real provider.

## When to use it

- You send OTPs / 2FA codes and want a gate **before** the SMS or email goes out.
- You need **server-side** CAPTCHA verification, not just a front-end widget.
- You want to switch between Turnstile, reCAPTCHA v3 and hCaptcha without touching call sites.
- You need bot decisions to land in the same audit trail as the rest of your auth events.

## Worked example

```bash
composer require padosoft/laravel-rebel-bot-protection
php artisan vendor:publish
```

Configure your provider and keys in `config/rebel-bot-protection.php`, then let the suite resolve the
core `BotProtection` contract — the selected driver verifies tokens server-side and fails closed when
verification can't complete.

## How it fits

Bot protection sits at the **front edge** of an auth flow. It binds the core `BotProtection` contract,
so step-up, OTP and login flows gate on it without knowing which provider is configured. Because every
verdict is audited through `laravel-rebel-core`, bot pressure shows up in the admin API and the AI
guard alongside every other signal.

A pluggable, fail-closed, fully-audited CAPTCHA gate is rarer than it sounds — see the breakdown in
**[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Contracts\CaptchaVerifier.php`
- `src\Gateway\HttpCaptchaVerifier.php`
- `src\Providers\AbstractCaptchaBotProtection.php`
- `src\Providers\AlwaysPassBotProtection.php`
- `src\Providers\HcaptchaBotProtection.php`
- `src\Providers\RecaptchaBotProtection.php`
- `src\Providers\TurnstileBotProtection.php`
- `src\Testing\FakeCaptchaVerifier.php`
- `src\Verification\VerificationResult.php`
- `src\RebelBotProtectionServiceProvider.php`

### Service providers

- `src\Providers\AbstractCaptchaBotProtection.php`
- `src\Providers\AlwaysPassBotProtection.php`
- `src\Providers\HcaptchaBotProtection.php`
- `src\Providers\RecaptchaBotProtection.php`
- `src\Providers\TurnstileBotProtection.php`
- `src\RebelBotProtectionServiceProvider.php`

### Services and managers

- `src\Contracts\CaptchaVerifier.php`
- `src\Gateway\HttpCaptchaVerifier.php`
- `src\Testing\FakeCaptchaVerifier.php`
- `src\RebelBotProtectionServiceProvider.php`

### Contracts

- `src\Contracts\CaptchaVerifier.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bot-protection.php`

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

::: collapsible "Problem: keep laravel-rebel-bot-protection replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\AlwaysPassBotProtectionTest.php`
- `tests\Feature\CaptchaProviderTest.php`
- `tests\Feature\DriverSelectionTest.php`
- `tests\Feature\HttpCaptchaVerifierTest.php`
- `tests\Live\CaptchaLiveTest.php`
- `tests\Support\SpyAuditLogger.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
