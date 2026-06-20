---
title: Capability Matrix
description: Map every authentication capability to the Laravel Rebel packages that provide it — and see at a glance which package owns what.
---

# Capability Matrix

Start from **what you want to do**, find the package that does it. Every capability below maps to one
or more packages; the core underpins them all.

| You want to… | Capability | Package(s) |
|---|---|---|
| Speak one assurance/audit language across the suite | Shared contracts, AAL/AMR, keyed hashing, audit | [`core`](/packages/core) |
| Install the recommended stack in one shot | Curated meta-bundle + wiring | [`auth`](/packages/auth) |
| Let users log in without a password | Passwordless email-OTP (web + mobile) | [`email-otp`](/packages/email-otp) |
| Re-confirm a sensitive action at the right strength | Per-action step-up, risk-based, PSD2/SCA | [`step-up`](/packages/step-up) |
| Use Fortify's password-confirm / passkey / TOTP as step-up | Fortify integration + passkey-first login | [`bridge-fortify`](/packages/bridge-fortify) |
| Offer phishing-resistant passkeys | WebAuthn AAL3 step-up driver | [`bridge-passkeys`](/packages/bridge-passkeys) |
| Offer TOTP authenticator apps | TOTP AAL2 step-up driver | [`bridge-laragear-2fa`](/packages/bridge-laragear-2fa), [`bridge-spatie-otp`](/packages/bridge-spatie-otp) |
| Offer email magic-code as step-up | OTPZ email-code driver | [`bridge-otpz`](/packages/bridge-otpz) |
| Send SMS/WhatsApp/voice resiliently | Channel abstraction: fallback, cooldown, anti-fraud | [`channels`](/packages/channels) |
| Deliver via a specific provider | Provider drivers (Verify, delivery, webhooks) | [`channel-twilio`](/packages/channel-twilio), [`channel-vonage`](/packages/channel-vonage), [`channel-bird`](/packages/channel-bird) |
| Push OTP/alerts to chat | Telegram / Discord delivery | [`channel-telegram`](/packages/channel-telegram), [`channel-discord`](/packages/channel-discord) |
| Govern devices and sessions | Session/device registry, logout-everywhere, refresh rotation | [`sessions`](/packages/sessions) |
| Let users recover a locked account safely | High-assurance recovery codes, anti-ATO | [`recovery`](/packages/recovery) |
| Stop bots before they cost you | CAPTCHA gate (Turnstile/reCAPTCHA/hCaptcha), fail-closed | [`bot-protection`](/packages/bot-protection) |
| Read security metrics over an API | Control-plane JSON API (metrics, audit, funnels, health) | [`admin-api`](/packages/admin-api) |
| Watch it all from a dashboard | Web admin panel (SOC) | [`admin`](/packages/admin) |
| Detect and explain anomalies | Deterministic rules + AI copilot | [`ai-guard`](/packages/ai-guard) |
| See a full reference wiring | Demo / integration app | [`demo`](/packages/demo) |

---

## Capability coverage at a glance

Which layer of the stack each capability lives in. The core is always involved (it defines the
language); the rest is opt-in.

| Capability | Core | Login | Step-up | Channels | Governance | Operations |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| NIST AAL/AMR assurance model | ✅ | ✅ | ✅ | ➖ | ➖ | ✅ |
| Keyed-HMAC PII storage + rotation | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Audit trail + secret redaction | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Passwordless email-OTP | ➖ | ✅ | ➖ | ➖ | ➖ | ➖ |
| Per-action step-up + PSD2/SCA | ➖ | ➖ | ✅ | ➖ | ➖ | ➖ |
| Passkey / TOTP / OTP drivers | ➖ | ➖ | ✅ | ➖ | ➖ | ➖ |
| SMS/WhatsApp/voice + fallback + anti-fraud | ➖ | ➖ | ➖ | ✅ | ➖ | ➖ |
| Sessions / devices / recovery | ➖ | ➖ | ➖ | ➖ | ✅ | ➖ |
| Bot / CAPTCHA gate | ➖ | ✅ | ✅ | ✅ | ➖ | ➖ |
| Metrics API + admin panel + AI copilot | ➖ | ➖ | ➖ | ➖ | ➖ | ✅ |

> Legend: ✅ primary owner · ➖ participates or not applicable.

::: callout tip
Need file-level detail — providers, routes, migrations, tests? Open the package reference pages under
**[Packages](/packages/core)**. Want to see how they depend on each other? See the
**[Dependency Graph](/ecosystem/dependency-graph)**.
:::
