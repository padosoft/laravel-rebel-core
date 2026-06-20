---
title: Gotchas & Limits
description: The honest list of Rebel's sharp edges and boundaries — what each limit is, why it exists, and what to do about it. This is where intellectual honesty lives.
---

# Gotchas & Limits

> Good security tools tell you what they *can't* do. This page is the honest inventory of Rebel's sharp
> edges — the places where a reasonable-looking assumption is wrong, and what to do instead.

None of these are defects. They are properties of the standards and the threat model Rebel is faithful
to. Read them once and you'll avoid the mistakes that look fine in a demo and fail in an audit.

---

## The sharp edges

| Gotcha | Why it's true | What to do |
|---|---|---|
| **A delivery receipt is not authentication.** | "Message delivered" proves the channel worked, not that the right person received or acted on it. | Treat delivery as telemetry; require an actual verified challenge for AAL. |
| **SMS is NIST "restricted".** | SMS OTP is phishable — it is **not** phishing-resistant. | Use it as a fallback only; reach for passkeys when phishing-resistance is required. |
| **Only passkeys are phishing-resistant.** | WebAuthn binds the credential to the origin; OTP over any channel does not. | Map a phishing-resistant requirement to passkeys, full stop. |
| **Email-OTP is AAL1.** | It cannot, by definition, satisfy a phishing-resistant requirement. | Don't let email-OTP "cover" an action that demands AAL2 phishing-resistant. |
| **`fortify_password_confirm` is web-only.** | The confirm flow lives in the web session. | On mobile/token clients, do step-up natively with tokens. |
| **SCA dynamic linking complements 3DS2 — it doesn't replace it.** | For cards, the PSP's 3DS2 still owns the card challenge. | Use Rebel's dynamic linking *alongside* the PSP flow, not instead of it. |
| **ai-guard's AI explains, never decides.** | An AI signal is advisory; it must never be the gate. | Keep the decision in deterministic policy; let AI annotate, not authorize. |
| **Tenant scoping needs `BelongsToTenant`.** | Without the trait, a model isn't tenant-isolated. | Add `BelongsToTenant`; cover the scoping with a fail-closed test. |
| **Stay on `^0.1` while in 0.x.** | Composer `^0.1` excludes `0.2.0`; floating breaks dependents. | Pin suite packages to `^0.1` until the suite leaves 0.x. |

---

## Callouts worth pinning

::: callout warning
**Delivery ≠ identity.** If your code path treats a provider delivery receipt as proof of who the user
is, you have a security bug, not a UX shortcut.
:::

::: callout info
**Queued audit workers reset tenant state safely.** That's a feature, not a footgun — but it means you
can't rely on ambient tenant state surviving into a job; pass what the job needs explicitly.
:::

::: callout tip
The assurance model is what mechanically enforces "email-OTP can't cover a passkey-required action."
Read the reasoning in **[Assurance Theory](/concepts/assurance-theory)**.
:::

---

## When Rebel is the wrong tool

Honesty cuts both ways. If your app has a single user model, no regulated actions, no tenancy and no
audit obligation, the full control plane is overkill — a plain Fortify setup may serve you better.
Rebel earns its weight when assurance levels, keyed PII, tenant isolation and a defensible audit trail
actually matter. The candid version of that trade-off lives in
**[Why Rebel](/ecosystem/why-rebel)**.
