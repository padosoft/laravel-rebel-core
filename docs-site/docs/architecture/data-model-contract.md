---
title: Data Model & Contracts
description: The Laravel Rebel data model — the rebel_auth_events audit schema, the immutable value objects (AssuranceLevel, HashedValue, SecurityContext, LoginResult, TokenPair, RiskAssessment), the contract interfaces that act as integration seams, and per-tenant isolation via BelongsToTenant.
---

# Data Model & Contracts

> Rebel's surface area is intentionally thin: a single audit table, a handful of **immutable value
> objects**, and a set of **contracts** that act as the seams where you bind your own infrastructure.
> Understand these and you understand the whole control plane — everything else is composition.

## The audit table: `rebel_auth_events`

Every security-significant outcome lands in one append-only table. It is the system of record an
auditor or a SOC analyst reads, so each column earns its place.

| Field | Why it exists |
|---|---|
| `type` | What happened — login, OTP verified, step-up denied. Drives dashboards and anomaly rules. |
| `guard` | Which auth guard the event belongs to (e.g. `customers`, `admins`). Keeps multi-guard apps separable. |
| `identifierHmac` | The actor's identifier as a **keyed HMAC** — never the cleartext email/phone (GDPR). |
| `keyVersion` | Which pepper version produced the HMAC, so values stay comparable across **rotation**. |
| `purpose` | The business intent (`customer-login`, `change-payout-account`). Gives every event context. |
| `aal` | The NIST assurance level in force when the event occurred. |
| `amr` | The methods used (`otp`, `email`, `webauthn`). Lets you prove *how* the actor authenticated. |
| `metadata` | Free-form detail, **redacted** before write — secrets never reach this column. |
| `country` | Derived from `CF-IPCountry`. Enables geo anomaly detection without storing the IP. |
| `created_at` / timestamps | When it happened — the spine of any audit timeline. |

::: callout info
The table is reached through the `AuditLogger` contract and modeled by `RebelAuthEvent`. It is
**never** written to the session, and dispatch can be synchronous or queued (`audit.mode`).
:::

## Value objects — immutable, `final`, typed

The vocabulary that flows through the [pipeline](/architecture/pipeline-workflow) is expressed as
small immutable objects. They make illegal states unrepresentable and carry meaning the type system
can enforce.

::: grids

::: grid
::: card "AssuranceLevel" icon:shield
NIST AAL plus AMR and a phishing-resistance flag. Its `satisfies(Aal, requirePhishingResistant)`
guard is the central security rule — email-OTP (AAL1) cannot cover an action needing AAL2
phishing-resistant.
:::
:::

::: grid
::: card "HashedValue" icon:lock
A keyed HMAC `(hash, keyVersion)`. Produced by `HmacKeyedHasher` (HMAC-SHA256, versioned pepper),
compared in constant time. The reason no PII is ever stored in cleartext.
:::
:::

::: grid
::: card "SecurityContext" icon:globe
The request, reduced and privacy-safe: IP and User-Agent already hashed, country resolved. The single
input the risk and assurance stages read from.
:::
:::

::: grid
::: card "RiskAssessment" icon:activity
A risk level plus a recommended action, produced by the `RiskEvaluator`. It is what tips a decision
from *Allow* toward *Step-up* or *Deny*.
:::
:::

::: grid
::: card "LoginResult" icon:key
The envelope a successful login returns: a **web session** or a token result. Same decision logic,
two shapes.
:::
:::

::: grid
::: card "TokenPair" icon:repeat
A Sanctum **access + refresh** pair for API and mobile callers, minted by the `TokenIssuer` contract.
:::
:::

:::

## Contracts — the integration seams

Each boundary in the control plane is an interface bound in the container. Every one ships a default
and is meant to be **swapped per application**.

| Contract | Responsibility | Default impl | Why you'd swap it |
|---|---|---|---|
| `AuditLogger` | Persist security events. | `DatabaseAuditLogger` (→ `rebel_auth_events`) | Ship events to a SIEM or data lake; decorate (`ContextEnrichingAuditLogger`) or queue (`QueuedAuditLogger`). |
| `KeyedHasher` | Key/pepper PII into `HashedValue`. | `HmacKeyedHasher` | Change the keying/peppering strategy; preserve constant-time compare. |
| `RiskEvaluator` | Score a request's risk. | ships a default | Plug in proprietary signals (velocity, device reputation, geo). |
| `TokenIssuer` | Mint Sanctum `TokenPair`s. | ships a default | Custom token lifetimes, claims or storage. |
| `SubjectResolver` | Identify the actor. | ships a default | Non-standard user models or federated identity. |
| `TenantResolver` | Resolve the active tenant. | ships a default | Host-, header- or claim-based tenancy. |
| `SessionRegistry` | Track active sessions. | ships a default | Centralized session revocation, device lists. |
| `DeviceTrust` | Judge device trust. | ships a default | Bind to an MDM or device-attestation source. |
| `BotProtection` | Detect automated abuse. | ships a default | Integrate a managed bot-defense provider. |
| `RateLimiter` | Throttle sensitive flows. | ships a default | Distributed limiting across nodes. |
| `Clock` (PSR-20) | Supply current time. | `SystemClock` (`FakeClock` in tests) | Deterministic time for testing and replay. |

::: callout tip
Because these are interfaces, an enterprise rebinds any single one without forking a package. The
**[overview](/architecture/overview)** explains why this seam-based design keeps the blast radius of
change small; see also the **[dependency graph](/ecosystem/dependency-graph)**.
:::

## Tenant isolation: `BelongsToTenant`

Rebel is multi-tenant by construction. Models that hold tenant-scoped data use the `BelongsToTenant`
trait, which constrains reads and writes to the current tenant (resolved via `TenantResolver` /
`CurrentTenant`).

::: callout warning
Cross-tenant administrative reads must be **deliberate** — use `withoutGlobalScopes()` only where an
operator genuinely needs a suite-wide view, and audit those reads. Accidental scope bypass is a
tenant-isolation leak.
:::

The decisions that produced this model — assurance as a first-class type, keyed HMACs for PII,
always-on redacted audit, contracts over concretions — are recorded in the
**[Architecture Decision Records](/architecture/adr)**.
