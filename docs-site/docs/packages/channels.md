# laravel-rebel-channels

[GitHub repository](https://github.com/padosoft/laravel-rebel-channels) · Composer package: `padosoft/laravel-rebel-channels`

## Motivazione

Channel/provider abstraction (SMS/WhatsApp/voice) for Laravel Rebel: verification routing with fallback, cooldown, multi-dimensional rate limiting, and anti toll-fraud/IRSF defences. Part of padosoft/laravel-rebel-*.

This package participates in the Laravel Rebel ecosystem by contributing one bounded capability to the authentication control plane.

## Teoria

A Rebel package should expose a capability $C$ without redefining the global assurance model $A$. Formally, the package contributes evidence $e$ and configuration $k$:

$$
C(package)=f(e,k) \quad \text{while} \quad A \in core
$$

## Design + diagramma

```mermaid
flowchart LR
  App[Laravel app] --> Package[laravel-rebel-channels]
  Package --> Core[laravel-rebel-core contracts]
  Package --> Config[Config / migrations / routes]
  Package --> Tests[Test suite]
  Core --> Audit[Audit and assurance]
```

## Modello dati / contratto

### Runtime files

- `src\Contracts\MessageDeliveryChannel.php`
- `src\Contracts\VerificationProvider.php`
- `src\Enums\Channel.php`
- `src\Enums\DeliveryStatus.php`
- `src\Enums\VerificationStatus.php`
- `src\Fraud\FraudDecision.php`
- `src\Fraud\FraudGuard.php`
- `src\Results\DeliveryResult.php`
- `src\Results\VerificationResult.php`
- `src\Routing\DeliveryChannelRegistry.php`
- `src\Routing\ProviderRegistry.php`
- `src\Routing\VerificationRouter.php`
- `src\Support\CacheRateLimiter.php`
- `src\Support\NullBotProtection.php`
- `src\Testing\FakeMessageDeliveryChannel.php`
- `src\Testing\FakeVerificationProvider.php`
- `src\RebelChannelsServiceProvider.php`

### Service providers

- `src\Contracts\VerificationProvider.php`
- `src\Routing\ProviderRegistry.php`
- `src\Testing\FakeVerificationProvider.php`
- `src\RebelChannelsServiceProvider.php`

### Services and managers

- `src\Fraud\FraudGuard.php`
- `src\Routing\DeliveryChannelRegistry.php`
- `src\Routing\ProviderRegistry.php`
- `src\RebelChannelsServiceProvider.php`

### Contracts

- `src\Contracts\MessageDeliveryChannel.php`
- `src\Contracts\VerificationProvider.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-channels.php`

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

::: collapsible "Problem: keep laravel-rebel-channels replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Worked example

```bash
composer require padosoft/laravel-rebel-channels
php artisan vendor:publish
php artisan migrate
```

## Test and verification surface

- `tests\Feature\DeliveryChannelRegistryTest.php`
- `tests\Feature\FraudGuardTest.php`
- `tests\Feature\VerificationRouterTest.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
