---
title: Architecture Decision Records
description: The load-bearing decisions behind Laravel Rebel — a dependency-free core with volatility at the leaves, assurance as a first-class type, keyed HMAC for PII, always-on redacted audit, contracts over concretions, and token-native step-up on mobile.
---

# Architecture Decision Records

> Architecture is the set of decisions that are expensive to reverse. This page records the
> load-bearing ones for Laravel Rebel — each as Context, Decision, and Consequences — so future
> contributors understand not just *what* the suite does but *why it had to*.

::: callout info
The Rebel design is **locked**. The canonical record is `ADR-0005-design-lock` in the core repository's
`docs/adr`; the entries below summarize the decisions that lock applies to. Changing one of these is a
design-level event, not a routine refactor.
:::

::: collapsible "ADR-0001 — Keep the core dependency-free; push volatility to the leaves"
**Context.** Auth integrations age badly: SMS provider APIs shift, WebAuthn libraries ship breaking
releases, AI models come and go. If the foundation depended on any of these, every churn would ripple
through 22 packages.

**Decision.** `laravel-rebel-core` depends on **nothing** in the suite and has **no hard dependency**
on Fortify, Twilio, a passkey library or any AI provider. Volatile integrations live at the **leaves**,
where they can change in isolation. The core only defines the shared language: value objects, the
assurance model, keyed hashing, audit and contracts.

**Consequences.** The blast radius of any provider change is contained to one leaf package.
Application policy is expressed against stable core types and does not move. The trade-off is that
composition must be documented (this site), not just individual APIs.
:::

::: collapsible "ADR-0002 — Assurance is a first-class type with a satisfies() guard"
**Context.** Most apps treat "is this user strong enough?" as a boolean, which collapses the
difference between an email OTP and a phishing-resistant passkey.

**Decision.** Model assurance as `AssuranceLevel` over **NIST AAL/AMR**, with a single guard:
`satisfies(Aal, requirePhishingResistant)`. Email-OTP is AAL1; TOTP and SMS-OTP are AAL2; passkeys are
phishing-resistant; SMS is treated as **restricted**. Each protected action declares its required
assurance, and the guard enforces it.

**Consequences.** An AAL1 session cannot silently cover an AAL2 action. Policy becomes explicit and
testable, and step-up decisions follow directly from comparing the actor's level against the action's
requirement.
:::

::: collapsible "ADR-0003 — Keyed HMAC with a versioned pepper for PII (GDPR)"
**Context.** Storing emails, IPs and User-Agents in cleartext is a GDPR liability and turns any audit
table into a breach magnet — yet you still need to correlate events for the same actor.

**Decision.** Hash identifiers with `HmacKeyedHasher` (HMAC-SHA256) using a **versioned pepper**
(`peppers`, `pepper_current`). The result is a `HashedValue(hash, keyVersion)`, compared in constant
time. Pepper **rotation** is supported: new writes use the current version while old values stay
verifiable via their stored `keyVersion`.

**Consequences.** No PII is ever stored in cleartext, yet events remain correlatable per actor. Key
rotation is a configuration change, not a data migration. Comparisons must stay constant-time to avoid
timing leaks.
:::

::: collapsible "ADR-0004 — Audit is always persisted, always redacted, sync or queue"
**Context.** Security evidence that is optional, lossy, or leaky is worse than none. Logging to the
session is not durable, and naive logging exfiltrates secrets.

**Decision.** Every security-significant outcome is recorded through the `AuditLogger` contract to
**`rebel_auth_events`** (never the session). The `Redactor` strips OTPs, tokens, recovery codes and
raw challenges before write. Dispatch is **configurable sync or queue** (`audit.mode`,
`RecordAuditEventJob`, Horizon-ready), and `ContextEnrichingAuditLogger` can decorate events with
context.

**Consequences.** Auditors get a complete, durable, secret-free trail. High-traffic deployments push
writes off the request path onto a queue. Surfacing an honest **empty state** is required — never
fabricate events to fill a panel.
:::

::: collapsible "ADR-0005 — Contracts over concretions for swappability"
**Context.** Enterprises have their own SIEMs, risk engines, session stores and device-trust sources.
A framework that hard-codes these forces forks.

**Decision.** Every integration boundary is a **contract** (interface) bound in the container:
`AuditLogger`, `TokenIssuer`, `SubjectResolver`, `TenantResolver`, `RiskEvaluator`, `SessionRegistry`,
`DeviceTrust`, `BotProtection`, `RateLimiter`, `KeyedHasher`, `Clock` (PSR-20). Each ships a sane
default and is meant to be overridden per app.

**Consequences.** Any single seam can be rebound without forking a package; the rest of the control
plane keeps working against the same types. The cost is one layer of indirection, which buys
testability (e.g. `FakeClock`) and integration freedom. The full catalogue is in
**[Data Model & Contracts](/architecture/data-model-contract)**.
:::

::: collapsible "ADR-0006 — fortify_password_confirm is web-only; mobile uses token-native step-up"
**Context.** Fortify's password-confirmation flow is built around a server-rendered web session. Mobile
and API clients authenticate with Sanctum tokens and have no session to confirm against, so reusing the
web flow there is a category error.

**Decision.** Keep `fortify_password_confirm` for **web** callers. For API and mobile callers, perform
**token-native step-up**: a per-action challenge whose success is reflected in the Sanctum `TokenPair`
flow rather than in a web session. The decision logic (risk → assurance → Allow/Step-up/Deny) is
identical; only the confirmation mechanism differs by caller type.

**Consequences.** Each client type uses the step-up mechanism native to it, while sharing one decision
pipeline and one audit trail. See the **[Pipeline & Workflow](/architecture/pipeline-workflow)** for how
the two result shapes converge.
:::

::: callout tip
These decisions are why the suite stays modular and auditable as it grows. Start from the
**[Architecture Overview](/architecture/overview)** for how they fit together, or the
**[core package](/packages/core)** for the types that implement them.
:::
