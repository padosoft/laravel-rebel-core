---
title: laravel-rebel-demo
description: A runnable reference application that wires the entire Laravel Rebel suite together — channels, bridges, sessions, step-up, admin and AI guard — so you can read and run a real integration.
---

# laravel-rebel-demo

[GitHub repository](https://github.com/padosoft/laravel-rebel-demo) · Composer: `padosoft/laravel-rebel-demo` · MIT

> **The whole suite, wired up and running.** A reference Laravel application that integrates the
> Rebel packages end to end — channels, bridges, sessions, step-up, the admin panel and the AI guard —
> so you can see exactly how the pieces fit before building your own.

## What it is

A complete, runnable Laravel app that depends on the Rebel suite and configures it the way it's meant
to be used: Fortify and Sanctum at the base, every OTP/2FA bridge (`otpz`, Laragear two-factor,
Spatie passkeys, Spatie one-time-passwords) wired in, the full set of delivery channels (Twilio,
Vonage, Bird, Telegram, Discord), sessions, step-up, recovery, the admin API and panel, and the AI
guard. It exists to be **read and run**, not depended on in production.

## The problem it solves

A suite of 20+ packages is powerful but raises an obvious question: *how do they actually fit
together in one app?* Which config keys, which migrations, which service bindings, in which order?
Reading each package README leaves gaps at the seams. The demo answers it by being the seam itself —
a single repository where every package is installed, configured and migrated together, so you can
copy the wiring instead of reverse-engineering it.

## What you get

- **A reference integration** — every Rebel package installed and configured in one working app.
- **Real config, side by side** — `rebel-core`, `rebel-channels`, `rebel-step-up`, `rebel-sessions`,
  the bridge configs and more, all published and tuned together.
- **A complete migration set** — auth events, OTP/step-up challenges, metric buckets, sessions,
  devices, recovery codes, anomaly cases, risk rules, admin settings, passkeys and 2FA tables.
- **Provider breadth** — Twilio, Vonage, Bird, Telegram and Discord channels wired in at once.
- **Something to run** — boot it locally to exercise the flows the rest of the docs describe.

## When to use it

- You're **starting an integration** and want a known-good wiring to copy from.
- You need to see **which config and migrations** a full Rebel install actually produces.
- You want to **exercise the flows** (login, OTP, step-up, admin panel) against a real app.
- You're evaluating the suite and prefer to **read and run** over reading specs.

## Worked example

```bash
git clone https://github.com/padosoft/laravel-rebel-demo
cd laravel-rebel-demo
composer install
php artisan migrate
php artisan db:seed
```

::: callout warning
This is a **reference application**, not a library. Read its wiring, run it locally, copy the
patterns — but don't add `padosoft/laravel-rebel-demo` as a production dependency.
:::

## How it fits

The demo sits at the top of the dependency graph: it consumes the whole suite and adds no reusable
library code of its own. Treat it as living documentation of the integration surface — when you want
to know how `laravel-rebel-core`, the channels, the bridges and the admin packages compose into one
app, this is the worked example.

---

## Reference

### Runtime files

None detected in the package tree.

### Service providers

None detected in the package tree.

### Services and managers

None detected in the package tree.

### Contracts

None detected in the package tree.

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\app.php`
- `config\auth.php`
- `config\cache.php`
- `config\database.php`
- `config\filesystems.php`
- `config\logging.php`
- `config\mail.php`
- `config\one-time-passwords.php`
- `config\otpz.php`
- `config\passkeys.php`
- `config\queue.php`
- `config\rebel-admin-api.php`
- `config\rebel-admin.php`
- `config\rebel-ai-guard.php`
- `config\rebel-bot-protection.php`
- `config\rebel-bridge-laragear-2fa.php`
- `config\rebel-bridge-otpz.php`
- `config\rebel-bridge-passkeys.php`
- `config\rebel-bridge-spatie-otp.php`
- `config\rebel-channel-twilio.php`
- `config\rebel-channels.php`
- `config\rebel-core.php`
- `config\rebel-email-otp.php`
- `config\rebel-recovery.php`
- `config\rebel-sessions.php`
- `config\rebel-step-up.php`
- `config\services.php`
- `config\session.php`
- `config\two-factor.php`

### Migrations

- `database\factories\UserFactory.php`
- `database\migrations\0001_01_01_000000_create_users_table.php`
- `database\migrations\0001_01_01_000001_create_cache_table.php`
- `database\migrations\0001_01_01_000002_create_jobs_table.php`
- `database\migrations\2026_06_03_123812_create_rebel_auth_events_table.php`
- `database\migrations\2026_06_03_123835_create_rebel_email_otp_challenges_table.php`
- `database\migrations\2026_06_03_123836_create_rebel_step_up_challenges_table.php`
- `database\migrations\2026_06_03_123837_create_rebel_metric_buckets_table.php`
- `database\migrations\2026_06_03_123838_create_rebel_sessions_table.php`
- `database\migrations\2026_06_03_123839_create_rebel_devices_table.php`
- `database\migrations\2026_06_03_123839_create_rebel_recovery_codes_table.php`
- `database\migrations\2026_06_03_123840_create_rebel_anomaly_cases_table.php`
- `database\migrations\2026_06_03_130000_add_is_admin_to_users_table.php`
- `database\migrations\2026_06_03_154527_create_rebel_risk_rules_table.php`
- `database\migrations\2026_06_03_154528_create_rebel_admin_settings_table.php`
- `database\migrations\2026_06_04_032639_create_passkeys_table.php`
- `database\migrations\2026_06_04_032711_create_one_time_passwords_table.php`
- `database\migrations\2026_06_04_032742_create_two_factor_authentications_table.php`
- `database\migrations\2026_06_04_032808_create_otps_table.php`
- `database\seeders\DatabaseSeeder.php`

### Routes

- `routes\console.php`
- `routes\web.php`

### Commands

None detected in the package tree.

### Composer requirements

| Dependency | Constraint |
|---|---|
| `benbjurstrom/otpz` | `0.7` |
| `laragear/two-factor` | `4.0` |
| `laravel/fortify` | `^1.25` |
| `laravel/framework` | `^13.8` |
| `laravel/sanctum` | `^4.0` |
| `laravel/tinker` | `^3.0` |
| `padosoft/laravel-rebel-admin-api` | `0.1.7` |
| `padosoft/laravel-rebel-auth` | `^0.1` |
| `padosoft/laravel-rebel-bot-protection` | `0.1` |
| `padosoft/laravel-rebel-bridge-laragear-2fa` | `0.1.1` |
| `padosoft/laravel-rebel-bridge-otpz` | `0.1` |
| `padosoft/laravel-rebel-bridge-passkeys` | `0.1` |
| `padosoft/laravel-rebel-bridge-spatie-otp` | `0.1` |
| `padosoft/laravel-rebel-channel-bird` | `0.1` |
| `padosoft/laravel-rebel-channel-discord` | `0.1` |
| `padosoft/laravel-rebel-channel-telegram` | `0.1` |
| `padosoft/laravel-rebel-channel-twilio` | `^0.1` |
| `padosoft/laravel-rebel-channel-vonage` | `0.1` |
| `padosoft/laravel-rebel-channels` | `0.1.2` |
| `php` | `^8.3` |
| `spatie/laravel-one-time-passwords` | `1.1` |
| `spatie/laravel-passkeys` | `1.8` |

### Development requirements

| Dependency | Constraint |
|---|---|
| `fakerphp/faker` | `^1.23` |
| `laravel/pail` | `^1.2.5` |
| `laravel/pao` | `^1.0.6` |
| `laravel/pint` | `^1.27` |
| `mockery/mockery` | `^1.6` |
| `nunomaduro/collision` | `^8.6` |
| `phpunit/phpunit` | `^12.5.12` |

### Architecture decisions

::: collapsible "Problem: keep laravel-rebel-demo replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\ExampleTest.php`
- `tests\Feature\ExtrasIntegrationTest.php`
- `tests\Unit\ExampleTest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
