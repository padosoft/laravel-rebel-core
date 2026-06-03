# Laravel Rebel — Core

> **The heart of the `padosoft/laravel-rebel-*` ecosystem: an enterprise authentication _control plane_ for Laravel** (passwordless OTP, passkey-first, risk-based step-up, SCA, multi-tenant, admin, AI). This `-core` package contains the shared "building blocks" (value objects, contracts, assurance, audit, hashing) that all the others rest on.

<p align="center">
  <img src="resources/screenshoots/Laravel-Rebel-banner.png" alt="Laravel Rebel" width="100%">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12%20%7C%2013-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12|13">
  <img src="https://img.shields.io/badge/PHP-8.3%20%7C%208.4%20%7C%208.5-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/PHPStan-max-2A6FDB?style=flat-square" alt="PHPStan max">
  <img src="https://img.shields.io/badge/tests-Pest%204-22C55E?style=flat-square" alt="Pest 4">
  <img src="https://img.shields.io/badge/license-MIT-blue?style=flat-square" alt="MIT">
</p>

<p align="center"><strong>If this is the first time you see this project, start here: this README explains the WHOLE ecosystem to you.</strong></p>

---

## Table of contents

- [What Laravel Rebel is (in 1 minute)](#what-laravel-rebel-is-in-1-minute)
- [Glossary (for those who are not auth experts)](#glossary-for-those-who-are-not-auth-experts)
- [The suite: all the packages](#the-suite-all-the-packages)
- [How they fit together (dependency DAG)](#how-they-fit-together-dependency-dag)
- [Web Admin Panel](#web-admin-panel)
- [What this package does (`-core`)](#what-this-package-does--core)
- [Why Rebel Core vs. the alternatives](#why-rebel-core-vs-the-alternatives)
- [End-to-end flows (narrated examples)](#end-to-end-flows-narrated-examples)
- [Installation (junior-proof)](#installation-junior-proof)
- [Configuration (every option explained)](#configuration-every-option-explained)
- [Usage examples](#usage-examples)
- [Compliance](#compliance)
- [Testing](#testing)
- [License](#license)

---

## What Laravel Rebel is (in 1 minute)

Laravel already has **Fortify** (login, registration, password reset, TOTP 2FA, passkey). Rebel **does not replace it**: it sits _on top_ and adds what an **enterprise/ecommerce** product needs:

- **passwordless login** (Shopify-style email-OTP) and **passkey-first** (the most secure);
- **step-up**: re-prompting for a strong confirmation only for **sensitive actions** (change email, credit order, download invoice…), with a security level appropriate to the risk;
- **SCA / PSD2 dynamic linking** for payments / credit orders in the EU;
- SMS/WhatsApp/voice **channels** with anti-fraud defenses;
- **multi-tenant**, **audit**, **web admin panel**, **AI guard**.

Everything is split into **small, composable packages**: you use only what you need.

> In one line: **Rebel turns Laravel Fortify into an enterprise authentication control plane.**

---

## Glossary (for those who are not auth experts)

| Term | Plain meaning |
|---|---|
| **OTP** | "One-Time Password": a single-use code (e.g. 6 digits) sent via email/SMS. |
| **Passwordless** | Login without a password: you prove it's you with an OTP or a passkey. |
| **Passkey / FIDO2 / WebAuthn** | A cryptographic credential bound to your device (fingerprint/Face ID/key). It is **phishing-resistant**. |
| **Phishing-resistant** | Not replayable on a scam site. Only passkeys are (OTP/SMS are not). |
| **Step-up** | Raising the verification level for a single sensitive action, not for the whole login. |
| **AAL (1/2/3)** | "Authenticator Assurance Level" (NIST): how strong the authentication is. AAL1 = 1 factor, AAL2 = 2 factors, AAL3 = hardware. |
| **AMR** | "Authentication Methods References": _how_ you authenticated (e.g. `['webauthn']`, `['otp','email']`). |
| **Dynamic linking (PSD2/SCA)** | The payment confirmation is bound to **amount + payee**: if they change, it expires. |
| **Pepper** | A server-side secret key used for the HMACs (so stored email/IP are not reversible). |
| **Tenant** | A "tenant" in a multi-customer system (e.g. site/brand/country of an ecommerce). |

---

## The suite: all the packages

| Package | What it's for | What it does NOT do |
|---|---|---|
| **`laravel-rebel-core`** (this one) | Value objects, contracts, assurance, audit, shared hashing | No routes/UI; no Fortify/Twilio/AI |
| `laravel-rebel-email-otp` | Passwordless email-OTP login (web + mobile/Sanctum) | Does not handle SMS (see channels) |
| `laravel-rebel-bridge-fortify` | Uses Fortify (password-confirm, passkey, TOTP) as a driver + passkey-first login | Does not reimplement Fortify |
| `laravel-rebel-step-up` | Step-up per action/purpose, risk-based, with SCA dynamic linking | It is not the login (it's the confirmation of an action) |
| `laravel-rebel-channels` | Channel/provider abstraction + anti toll-fraud/IRSF + bot gate | Not tied to a specific provider |
| `laravel-rebel-channel-twilio` | Twilio provider (SMS/WhatsApp/Voice, Verify, webhook) | — |
| `laravel-rebel-recovery` | High-assurance account recovery + recovery codes | — |
| `laravel-rebel-sessions` | Device/session, "log out everywhere", refresh rotation | — |
| `laravel-rebel-admin-api` | JSON API control plane (metrics, audit, anomalies) | No UI (API only) |
| `laravel-rebel-admin` | **Web Admin Panel** (Blade + AJAX + vanilla JS) | — |
| `laravel-rebel-ai-guard` | Anomaly detection + AI copilot (explains, does not decide) | Does not make destructive decisions on its own |
| `laravel-rebel-auth` | Meta-package: installs the recommended bundle | No business logic |

---

## How they fit together (dependency DAG)

```text
                         laravel-rebel-core
                          (common language)
        ┌───────────┬───────────┬─────────────┬───────────────┐
        ▼           ▼           ▼             ▼               ▼
   email-otp     channels    step-up   sessions/recovery   admin-api
        │           │           ▲                              │
        │           └──► channel-twilio                        │
        │                       │                              ▼
        └───────────────► bridge-fortify                     admin (web panel)
                                                                │
                                          ai-guard ── reads ────┘ (buckets/metrics)

Install order: core → email-otp → bridge-fortify → step-up →
channels (+twilio) → admin-api → admin → sessions/recovery → ai-guard.
```

Rules: the **core does not depend** on Fortify/Twilio/AI. The **admin works without ai-guard**. The `fortify_password_confirm` is **web-only** (mobile uses a token-native step-up).

---

## Web Admin Panel

The suite includes a **web administration panel** (package `laravel-rebel-admin`) — Blade + AJAX + vanilla JS, with **no** mandatory JS framework — to monitor login/OTP/step-up, provider health, audit, anomalies and compliance.

<p align="center">
  <img src="resources/screenshoots/Laravel-Rebel-Web-Panel-dasboard-dark.png" alt="Laravel Rebel — Web Admin Panel" width="100%">
</p>

---

## What this package does (`-core`)

The core is **small and stable**: it defines the shared "language". It contains:

- **Identifiers** — `EmailIdentifier`, `PhoneIdentifier`, `GenericIdentifier`: normalize and mask email/phone.
- **Keyed hashing** — `KeyedHasher`/`HmacKeyedHasher`: HMAC with a **versioned pepper** and rotation (for email/IP/OTP).
- **Assurance** — `Aal`, `AssuranceLevel`: the security model that prevents, for example, an email-OTP (AAL1) from "covering" an action that requires a passkey.
- **Context** — `SecurityContext`, `TenantContext`, `DeviceContext`: the context of a request (IP/UA already hashed).
- **Risk** — `RiskAssessment`, `RiskLevel`, `RecommendedAction`.
- **Auth** — `LoginResult` (web|token), `TokenPair` (Sanctum access+refresh).
- **Audit** — `AuditEvent`, `DatabaseAuditLogger` (+ `rebel_auth_events` table), `Redactor` (never OTP/secret in the logs).
- **Contracts** — `TokenIssuer`, `SubjectResolver`, `TenantResolver`, `RiskEvaluator`, `AuditLogger`, `SessionRegistry`, `DeviceTrust`, `BotProtection`, `RateLimiter`, `Clock` (PSR-20).
- **Tenancy** — `CurrentTenant` + `BelongsToTenant` trait (per-tenant isolation).
- **Config** — `php artisan rebel:validate-config` command (fail-fast in CI).

---

## Why Rebel Core vs. the alternatives

There is no drop-in package that gives you a **shared auth "core" / contracts layer** with first-class NIST assurance, keyed hashing with rotation and built-in audit redaction. The realistic alternatives are: building these primitives **by hand**, relying on **framework-native** auth only, or pulling in a heavier all-in-one bundle. Here is how they compare for a shared core that the rest of an auth suite can build on.

| Capability | **Rebel Core** | Shopify | Hand-rolled primitives | Fortify (framework-native) | Spatie permission/multitenancy |
|---|:---:|:---:|:---:|:---:|:---:|
| First-class NIST AAL/AMR assurance model | ✅ | ❌ | ❌ | ❌ | ❌ |
| `satisfies()` guard (blocks email-OTP on phishing-resistant purposes) | ✅ | ❌ | ❌ | ❌ | ❌ |
| Keyed HMAC hashing with **versioned pepper + rotation** | ✅ | ❌ | ❌ | ❌ | ❌ |
| Audit trail with **automatic secret redaction** | ✅ | ➖ | ❌ | ❌ | ❌ |
| GDPR-safe IP/UA stored as keyed HMAC (never cleartext) | ✅ | ❌ | ❌ | ❌ | ❌ |
| Web/mobile `LoginResult` + Sanctum `TokenPair` contract | ✅ | ❌ | ❌ | ❌ | ❌ |
| Per-tenant isolation trait + safe queue worker reset | ✅ | ❌ | ❌ | ❌ | ✅ |
| PSR-20 testable `Clock` for OTP/step-up expirations | ✅ | ❌ | ❌ | ❌ | ❌ |
| Stable contracts to swap implementations (channels, risk, sessions) | ✅ | ❌ | ❌ | ❌ | ❌ |
| `validate-config` fail-fast command for CI | ✅ | ❌ | ❌ | ❌ | ❌ |
| Zero hard dependency on Fortify/Twilio/AI | ✅ | ➖ | ✅ | ❌ | ✅ |
| Login/registration/password-reset screens | ❌ (by design) | ✅ | ❌ | ✅ | ❌ |

> Legend: ✅ built-in · ➖ partial / hosted-only / not exposed to you · ❌ not available.
> Note on Shopify: it is a **hosted, closed commerce platform** — you can't self-host it, extend its auth internals, or reuse these low-level primitives in your own Laravel app; it's a black box you don't control, so most developer-facing rows are ❌/➖.

> Honest take: Fortify and the Spatie packages are excellent at what they do — Fortify ships the actual auth screens, Spatie handles permissions/multitenancy. Rebel Core is **not** competing on those; it provides the assurance/audit/hashing/contracts substrate they don't, and it stays unopinionated so you can layer the rest of the suite (or Fortify itself, via `bridge-fortify`) on top.

---

## End-to-end flows (narrated examples)

**1) Passwordless login (ecommerce customer)**
```text
user enters their email
  → Rebel creates an OTP challenge, sends the code (anti-enumeration: always the same response)
  → user enters the code
  → atomic verification (single-use) → login
       web    → session + cookie
       mobile → Sanctum TokenPair (access + refresh)
  → audit: email_otp.verified (aal1, amr ['otp','email'])
```

**2) B2B credit order (step-up + SCA)**
```text
user clicks "Confirm credit order €1,250 → ACME Srl"
  → middleware rebel.stepup:checkout-credit-order
  → the request requires AAL2 phishing-resistant → passkey preferred
  → the confirmation is BOUND to amount+payee (dynamic linking):
       if the cart total changes → the confirmation expires → re-authenticate
  → action executed, audit with aal/amr and binding
```

**3) Account recovery (the most delicate point)**
```text
user has lost access
  → recovery is NOT "email a code": it is a step-up at HIGHER assurance than login
  → single-use recovery code + optional identity verification
```

---

## Installation (junior-proof)

> You usually don't install `-core` on its own: it comes as a dependency of the other packages. But you can use it stand-alone for its value objects/contracts.

**1. Require the package**
```bash
composer require padosoft/laravel-rebel-core
```

**2. Publish the config (optional)**
```bash
php artisan vendor:publish --tag=rebel-core-config
```

**3. Set the pepper in `.env`** (secret key for the HMACs)
```dotenv
# generate a strong value:  php -r "echo bin2hex(random_bytes(32));"
REBEL_PEPPER_V1=paste-here-a-long-random-value
REBEL_PEPPER_CURRENT=1
```

**4. (If you use the audit) run the migrations**
```bash
php artisan migrate
```

**5. Validate the config**
```bash
php artisan rebel:validate-config
# -> "Configurazione Rebel valida."  (exit 0)
```

Done. Now the contracts/value objects are available.

---

## Configuration (every option explained)

File: `config/rebel-core.php`

| Key | Default | What it does | When to change it |
|---|---|---|---|
| `peppers` | `[1 => env('REBEL_PEPPER_V1')]` | Map `version => secret` for the HMACs | Add a version to **rotate** the pepper |
| `pepper_current` | `1` | Version used for **new** hashes | When you rotate, set the new version |
| `hmac_algo` | `sha256` | HMAC algorithm | Rarely; it must be supported by PHP |
| `hash_ip` | `true` | Stores the IP as an HMAC (never in cleartext) | Leave it `true` for GDPR |
| `hash_user_agent` | `true` | Stores the User-Agent as an HMAC | Leave it `true` for GDPR |
| `audit.mode` | `sync` | How events are written: `sync` (inline) or `queue` (a job per event) | Set `queue` for high volume / enterprise |
| `audit.connection` / `audit.queue` | `null` | Queue connection + name for `mode=queue` (Horizon-compatible) | Point at a dedicated audit queue |
| `geo.enabled` | `true` | Record the request country on every event | Disable if you don't want geo |
| `geo.country_header` | `CF-IPCountry` | Request header the country is read from | Point at your proxy's header (Cloudflare sets `CF-IPCountry`) |

**Audit dispatch (sync vs queued) — capture is turnkey, you choose how/where.** Every package in
the suite records through the **same** `AuditLogger` contract, which **always persists** to
`rebel_auth_events` (never just in session). By default the write is synchronous; for high-volume
/ enterprise workloads, switch to a queued write (works directly with **Horizon** and any Laravel
queue) — the event is fully enriched (country, etc.) before it is queued, so nothing is lost:

```php
// config/rebel-core.php
'audit' => [
    'mode' => 'queue',                 // 'sync' (default) | 'queue'
    'connection' => 'redis',           // null = the app's default queue connection
    'queue' => 'rebel-audit',          // a dedicated queue is nice for Horizon dashboards
],
```

The **destination** is the bound `AuditLogger` / `DatabaseAuditLogger` (table + DB connection) —
rebind it to send the trail anywhere you like.

**Country enrichment.** The audit records the request country (ISO 3166-1 alpha-2), read from a
request header. Put **Cloudflare** in front and it sets `CF-IPCountry` for you (clean, accurate,
no extra service); behind another proxy, point `geo.country_header` at whatever it sets.

**Pepper rotation (example):**
```php
// config/rebel-core.php
'peppers' => [
    1 => env('REBEL_PEPPER_V1'),
    2 => env('REBEL_PEPPER_V2'), // new secret
],
'pepper_current' => 2, // new hashes use v2; the old ones (v1) remain verifiable
```

---

## Usage examples

**Identifiers (normalization + masking)**
```php
use Padosoft\Rebel\Core\Identifiers\EmailIdentifier;
use Padosoft\Rebel\Core\Identifiers\PhoneIdentifier;

$email = EmailIdentifier::from('  Mario.Rossi@Example.IT ');
$email->normalized(); // "mario.rossi@example.it"  (for lookup/HMAC)
$email->masked();     // "m***@example.it"          (for UI/log)

PhoneIdentifier::from('+39 328 000 0000')->normalized(); // "+393280000000"
```

**Keyed hashing (with rotation)**
```php
use Padosoft\Rebel\Core\Contracts\KeyedHasher;

$hasher = app(KeyedHasher::class);
$h = $hasher->hash($email->normalized());   // HashedValue(hash, keyVersion)
$hasher->matches($email->normalized(), $h->hash, $h->keyVersion); // true (constant-time)
```

**Assurance (the central security rule)**
```php
use Padosoft\Rebel\Core\Assurance\Aal;
use Padosoft\Rebel\Core\Assurance\AssuranceLevel;

$emailOtp = new AssuranceLevel(Aal::Aal1, phishingResistant: false, amr: ['otp', 'email']);
$passkey  = new AssuranceLevel(Aal::Aal2, phishingResistant: true,  amr: ['webauthn']);

// An action that requires AAL2 phishing-resistant:
$emailOtp->satisfies(Aal::Aal2, requirePhishingResistant: true); // false  ← email-OTP is not enough
$passkey->satisfies(Aal::Aal2, requirePhishingResistant: true);  // true
```

**SecurityContext from a request**
```php
use Padosoft\Rebel\Core\Context\SecurityContext;
use Padosoft\Rebel\Core\Contracts\KeyedHasher;

$ctx = SecurityContext::fromRequest($request, app(KeyedHasher::class))
    ->withGuard('customers')
    ->withPurpose('customer-login')
    ->withIdentifier($email);
// $ctx->ipHmac / $ctx->userAgentHash are already hashed (never in cleartext)
```

**Audit (with automatic secret redaction)**
```php
use Padosoft\Rebel\Core\Audit\AuditEvent;
use Padosoft\Rebel\Core\Audit\AuthEventType;
use Padosoft\Rebel\Core\Contracts\AuditLogger;

app(AuditLogger::class)->record(new AuditEvent(
    type: AuthEventType::EmailOtpVerified->value,
    guard: 'customers',
    identifierHmac: $h->hash, keyVersion: $h->keyVersion,
    purpose: 'customer-login',
    aal: Aal::Aal1, amr: ['otp', 'email'],
    metadata: ['otp' => '123456'], // ← will be stored as "[REDACTED]"
));
```

**Tenancy (per-tenant isolation)**
```php
use Padosoft\Rebel\Core\Tenancy\CurrentTenant;

app(CurrentTenant::class)->set('site-it'); // usually done by the TenantResolver/middleware
// All Rebel models with BelongsToTenant now filter and stamp tenant_id = 'site-it'
```

**Testable time (PSR-20 Clock)**
```php
use Padosoft\Rebel\Core\Clock\FakeClock;
use Psr\Clock\ClockInterface;

$clock = new FakeClock(new DateTimeImmutable('2026-01-01 10:00:00'));
app()->instance(ClockInterface::class, $clock);
$clock->advance(600); // simulates +10 minutes (useful to test OTP/step-up expirations)
```

---

## Compliance

Rebel is designed _by-design_ on recognized standards (details in the ADRs/`docs/`):

- **NIST 800-63B-4** — AAL/AMR model; email-OTP = AAL1; SMS = "restricted"; only passkeys are phishing-resistant.
- **PSD2 / SCA** — dynamic linking for B2B credit orders (does not replace the PSP's 3DS2 for cards).
- **GDPR** — IP/identifiers as keyed HMAC + `key_version` (rotation), log redaction, no PII in cleartext.

See `docs/adr/ADR-0005-design-lock.md`.

---

## 🔋 Vibe coding with batteries included

This package ships **AI batteries** — so you (and your AI agent) can extend it correctly on the
first try:

- **`CLAUDE.md`** — a concise AI working guide (purpose, conventions, architecture, how to extend,
  Definition of Done). Plain Markdown, so Claude Code, Cursor, Copilot and Codex all read it.
- **`AGENTS.md`** — the agent/workflow contract (branch → PR → CI → tag/release, the gates).
- **`.claude/skills/`** — invocable skills (at least `rebel-package-dev`) encoding the suite's
  TDD loop, the **PHPStan-level-max** recipes, the security/telemetry rules, and the release
  discipline.

Open the repo in your AI editor and just start — the rules, guardrails and extension recipes come
with it. PRs that follow the shipped `CLAUDE.md` pass CI (PHPStan max + Pest + Pint) and review the
first time around.

---

## Testing

```bash
composer test       # Pest
composer phpstan    # static analysis (max level)
composer pint       # code style
```

---

## License

MIT — see [LICENSE](LICENSE). © Padosoft.
