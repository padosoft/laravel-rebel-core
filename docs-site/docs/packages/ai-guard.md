---
title: laravel-rebel-ai-guard
description: Deterministic anomaly detection plus an optional AI copilot that only explains and suggests on sanitized prompts — never PII, never destructive on its own.
---

# laravel-rebel-ai-guard

[GitHub repository](https://github.com/padosoft/laravel-rebel-ai-guard) · Composer: `padosoft/laravel-rebel-ai-guard` · MIT

> **Rules decide, AI explains.** Deterministic detectors raise anomaly cases from your audit events;
> the optional AI copilot only **explains and suggests** on sanitized prompts — no PII, no OTPs, and
> never a destructive action on its own.

## What it is

A two-layer security guard. The **deterministic** layer (`AnomalyDetector`) scans audit events and
opens `AnomalyCase` records for the patterns you configure — graded by `Severity`, typed by
`AnomalyType`, tracked through `CaseStatus`. The **optional** AI layer (`AiExplainer` over the
`AiClient` contract) adds human-readable explanations and suggested next steps, but only after the
`PromptSanitizer` strips PII and secrets from the prompt. The AI never decides; it advises, and a
human reviews.

## The problem it solves

Pure-AI security tooling is a trust and compliance problem: it's non-deterministic, it can leak PII
into a third-party model, and you can't audit *why* it acted. Pure rules, on the other hand, are
trustworthy but terse — an analyst still has to interpret the case. This package keeps the two roles
separate on purpose: **rules are the source of truth** and stay fully auditable, while the AI is a
clearly-bounded copilot that explains on sanitized input and is safe to turn off entirely.

## What you get

- **Deterministic detection** — `AnomalyDetector` raises `AnomalyCase` records you can trust and audit.
- **Typed, graded cases** — `AnomalyType`, `Severity` and `CaseStatus` enums give every case structure.
- **An optional AI copilot** — `AiExplainer` explains and suggests; bring any provider via the `AiClient` contract.
- **Privacy by construction** — `PromptSanitizer` removes PII/OTP before anything reaches a model.
- **Scheduled scans** — `DetectAnomaliesCommand` with configurable `ScheduleFrequency`.
- **Testable without a model** — `FakeAiClient` stands in for the AI in tests.

## When to use it

- You want **anomaly cases you can trust** — deterministic, auditable, not a black box.
- You'd like **AI-written explanations** of those cases without sending PII to a model.
- You need the AI to be **strictly advisory** — no autonomous destructive actions.
- You want anomaly detection to run on a **schedule** over your audit events.

## Worked example

```bash
composer require padosoft/laravel-rebel-ai-guard
php artisan vendor:publish
php artisan migrate
```

Run a detection pass on demand, or schedule it at the frequency configured in
`config/rebel-ai-guard.php`:

```bash
php artisan rebel:detect-anomalies
```

The AI copilot is entirely optional — bind your own implementation of the `AiClient` contract to
enable explanations, or leave it off and rely on the deterministic detector alone.

## How it fits

The AI guard reads the audit trail and metric buckets the rest of the suite already produces — it adds
detection and (optionally) explanation on top, writing `AnomalyCase` records that the admin API
surfaces for review. The admin panel works **without** it; turning it on enriches cases with
sanitized AI commentary, never with autonomous decisions.

Deterministic-first detection with a strictly-advisory, PII-safe AI copilot is a deliberately
different posture from "let the model decide" tools — see **[Why Rebel](/ecosystem/why-rebel)**.

---

## Reference

### Runtime files

- `src\Console\DetectAnomaliesCommand.php`
- `src\Contracts\AiClient.php`
- `src\Detection\AnomalyDetector.php`
- `src\Enums\AnomalyType.php`
- `src\Enums\CaseStatus.php`
- `src\Enums\Severity.php`
- `src\Models\AnomalyCase.php`
- `src\Support\PromptSanitizer.php`
- `src\Support\ScheduleFrequency.php`
- `src\Testing\FakeAiClient.php`
- `src\AiExplainer.php`
- `src\RebelAiGuardServiceProvider.php`

### Service providers

- `src\RebelAiGuardServiceProvider.php`

### Services and managers

- `src\RebelAiGuardServiceProvider.php`

### Contracts

- `src\Contracts\AiClient.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

- `src\Models\AnomalyCase.php`

### Config

- `config\rebel-ai-guard.php`

### Migrations

- `database\migrations\create_rebel_anomaly_cases_table.php`

### Routes

None detected in the package tree.

### Commands

- `src\Console\DetectAnomaliesCommand.php`

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

::: collapsible "Problem: keep laravel-rebel-ai-guard replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

### Test & verification surface

- `tests\Feature\AiExplainerTest.php`
- `tests\Feature\AnomalyDetectorTest.php`
- `tests\Feature\DetectAnomaliesCommandTest.php`
- `tests\Feature\PromptSanitizerTest.php`
- `tests\Feature\ScheduleFrequencyTest.php`
- `tests\Schedule\ScheduleFrequencyConfigTest.php`
- `tests\CronFrequencyTestCase.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
