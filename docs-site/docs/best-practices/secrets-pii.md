---
title: Secrets & PII
description: The Rebel data-protection rules — keyed HMAC for all PII with a versioned pepper and rotation, never logging secrets via the Redactor, durable audit persistence, and a defensible GDPR posture.
---

# Secrets & PII

> Two rules carry the whole chapter: **no PII in cleartext, no secret in a log.** Identifiers, IPs and
> user-agents become keyed HMACs before they touch storage; OTPs, recovery codes, raw challenges and
> provider tokens never reach the audit trail at all.

Rebel treats personal data as something you *match*, not something you *read back*. You can prove "this
is the same email/IP we saw before" without ever storing the email or IP. That is what keeps the audit
trail useful and a GDPR request answerable.

---

## Keyed HMAC for every identifier

PII is hashed with `HmacKeyedHasher` using a **versioned pepper**. Each `HashedValue` records the
`key_version` it was produced with, so rotation never invalidates old data — old hashes stay verifiable
under their original pepper while new writes use the current one. Comparison is **constant-time**
(`matches()`), so it can't leak information through timing.

| Field | What you do | What you don't |
|---|---|---|
| Email / phone / identifier | Store the keyed HMAC + `key_version`. | Store the raw value, or a plain `sha256` with no pepper. |
| IP address | Hash it; match on the hash. | Log the dotted-quad in cleartext. |
| User-Agent | Hash it; correlate on the hash. | Persist the raw UA string. |
| Comparison | `matches()` (constant-time). | `===` / `==` on hashes. |

Configure the pepper from the environment — `peppers` keyed by version, with `pepper_current`
selecting which one new writes use:

```text
REBEL_PEPPER_V1=<64 hex chars>
REBEL_PEPPER_CURRENT=v1
```

Generate a pepper with:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

---

## Never log secrets

The `Redactor` strips sensitive fields from audit metadata **before** the write, so a stray value in a
metadata array becomes `[REDACTED]` instead of a breach.

::: callout warning
**Never log, and never pass un-redacted:** OTP codes · recovery / backup codes · passkey raw
challenges · provider tokens · webhook secrets · passwords. If one of these can appear in a metadata
array, the Redactor must cover it — verify with a test.
:::

::: callout tip
Audit events persist to **`rebel_auth_events`** — never the session — and dispatch **sync or queue**
(Horizon-ready). Queue workers reset tenant state safely, so a queued write can't bleed across tenants.
:::

---

## Pepper rotation

Rotation is additive: add the new pepper, point `pepper_current` at it, and let old hashes keep
verifying under their recorded `key_version`.

::: steps

### Generate the new pepper

`php -r "echo bin2hex(random_bytes(32));"` — store it as the next version, e.g. `REBEL_PEPPER_V2`.

### Add it alongside the old one

Keep `REBEL_PEPPER_V1` in place. Both peppers now live in `peppers`, keyed by version. Nothing is
re-hashed; old records still match under `v1`.

### Promote the new version

Set `REBEL_PEPPER_CURRENT=v2`. From now on, **new** writes are keyed with `v2`; existing `v1` hashes
remain valid because each row carries its own `key_version`.

### Retire on your data-minimization schedule

Once records keyed with an old pepper have aged out under your retention policy, remove the old pepper.
This is also how a `key_version` rotation supports the right to erasure.

:::

---

## GDPR posture

- **Data minimization** — store the keyed HMAC, not the personal datum.
- **`key_version` rotation** — supports erasure and key-compromise response without rewriting history.
- **Auditable, not readable** — you can prove correlation and answer an audit without holding cleartext PII.

::: callout info
These are the same guarantees stated normatively in
**[Security Invariants](/concepts/security-invariants)** — this page is the operational *how*; that
page is the invariant *what*.
:::
