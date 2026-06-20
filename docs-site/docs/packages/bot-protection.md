# laravel-rebel-bot-protection

[GitHub repository](https://github.com/padosoft/laravel-rebel-bot-protection) · Composer package: `padosoft/laravel-rebel-bot-protection`

## Motivazione

Pluggable anti-bot / CAPTCHA gate for Laravel Rebel: server-side verification of Cloudflare Turnstile, Google reCAPTCHA v3 and hCaptcha tokens, fail-closed by default and fully audited. Part of padosoft/laravel-rebel-*.

This package participates in the Laravel Rebel ecosystem by contributing one bounded capability to the authentication control plane.

## Teoria

A Rebel package should expose a capability $C$ without redefining the global assurance model $A$. Formally, the package contributes evidence $e$ and configuration $k$:

$$
C(package)=f(e,k) \quad \text{while} \quad A \in core
$$

## Design + diagramma

```mermaid
flowchart LR
  App[Laravel app] --> Package[laravel-rebel-bot-protection]
  Package --> Core[laravel-rebel-core contracts]
  Package --> Config[Config / migrations / routes]
  Package --> Tests[Test suite]
  Core --> Audit[Audit and assurance]
```

## Modello dati / contratto

### Runtime files

- `src\Contracts\CaptchaVerifier.php`
- `src\Gateway\HttpCaptchaVerifier.php`
- `src\Providers\AbstractCaptchaBotProtection.php`
- `src\Providers\AlwaysPassBotProtection.php`
- `src\Providers\HcaptchaBotProtection.php`
- `src\Providers\RecaptchaBotProtection.php`
- `src\Providers\TurnstileBotProtection.php`
- `src\Testing\FakeCaptchaVerifier.php`
- `src\Verification\VerificationResult.php`
- `src\RebelBotProtectionServiceProvider.php`

### Service providers

- `src\Providers\AbstractCaptchaBotProtection.php`
- `src\Providers\AlwaysPassBotProtection.php`
- `src\Providers\HcaptchaBotProtection.php`
- `src\Providers\RecaptchaBotProtection.php`
- `src\Providers\TurnstileBotProtection.php`
- `src\RebelBotProtectionServiceProvider.php`

### Services and managers

- `src\Contracts\CaptchaVerifier.php`
- `src\Gateway\HttpCaptchaVerifier.php`
- `src\Testing\FakeCaptchaVerifier.php`
- `src\RebelBotProtectionServiceProvider.php`

### Contracts

- `src\Contracts\CaptchaVerifier.php`

### Controllers

None detected in the package tree.

### Middleware

None detected in the package tree.

### Models

None detected in the package tree.

### Config

- `config\rebel-bot-protection.php`

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

::: collapsible "Problem: keep laravel-rebel-bot-protection replaceable"
Decision: document its public responsibility and use Rebel core contracts at integration boundaries.

Consequences: applications can adopt the package without coupling every other Rebel module to its internals.
:::

::: collapsible "Problem: package-specific behavior must remain auditable"
Decision: all security-significant outcomes should emit or feed audit events through the core vocabulary.

Consequences: admin API, admin UI and AI guard can reason across packages without bespoke parsers for every provider.
:::

## Worked example

```bash
composer require padosoft/laravel-rebel-bot-protection
php artisan vendor:publish
php artisan migrate
```

## Test and verification surface

- `tests\Feature\AlwaysPassBotProtectionTest.php`
- `tests\Feature\CaptchaProviderTest.php`
- `tests\Feature\DriverSelectionTest.php`
- `tests\Feature\HttpCaptchaVerifierTest.php`
- `tests\Live\CaptchaLiveTest.php`
- `tests\Support\SpyAuditLogger.php`
- `tests\Pest.php`
- `tests\TestCase.php`

::: callout warning
Do not copy internal test-only classes into an application. Treat file lists as a source map for maintainers and auditors, not as an installation recipe by themselves.
:::
