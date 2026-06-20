# laravel-rebel-bridge-laragear-2fa

[GitHub repository](https://github.com/padosoft/laravel-rebel-bridge-laragear-2fa) · Composer package: `padosoft/laravel-rebel-bridge-laragear-2fa`

## Motivazione

Bridge between laragear/two-factor and Laravel Rebel: exposes TOTP as an AAL2 step-up driver, integrates recovery codes, and emits full audit telemetry into the Rebel audit trail. Part of padosoft/laravel-rebel-*.

This package participates in the Laravel Rebel ecosystem by contributing one bounded capability to the authentication control plane.

## Teoria

A Rebel package should expose a capability $C$ without redefining the global assurance model $A$. Formally, the package contributes evidence $e$ and configuration $k$:

$$
C(package)=f(e,k) \quad \text{while} \quad A \in core
$$

## Design + diagramma

```mermaid
flowchart LR
  App[Laravel app] --> Package[laravel-rebel-bridge-laragear-2fa]
  Package --> Core[laravel-rebel-core contracts]
  Package --> Config[Config / migrations / routes]
  Package --> Tests[Test suite]
  Core --> Audit[Audit and assurance]
```

## Modello dati / contratto

### Runtime files

- `src\Contracts\TwoFactorValidator.php`
- `src\Drivers\LaragearTotpStepUpDriver.php`
- `src\Support\LaragearBridge.php`
- `src\Support\LaragearTwoFactorValidator.php`
- `src\Testing\FakeTwoFactorValidator.php`
- `src\RebelLaragear2faBridgeServiceProvider.php`

### Service providers

- `src\RebelLaragear2faBridgeServiceProvider.php`

### Services and managers

- `src\RebelLaragear2faBridgeServiceProvider.php`

### Contracts

- `src\Contracts\TwoFactorValidator.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bridge-laragear-2fa.php`

### Migrations

None detected in the package tree.

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
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

## Development requirements

| Dependency | Constraint |
|---|---|
| `laragear/two-factor` | `^4.0` |
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

## ADR

::: collapsible "Problem: keep laravel-rebel-bridge-laragear-2fa replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Worked example

```bash
composer require padosoft/laravel-rebel-bridge-laragear-2fa
php artisan vendor:publish
php artisan migrate
```

## Test and verification surface

- `tests\Feature\FakeTwoFactorValidatorTest.php`
- `tests\Feature\LaragearTotpDriverTest.php`
- `tests\Feature\ServiceProviderTest.php`
- `tests\Fixtures\User.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
