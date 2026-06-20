---
title: Package Reference
description: A flat, linkable index of all 22 Laravel Rebel packages with their Composer names — grouped by role.
---

# Package Reference

A quick, linkable index of every package and its Composer name. For the narrative view see the
**[Package Map](/ecosystem/package-map)**; for capability-to-package lookup see the
**[Capability Matrix](/ecosystem/capability-matrix)**.

## Foundation

- [`laravel-rebel-core`](/packages/core) — `padosoft/laravel-rebel-core` — shared value objects, NIST AAL/AMR assurance, security context, keyed hashing, audit trail and contracts. The foundation.
- [`laravel-rebel-auth`](/packages/auth) — `padosoft/laravel-rebel-auth` — meta-package that installs and wires the recommended suite together.

## Login & step-up

- [`laravel-rebel-email-otp`](/packages/email-otp) — `padosoft/laravel-rebel-email-otp` — passwordless email-OTP login (web + mobile/Sanctum), anti-enumeration, rate-limited.
- [`laravel-rebel-step-up`](/packages/step-up) — `padosoft/laravel-rebel-step-up` — per-action step-up, risk-based, with PSD2/SCA dynamic linking.
- [`laravel-rebel-bridge-fortify`](/packages/bridge-fortify) — `padosoft/laravel-rebel-bridge-fortify` — Fortify password-confirm/passkey/TOTP as step-up drivers; passkey-first login.
- [`laravel-rebel-bridge-passkeys`](/packages/bridge-passkeys) — `padosoft/laravel-rebel-bridge-passkeys` — WebAuthn passkey step-up driver, phishing-resistant AAL3.
- [`laravel-rebel-bridge-laragear-2fa`](/packages/bridge-laragear-2fa) — `padosoft/laravel-rebel-bridge-laragear-2fa` — TOTP (laragear/two-factor) AAL2 step-up driver.
- [`laravel-rebel-bridge-spatie-otp`](/packages/bridge-spatie-otp) — `padosoft/laravel-rebel-bridge-spatie-otp` — email/SMS OTP (spatie) AAL2 step-up driver.
- [`laravel-rebel-bridge-otpz`](/packages/bridge-otpz) — `padosoft/laravel-rebel-bridge-otpz` — email magic-code (otpz) step-up driver, AAL2.

## Channels & delivery

- [`laravel-rebel-channels`](/packages/channels) — `padosoft/laravel-rebel-channels` — SMS/WhatsApp/voice abstraction: fallback, cooldown, rate limiting, anti toll-fraud/IRSF.
- [`laravel-rebel-channel-twilio`](/packages/channel-twilio) — `padosoft/laravel-rebel-channel-twilio` — Twilio Verify (SMS/WhatsApp/voice), delivery, signed webhooks.
- [`laravel-rebel-channel-vonage`](/packages/channel-vonage) — `padosoft/laravel-rebel-channel-vonage` — Vonage Verify (SMS/voice), SMS delivery, signed receipts.
- [`laravel-rebel-channel-bird`](/packages/channel-bird) — `padosoft/laravel-rebel-channel-bird` — Bird Verify API (SMS), delivery, signed webhooks.
- [`laravel-rebel-channel-telegram`](/packages/channel-telegram) — `padosoft/laravel-rebel-channel-telegram` — Telegram bot delivery for OTP codes and alerts.
- [`laravel-rebel-channel-discord`](/packages/channel-discord) — `padosoft/laravel-rebel-channel-discord` — Discord webhook delivery for SOC alerts.

## Account governance

- [`laravel-rebel-sessions`](/packages/sessions) — `padosoft/laravel-rebel-sessions` — session/device registry, logout-everywhere, refresh rotation with reuse detection.
- [`laravel-rebel-recovery`](/packages/recovery) — `padosoft/laravel-rebel-recovery` — high-assurance recovery codes, anti-ATO.
- [`laravel-rebel-bot-protection`](/packages/bot-protection) — `padosoft/laravel-rebel-bot-protection` — Turnstile/reCAPTCHA/hCaptcha gate, fail-closed, audited.

## Operations & intelligence

- [`laravel-rebel-admin-api`](/packages/admin-api) — `padosoft/laravel-rebel-admin-api` — control-plane JSON API: metrics, audit, funnels, provider health.
- [`laravel-rebel-admin`](/packages/admin) — `padosoft/laravel-rebel-admin` — Web Admin Panel (Blade + AJAX + vanilla JS), the SOC dashboard.
- [`laravel-rebel-ai-guard`](/packages/ai-guard) — `padosoft/laravel-rebel-ai-guard` — anomaly detection + AI copilot that explains, never decides.
- [`laravel-rebel-demo`](/packages/demo) — `padosoft/laravel-rebel-demo` — demo / integration app wiring the whole suite together.
