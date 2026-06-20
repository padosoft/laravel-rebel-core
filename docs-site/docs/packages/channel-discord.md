---
title: laravel-rebel-channel-discord
description: Ship Rebel security and SOC alerts — anomaly cases, lockouts, high-risk events — to a Discord channel via webhook, with every send recorded in the audit trail.
---

# laravel-rebel-channel-discord

[GitHub repository](https://github.com/padosoft/laravel-rebel-channel-discord) · Composer: `padosoft/laravel-rebel-channel-discord` · MIT

> **Your SOC feed, in Discord.** A delivery channel that pushes security and SOC alerts — anomaly
> cases, lockouts, high-risk events — to a Discord channel via webhook, so the team sees incidents
> the moment they happen, with each send logged in the Rebel audit trail.

## What it is

A **delivery channel** under the Rebel Channels umbrella, focused on operational and security
notifications. When the suite flags an anomaly case, locks an account, or detects a high-risk event,
this package ships that alert to a Discord channel through an incoming webhook. It owns one bounded
job: the Discord transport. The decision to alert — and the assurance and audit semantics behind it
— live in `laravel-rebel-core`.

## The problem it solves

Security signals are worthless if nobody sees them in time. Most teams already run a Discord server,
and a `#security` channel is the natural place for lockouts and anomaly alerts to land — but stitching
a webhook to your auth events by hand means hand-rolling payloads, retries, and a separate log of
what was actually delivered. This package turns Rebel's security-significant events into Discord
alerts out of the box, and records every delivery back into the same audit trail the rest of the
suite uses — so "did the team get paged?" is an auditable fact, not a guess.

## What you get

- A `DiscordDeliveryChannel` that ships security/SOC alerts (anomaly cases, lockouts, high-risk events) to a Discord channel.
- A `DiscordGateway` contract with a webhook-based HTTP implementation (`HttpDiscordGateway`).
- A `FakeDiscordGateway` so your tests assert payloads without hitting Discord.
- Delivery telemetry that feeds the core audit trail — sends and outcomes land in `rebel_auth_events`, never secrets.
- One config file (`config/rebel-channel-discord.php`) and zero routes or migrations to reason about.

## When to use it

- Your team runs Discord and wants security/SOC alerts in a channel they already watch.
- You want **anomaly cases, lockouts, and high-risk events** pushed in real time, not buried in a dashboard.
- You need webhook delivery wired into the Rebel audit trail without building the plumbing yourself.

## Worked example

```bash
composer require padosoft/laravel-rebel-channel-discord
php artisan vendor:publish
```

Configure the Discord webhook URL in `config/rebel-channel-discord.php` (publish it first), then let
the Rebel Channels layer route security alerts through this channel. In tests, bind
`FakeDiscordGateway` to assert exactly what would be posted to Discord without sending a real
request.

## How it fits

This package is a leaf: it depends on `padosoft/laravel-rebel-channels` for the channel contract and
on `padosoft/laravel-rebel-core` for the audit and assurance vocabulary. It adds no routes, models,
or migrations — it's pure transport. Add it for SOC alerting, remove it when you switch tools, and
the rest of the suite never notices, while every alert it sends stays visible in the shared audit
trail.

One audit trail spanning every channel — Discord, Telegram, SMS, email — is what bolt-on webhook
notifiers can't offer. See [Why Rebel](/ecosystem/why-rebel).

---

## Reference

### Runtime files

- `src\Contracts\DiscordGateway.php`
- `src\Delivery\DiscordDeliveryChannel.php`
- `src\Gateway\HttpDiscordGateway.php`
- `src\Testing\FakeDiscordGateway.php`
- `src\RebelDiscordServiceProvider.php`

### Service providers

- `src\RebelDiscordServiceProvider.php`

### Services and managers

- `src\RebelDiscordServiceProvider.php`

### Contracts

- `src\Contracts\DiscordGateway.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-channel-discord.php`

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

::: collapsible "Problem: keep laravel-rebel-channel-discord replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\DiscordDeliveryChannelTest.php`
- `tests\Feature\HttpDiscordGatewayTest.php`
- `tests\Feature\RegistrationTest.php`
- `tests\Live\DiscordLiveTest.php`
- `tests\Support\RecordingAuditLogger.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
