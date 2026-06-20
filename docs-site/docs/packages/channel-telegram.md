---
title: laravel-rebel-channel-telegram
description: Deliver Rebel OTP codes and security alerts straight to a Telegram chat — a bounded delivery channel that reports every send into the audit trail.
---

# laravel-rebel-channel-telegram

[GitHub repository](https://github.com/padosoft/laravel-rebel-channel-telegram) · Composer: `padosoft/laravel-rebel-channel-telegram` · MIT

> **A Telegram bot that speaks Rebel.** Drop in a delivery channel that pushes OTP codes and
> security alerts to a Telegram chat — with delivery telemetry flowing back into the core audit
> trail, and never an OTP in the logs.

## What it is

A **delivery channel** under the Rebel Channels umbrella. It takes a message the suite already wants
to send — a one-time code, a "new sign-in" alert — and delivers it to a Telegram chat through the
Bot API. It owns one bounded job: the Telegram transport. Everything about *when* to send, *what*
assurance the code carries, and *how* it's audited stays in `laravel-rebel-core`.

## The problem it solves

Email and SMS are the usual second factor, but they're slow, expensive, and easy to phish or
intercept. Plenty of teams already live in Telegram and want OTP codes and login alerts where they
actually read — instantly, for free, on a device they control. Wiring the Bot API by hand means
handling tokens, retries, and HTTP failures yourself, and then bolting on logging so the security
team can see whether a code was ever delivered. This package gives you that channel pre-wired and
already plugged into the Rebel audit trail.

## What you get

- A `TelegramDeliveryChannel` that delivers OTP codes and security alerts to a Telegram chat.
- A `TelegramGateway` contract with an HTTP implementation (`HttpTelegramGateway`) over the Bot API.
- A `FakeTelegramGateway` so your tests never hit the network.
- Delivery telemetry that feeds the core audit trail — sends and outcomes land in `rebel_auth_events`, never the OTP itself.
- One config file (`config/rebel-channel-telegram.php`) and zero routes or migrations to reason about.

## When to use it

- Your users already live in Telegram and you want OTP codes delivered there instead of by SMS.
- You want **free, instant** second-factor delivery without an SMS-gateway bill.
- You need security alerts ("new device signed in") pushed to a chat your team watches.

## Worked example

```bash
composer require padosoft/laravel-rebel-channel-telegram
php artisan vendor:publish
```

Set your bot token and target chat in `config/rebel-channel-telegram.php` (publish it first), then
let the Rebel Channels layer route OTP and alert deliveries through this channel. In tests, bind
`FakeTelegramGateway` instead of the HTTP gateway to assert what would have been sent without
touching the network.

## How it fits

This package is a leaf: it depends on `padosoft/laravel-rebel-channels` for the channel contract and
on `padosoft/laravel-rebel-core` for the audit and assurance vocabulary. It adds no routes, models,
or migrations — it's pure transport. Swap it in or out without touching the rest of the suite, and
every send it makes stays visible in the same audit trail as every other channel.

A single shared audit trail across Telegram, Discord, SMS and email is exactly what bolt-on
notification packages don't give you — see [Why Rebel](/ecosystem/why-rebel).

---

## Reference

### Runtime files

- `src\Contracts\TelegramGateway.php`
- `src\Delivery\TelegramDeliveryChannel.php`
- `src\Gateway\HttpTelegramGateway.php`
- `src\Testing\FakeTelegramGateway.php`
- `src\RebelTelegramServiceProvider.php`

### Service providers

- `src\RebelTelegramServiceProvider.php`

### Services and managers

- `src\RebelTelegramServiceProvider.php`

### Contracts

- `src\Contracts\TelegramGateway.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-channel-telegram.php`

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
| `padosoft/laravel-rebel-channels` | `^0.1.2` |
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

::: collapsible "Problem: keep laravel-rebel-channel-telegram replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\HttpTelegramGatewayTest.php`
- `tests\Feature\NotConfiguredTest.php`
- `tests\Feature\RegistrationDisabledTest.php`
- `tests\Feature\RegistrationTest.php`
- `tests\Feature\TelegramDeliveryChannelTest.php`
- `tests\Live\TelegramLiveTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
