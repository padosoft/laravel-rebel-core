# laravel-rebel-step-up

[GitHub repository](https://github.com/padosoft/laravel-rebel-step-up) · Composer package: `padosoft/laravel-rebel-step-up`

## Motivazione

Step-up authentication for Laravel Rebel: confirm an action/purpose with AAL/AMR assurance, risk-based, and PSD2/SCA dynamic linking. Part of padosoft/laravel-rebel-*.

This package participates in the Laravel Rebel ecosystem by contributing one bounded capability to the authentication control plane.

## Teoria

A Rebel package should expose a capability $C$ without redefining the global assurance model $A$. Formally, the package contributes evidence $e$ and configuration $k$:

$$
C(package)=f(e,k) \quad \text{while} \quad A \in core
$$

## Design + diagramma

```mermaid
flowchart LR
  App[Laravel app] --> Package[laravel-rebel-step-up]
  Package --> Core[laravel-rebel-core contracts]
  Package --> Config[Config / migrations / routes]
  Package --> Tests[Test suite]
  Core --> Audit[Audit and assurance]
```

## Modello dati / contratto

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

## Composer requirements

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
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

::: collapsible "Problem: keep laravel-rebel-step-up replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Worked example

```bash
composer require padosoft/laravel-rebel-step-up
php artisan vendor:publish
php artisan migrate
```

## Test and verification surface

- `tests\Feature\StepUpConfigValidatorTest.php`
- `tests\Feature\StepUpEmailDriverTest.php`
- `tests\Feature\StepUpManagerTest.php`
- `tests\Feature\StepUpMiddlewareTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
