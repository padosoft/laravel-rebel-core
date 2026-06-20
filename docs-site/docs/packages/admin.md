---
title: laravel-rebel-admin
description: The Laravel Rebel Web Admin Panel — a security operations dashboard in Blade + AJAX + vanilla JS over the Admin API, no mandatory JS framework.
---

# laravel-rebel-admin

[GitHub repository](https://github.com/padosoft/laravel-rebel-admin) · Composer: `padosoft/laravel-rebel-admin` · MIT

> **Your SOC dashboard for auth.** A ready-made Web Admin Panel — Blade, AJAX and vanilla JS, no
> mandatory front-end framework — that turns the Rebel Admin API into a live security operations
> view: metrics, audit explorer, funnels and provider health.

![Laravel Rebel — Web Admin Panel dashboard](/assets/laravel-rebel-admin-dashboard.png)

## What it is

The official front-end for the Laravel Rebel control plane. It renders the read models exposed by
`laravel-rebel-admin-api` as a security operations (SOC) dashboard: overview KPIs, the audit-event
explorer, OTP/step-up funnels, anomalies and provider health — all behind the `EnsurePanelAccess`
middleware. It's built with **Blade + AJAX + plain JavaScript**, so there's no SPA build step and no
mandatory JS framework to adopt.

## The problem it solves

You have the data and a clean API — but someone on the security team still needs a screen. Building
and maintaining a bespoke admin SPA is a project of its own, and most teams don't want a React/Vue
toolchain just to watch auth metrics. This panel gives you that operations view immediately, mounted
on your existing Laravel app, with no front-end framework lock-in. Importantly, it works **without
`laravel-rebel-ai-guard`** — the AI copilot is optional, the dashboard is not.

## What you get

- **A SOC dashboard, out of the box** — overview, audit explorer, funnels, anomalies and provider
  health rendered from the Admin API.
- **No JS framework required** — `PanelController` serves Blade views driven by AJAX and vanilla JS.
- **Access-gated** — the `EnsurePanelAccess` middleware guards every panel route.
- **Section-organized** — `Panel\Sections` defines the navigation structure of the dashboard.
- **Works without ai-guard** — the panel is fully usable on its own; AI explanations are an optional add-on.

## When to use it

- You want a **ready-made admin UI** for Rebel without building one yourself.
- Your team prefers **Blade + vanilla JS** over adopting a front-end SPA framework.
- You need an operations view for auth metrics, audit events and provider health today.
- You've already installed `laravel-rebel-admin-api` and want the matching panel on top.

## Worked example

```bash
composer require padosoft/laravel-rebel-admin
php artisan vendor:publish
```

The panel registers its routes from the package's `routes/web.php` behind the `EnsurePanelAccess`
middleware. Configure access and panel options in `config/rebel-admin.php`, then visit the panel in
your browser — it calls the Admin API for every read.

## How it fits

The admin panel is the **presentation layer** of the suite. It depends on `laravel-rebel-admin-api`
for all data and on `laravel-rebel-core` for the shared vocabulary; it adds no read models of its
own. Because the API is tenant-scoped and permission-gated, the panel inherits those guarantees
without re-implementing them.

A no-framework SOC dashboard that drops straight onto an existing Laravel app is a rare convenience —
see **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Http\Controllers\PanelController.php`
- `src\Http\Middleware\EnsurePanelAccess.php`
- `src\Panel\Sections.php`
- `src\RebelAdminServiceProvider.php`

### Service providers

- `src\RebelAdminServiceProvider.php`

### Services and managers

- `src\RebelAdminServiceProvider.php`

### Contracts

None detected in the package tree.

### Controllers

- `src\Http\Controllers\PanelController.php`

### Middleware

- `src\Http\Middleware\EnsurePanelAccess.php`

### Models

None detected in the package tree.

### Config

- `config\rebel-admin.php`

### Migrations

None detected in the package tree.

### Routes

- `routes\web.php`

### Commands

None detected in the package tree.

### Composer requirements

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-admin-api` | `^0.1` |
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

::: collapsible "Problem: keep laravel-rebel-admin replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\PanelTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
