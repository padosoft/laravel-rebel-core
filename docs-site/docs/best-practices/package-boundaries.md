---
title: Package Boundaries
description: How to compose the Laravel Rebel suite correctly — depend on contracts not internals, keep volatile integrations at the leaves, install only what you need, and extend by binding a contract in the container.
---

# Package Boundaries

> Rebel is a suite, not a monolith. The whole design rests on one idea: **the shared language lives in
> the core, capabilities live in narrow packages, and the volatile bits live at the leaves.** Compose
> along those seams and your app stays small, testable and cheap to change.

The suite is layered so that a change in a noisy place — an SMS provider's API, a passkey library, an
AI model — cannot ripple into your policy code. `laravel-rebel-core` defines the vocabulary
(assurance, audit, hashing, contracts) and **depends on nothing else in the suite**. Feature packages
each own one narrow capability. Provider integrations sit furthest out, behind an abstraction, so the
blast radius of any one of them is a single leaf.

---

## The three rules

::: grids
::: grid
::: card "Depend on contracts" icon:plug
Reach for a core **contract**, never a concrete class in another package's `src/`. Internals are free
to change between patch releases; contracts are the stable promise.
:::
::: card "Volatile at the leaves" icon:leaf
SMS providers, passkey libraries and AI live as far out as possible, behind the `channels`
abstraction. Swap a provider and your call sites never move.
:::
::: card "Install only what you need" icon:package
The admin works without `ai-guard`. Don't pull a package for a capability you aren't shipping — every
dependency is surface area you have to keep green.
:::
:::
:::

---

## Do / Don't

| Do | Don't |
|---|---|
| Type-hint a core contract and let the container resolve it. | `new` a concrete logger, resolver or hasher from another package. |
| Bind your implementation in a service provider. | Subclass an internal class to "borrow" behavior. |
| Route provider calls through the `channels` abstraction. | Call a provider SDK directly from a controller. |
| Keep `ai-guard` optional — AI **explains**, it never **decides**. | Make a security decision depend on an AI package being installed. |
| Keep `^0.1` constraints on suite packages while in `0.x`. | Float to `^0.2` — Composer would silently break dependents. |

::: callout info
`fortify_password_confirm` is **web-only**. On mobile/token flows, do step-up natively with tokens —
don't try to reuse the web confirm path. See [Gotchas & Limits](/best-practices/gotchas-limits).
:::

---

## Extend by binding a contract

Almost every extension point is "implement a core contract and bind it." You don't fork a package; you
hand the container your version.

| Contract | Bind it to… |
|---|---|
| `AuditLogger` | Ship security events to a SIEM or data lake; decorate or queue the write. |
| `TokenIssuer` | Mint your own Sanctum access + refresh pair (`TokenPair`). |
| `SubjectResolver` | Map a credential/identifier to your application's user model. |
| `TenantResolver` | Resolve the current tenant from host, header, claim or path. |
| `RiskEvaluator` | Plug your risk engine and return a recommended action. |
| `SessionRegistry` | Track and revoke active sessions your way. |
| `DeviceTrust` | Decide whether a device is known/trusted. |
| `BotProtection` | Wire your CAPTCHA / attestation provider. |
| `RateLimiter` | Apply your own throttle policy. |
| `KeyedHasher` | Change the keying/peppering strategy (keep constant-time compare). |
| `Clock` | Inject time (PSR-20) — essential for deterministic tests. |

```php
// In your app's service provider — swap the audit sink without touching call sites.
$this->app->bind(\Padosoft\Rebel\Core\Contracts\AuditLogger::class, SiemAuditLogger::class);
```

::: callout tip
Each contract ships a sane default, so you only bind the ones you actually need to change. Read the
ownership map in the **[Dependency Graph](/ecosystem/dependency-graph)** and the contract shapes in
**[Data Model & Contracts](/architecture/data-model-contract)**.
:::

---

## Why it pays off

A delivery receipt from an SMS provider is **not** an authentication event — keeping that provider at a
leaf, behind `channels`, is what stops a delivery concern from leaking into your auth policy. The same
discipline keeps `ai-guard` removable, keeps the admin independent, and keeps the core small enough
that 20+ packages can rely on it without fear.
