---
title: laravel-rebel-admin-api
description: The control-plane JSON API for Laravel Rebel — security metrics, an audit-event explorer, OTP/step-up funnels and provider health, permission-gated and tenant-scoped.
---

# laravel-rebel-admin-api

[GitHub repository](https://github.com/padosoft/laravel-rebel-admin-api) · Composer: `padosoft/laravel-rebel-admin-api` · MIT

> **The read models behind your security operations.** A JSON API that turns `rebel_auth_events`
> into overview metrics, an audit-event explorer, OTP/step-up funnels and provider health — every
> endpoint **permission-gated** and **tenant-scoped**. API only, no UI.

## What it is

The control-plane API for the Laravel Rebel suite. It projects raw audit events into queryable read
models — overviews, funnels, anomalies, channel and provider health, compliance and subject lookups —
and serves them over a clean JSON surface guarded by the `EnsureAdmin` middleware. It is deliberately
**headless**: bring your own UI, or pair it with `laravel-rebel-admin` for the ready-made panel.

## The problem it solves

The audit trail in `rebel_auth_events` is honest and complete, but it's raw. Answering "what's our
OTP delivery rate this week?", "where are users dropping out of step-up?" or "which provider is
degraded right now?" means aggregation, time bucketing, and — critically — making sure an admin only
sees their own tenant's data. This package builds those read models once, behind permission checks and
tenant scoping, so you never hand-roll a dashboard query against sensitive event data again.

## What you get

| Capability | Endpoint surface |
|---|---|
| **Overview & metrics** | `OverviewController`, `MetricsProjector`, `MetricBucket` — time-bucketed security KPIs. |
| **Audit-event explorer** | `AuthEventsController` — query and drill into `rebel_auth_events`. |
| **Funnels** | `FunnelController` — OTP / step-up conversion and drop-off. |
| **Anomalies** | `AnomaliesController` — anomaly cases surfaced for review. |
| **Channels & providers** | `ChannelsController`, `ProvidersController`, `HealthController` — delivery and provider health. |
| **Risk rules** | `RiskRulesController`, `RiskRuleEvaluator`, `RiskRule` — configurable rule read/write. |
| **Compliance & subjects** | `ComplianceController`, `SubjectsController`, `MeController`. |
| **Settings & copilot** | `SettingsController` (`AdminSetting`), `AiCopilotController`. |
| **Guarded & scoped** | `EnsureAdmin` middleware + `ResolvesTenant` — permission-gated, tenant-scoped. |

## When to use it

- You want **security KPIs and funnels** without writing aggregation queries against raw events.
- You're building a **custom admin UI** and need a stable, guarded JSON contract to call.
- You need read models that are **tenant-scoped and permission-gated** out of the box.
- You're installing `laravel-rebel-admin` (the panel) — it sits on top of this API.

## Worked example

```bash
composer require padosoft/laravel-rebel-admin-api
php artisan vendor:publish
php artisan migrate
```

Routes are registered from the package's `routes/api.php` behind the `EnsureAdmin` middleware. Build
metric buckets with the bundled command:

```bash
php artisan rebel:project-metrics
```

## How it fits

The admin API is the **read side** of the suite. It consumes the audit trail from
`laravel-rebel-core`, projects it into `MetricBucket` and related models, and serves it to whatever
front-end you choose. `laravel-rebel-admin` is the official consumer; the AI guard reads the same
buckets and rules. Everything stays tenant-scoped and permission-gated by construction.

A control-plane API that's headless, tenant-scoped and built directly on an auditable event store is
the hard part most dashboards skip — see **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Console\ProjectMetricsCommand.php`
- `src\Http\Concerns\ResolvesTenant.php`
- `src\Http\Controllers\AiCopilotController.php`
- `src\Http\Controllers\AnomaliesController.php`
- `src\Http\Controllers\AuthEventsController.php`
- `src\Http\Controllers\ChannelsController.php`
- `src\Http\Controllers\ComplianceController.php`
- `src\Http\Controllers\FunnelController.php`
- `src\Http\Controllers\HealthController.php`
- `src\Http\Controllers\MeController.php`
- `src\Http\Controllers\OverviewController.php`
- `src\Http\Controllers\ProvidersController.php`
- `src\Http\Controllers\RiskRulesController.php`
- `src\Http\Controllers\SettingsController.php`
- `src\Http\Controllers\SubjectsController.php`
- `src\Http\Middleware\EnsureAdmin.php`
- `src\Metrics\MetricsProjector.php`
- `src\Models\AdminSetting.php`
- `src\Models\MetricBucket.php`
- `src\Models\RiskRule.php`
- `src\Risk\RiskRuleEvaluator.php`
- `src\Support\AdminAudit.php`
- `src\Support\Period.php`
- `src\RebelAdminApiServiceProvider.php`

### Service providers

- `src\Http\Controllers\ProvidersController.php`
- `src\RebelAdminApiServiceProvider.php`

### Services and managers

- `src\RebelAdminApiServiceProvider.php`

### Contracts

None detected in the package tree.

### Controllers

- `src\Http\Controllers\AiCopilotController.php`
- `src\Http\Controllers\AnomaliesController.php`
- `src\Http\Controllers\AuthEventsController.php`
- `src\Http\Controllers\ChannelsController.php`
- `src\Http\Controllers\ComplianceController.php`
- `src\Http\Controllers\FunnelController.php`
- `src\Http\Controllers\HealthController.php`
- `src\Http\Controllers\MeController.php`
- `src\Http\Controllers\OverviewController.php`
- `src\Http\Controllers\ProvidersController.php`
- `src\Http\Controllers\RiskRulesController.php`
- `src\Http\Controllers\SettingsController.php`
- `src\Http\Controllers\SubjectsController.php`

### Middleware

- `src\Http\Middleware\EnsureAdmin.php`

### Models

- `src\Models\AdminSetting.php`
- `src\Models\MetricBucket.php`
- `src\Models\RiskRule.php`

### Config

- `config\rebel-admin-api.php`

### Migrations

- `database\migrations\create_rebel_admin_settings_table.php`
- `database\migrations\create_rebel_metric_buckets_table.php`
- `database\migrations\create_rebel_risk_rules_table.php`

### Routes

- `routes\api.php`

### Commands

- `src\Console\ProjectMetricsCommand.php`

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
| `padosoft/laravel-rebel-ai-guard` | `^0.1` |
| `padosoft/laravel-rebel-sessions` | `^0.1` |
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

### Architecture decisions

::: collapsible "Problem: keep laravel-rebel-admin-api replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\AdminGateTest.php`
- `tests\Feature\AiCopilotTest.php`
- `tests\Feature\AnomaliesTest.php`
- `tests\Feature\AuthEventDetailTest.php`
- `tests\Feature\AuthEventsExplorerTest.php`
- `tests\Feature\ChannelsProvidersTest.php`
- `tests\Feature\ComplianceMeSettingsTest.php`
- `tests\Feature\FunnelsTest.php`
- `tests\Feature\MetricsProjectorTest.php`
- `tests\Feature\OverviewTest.php`
- `tests\Feature\RiskRulesTest.php`
- `tests\Feature\SubjectsTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
