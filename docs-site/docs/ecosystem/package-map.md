---
title: Package Map
description: The 22 Laravel Rebel packages grouped by the role they play — foundation, login, step-up, channels, governance, operations and bridges.
---

# Package Map

Rebel is **one foundation + feature packages + provider bridges**. Instead of a flat list, here's the
suite organized by the role each package plays. Install only the rows you need.

---

## 🧱 Foundation

The shared language. Everything else depends on it; it depends on nothing in the suite.

| Package | What it gives you |
|---|---|
| [`laravel-rebel-core`](/packages/core) | Value objects, contracts, the NIST AAL/AMR assurance model, security context, keyed hashing, the audit trail and `rebel_auth_events`. The entry point of the whole ecosystem. |
| [`laravel-rebel-auth`](/packages/auth) | Meta-package that installs and wires the recommended suite together. The fastest way to start. |

---

## 🔑 Login & step-up

How users prove who they are — at login, and again for sensitive actions.

| Package | What it gives you |
|---|---|
| [`laravel-rebel-email-otp`](/packages/email-otp) | Passwordless email-OTP login for web and mobile (Sanctum), with anti-enumeration and multi-dimensional rate limiting. |
| [`laravel-rebel-step-up`](/packages/step-up) | Re-confirm an action/purpose at the right assurance, risk-based, with PSD2/SCA dynamic linking. |
| [`laravel-rebel-bridge-fortify`](/packages/bridge-fortify) | Exposes Fortify's password-confirm / passkey / TOTP as step-up drivers and enables passkey-first login. |
| [`laravel-rebel-bridge-passkeys`](/packages/bridge-passkeys) | WebAuthn passkey step-up driver (spatie/laravel-passkeys) — phishing-resistant AAL3. |
| [`laravel-rebel-bridge-laragear-2fa`](/packages/bridge-laragear-2fa) | TOTP authenticator-app step-up driver (laragear/two-factor), recovery-code aware. |
| [`laravel-rebel-bridge-spatie-otp`](/packages/bridge-spatie-otp) | Email/SMS OTP step-up driver (spatie/laravel-one-time-passwords), AAL2. |
| [`laravel-rebel-bridge-otpz`](/packages/bridge-otpz) | Email magic-code step-up driver (benbjurstrom/otpz), AAL2. |

---

## 📡 Channels & delivery

Getting codes and alerts to users — resilient and anti-fraud.

| Package | What it gives you |
|---|---|
| [`laravel-rebel-channels`](/packages/channels) | The provider-agnostic abstraction: verification routing with fallback, cooldown, multi-dimensional rate limiting and anti toll-fraud/IRSF defenses. |
| [`laravel-rebel-channel-twilio`](/packages/channel-twilio) | Twilio provider: Verify (SMS/WhatsApp/voice), message delivery, signed delivery-status webhooks. |
| [`laravel-rebel-channel-vonage`](/packages/channel-vonage) | Vonage provider: Verify (SMS/voice), SMS delivery, signed delivery receipts. |
| [`laravel-rebel-channel-bird`](/packages/channel-bird) | Bird (ex-MessageBird) provider: Verify API (SMS), delivery, signed webhooks. |
| [`laravel-rebel-channel-telegram`](/packages/channel-telegram) | Telegram bot channel: deliver OTP codes and security alerts to a chat. |
| [`laravel-rebel-channel-discord`](/packages/channel-discord) | Discord channel: ship SOC alerts (anomalies, lockouts, high-risk events) via webhook. |

---

## 🛡️ Account governance

What happens after login — sessions, devices, recovery and bot defense.

| Package | What it gives you |
|---|---|
| [`laravel-rebel-sessions`](/packages/sessions) | Device/session registry, "log out everywhere", refresh-token rotation with reuse detection, device trust. |
| [`laravel-rebel-recovery`](/packages/recovery) | High-assurance account recovery: single-use HMAC-hashed backup codes generated once at enrolment, with anti-ATO checks. |
| [`laravel-rebel-bot-protection`](/packages/bot-protection) | Pluggable CAPTCHA gate (Turnstile, reCAPTCHA v3, hCaptcha), fail-closed by default and fully audited. |

---

## 📊 Operations & intelligence

Run it, watch it, understand it.

| Package | What it gives you |
|---|---|
| [`laravel-rebel-admin-api`](/packages/admin-api) | Control-plane JSON API: security metrics, audit-event explorer, OTP/step-up funnels, provider health — permission-gated and tenant-scoped. |
| [`laravel-rebel-admin`](/packages/admin) | The Web Admin Panel (Blade + AJAX + vanilla JS) — a security operations dashboard over the Admin API. |
| [`laravel-rebel-ai-guard`](/packages/ai-guard) | Anomaly detection (deterministic rules) + an AI copilot that explains and suggests on sanitized prompts — never decides. |
| [`laravel-rebel-demo`](/packages/demo) | A demo / integration application wiring the whole suite together — a reference you can read and run. |

---

::: callout tip
Want to see the install order and how these depend on each other? See the
**[Dependency Graph](/ecosystem/dependency-graph)**. Want to map a capability to a package? See the
**[Capability Matrix](/ecosystem/capability-matrix)**. Each package reference page lists its real
files, providers, routes, migrations and tests.
:::
