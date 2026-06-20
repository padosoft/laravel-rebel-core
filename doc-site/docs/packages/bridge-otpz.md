# laravel-rebel-bridge-otpz

[GitHub repository](https://github.com/padosoft/laravel-rebel-bridge-otpz) · Composer package: `padosoft/laravel-rebel-bridge-otpz`

## Motivazione

Bridge the benbjurstrom/otpz email one-time-password package into Laravel Rebel step-up. Exposes OTP email magic-code as a step-up driver (AAL2, AMR otp). Part of padosoft/laravel-rebel-*.

This package participates in the Laravel Rebel ecosystem by contributing one bounded capability to the authentication control plane.

## Teoria

A Rebel package should expose a capability $C$ without redefining the global assurance model $A$. Formally, the package contributes evidence $e$ and configuration $k$:

$$
C(package)=f(e,k) \quad \text{while} \quad A \in core
$$

## Design + diagramma

```mermaid
flowchart LR
  App[Laravel app] --> Package[laravel-rebel-bridge-otpz]
  Package --> Core[laravel-rebel-core contracts]
  Package --> Config[Config / migrations / routes]
  Package --> Tests[Test suite]
  Core --> Audit[Audit and assurance]
```

## Modello dati / contratto

### Runtime files

- `src\Contracts\OtpzBroker.php`
- `src\Drivers\OtpzStepUpDriver.php`
- `src\Testing\FakeOtpzBroker.php`
- `src\OtpzBrokerImpl.php`
- `src\RebelOtpzBridgeServiceProvider.php`

### Service providers

- `src\RebelOtpzBridgeServiceProvider.php`

### Services and managers

- `src\Contracts\OtpzBroker.php`
- `src\Testing\FakeOtpzBroker.php`
- `src\OtpzBrokerImpl.php`
- `src\RebelOtpzBridgeServiceProvider.php`

### Contracts

- `src\Contracts\OtpzBroker.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bridge-otpz.php`

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
| `benbjurstrom/otpz` | `^0.7.0` |
| `larastan/larastan` | `^3.0` |
| `laravel/pint` | `^1.18` |
| `orchestra/testbench` | `^10.0|^11.0` |
| `pestphp/pest` | `^4.0` |
| `pestphp/pest-plugin-laravel` | `^4.0` |

## ADR

::: collapsible "Problem: keep laravel-rebel-bridge-otpz replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Worked example

```bash
composer require padosoft/laravel-rebel-bridge-otpz
php artisan vendor:publish
php artisan migrate
```

## Test and verification surface

- `tests\Feature\OtpzDriverTest.php`
- `tests\Fixtures\User.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
