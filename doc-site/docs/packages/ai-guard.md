# laravel-rebel-ai-guard

[GitHub repository](https://github.com/padosoft/laravel-rebel-ai-guard) · Composer package: `padosoft/laravel-rebel-ai-guard`

## Motivazione

Anomaly detection + AI security copilot for Laravel Rebel: deterministic rules detect anomaly cases; the optional AI only explains/suggests (sanitized prompts, no PII/OTP, human review). Part of padosoft/laravel-rebel-*.

This package participates in the Laravel Rebel ecosystem by contributing one bounded capability to the authentication control plane.

## Teoria

A Rebel package should expose a capability $C$ without redefining the global assurance model $A$. Formally, the package contributes evidence $e$ and configuration $k$:

$$
C(package)=f(e,k) \quad \text{while} \quad A \in core
$$

## Design + diagramma

```mermaid
flowchart LR
  App[Laravel app] --> Package[laravel-rebel-ai-guard]
  Package --> Core[laravel-rebel-core contracts]
  Package --> Config[Config / migrations / routes]
  Package --> Tests[Test suite]
  Core --> Audit[Audit and assurance]
```

## Modello dati / contratto

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

## Composer requirements

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

## Development requirements

| Dependency | Constraint |
|---|---|
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

## ADR

::: collapsible "Problem: keep laravel-rebel-ai-guard replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Worked example

```bash
composer require padosoft/laravel-rebel-ai-guard
php artisan vendor:publish
php artisan migrate
```

## Test and verification surface

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
