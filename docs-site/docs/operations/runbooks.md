---
title: Runbooks
description: Concise operational runbooks for the Laravel Rebel suite — anomaly triage, provider outage, toll-fraud spikes, pepper rotation, refresh-token reuse, audit backlog and config drift.
---

# Runbooks

> Short, repeatable responses for the incidents an operator actually faces. Each runbook is symptom → steps,
> and every one ends with the same golden rule. Expand the scenario you need.

::: callout warning
**Golden rule — applies to every runbook below.** Never log OTPs or secrets. All PII (identifiers, IPs,
User-Agents) stays as a keyed HMAC. See **[Secrets and PII](/best-practices/secrets-pii)**.
:::

## Scenarios

::: collapsible "Anomaly case triage"
**Symptom:** an anomaly case appears in the admin SOC panel, or you want to sweep for new ones.

**Steps:**

1. Run the deterministic detector: `php artisan rebel:detect-anomalies` (ai-guard). Detection is rule-based,
   not a model guess.
2. Open the case in the admin SOC panel and review the evidence.
3. The AI only **explains and suggests** — it sees no PII or OTPs. A human makes the decision.
4. Action the case (dismiss, escalate or block) and let the outcome flow into the audit trail.

See **[AI Guard](/guides/ai-guard)**. Golden rule applies.
:::

::: collapsible "Provider / channel outage"
**Symptom:** message delivery is failing or degraded for a provider.

**Steps:**

1. The `channels` package fails over across providers automatically (for example Twilio → Vonage → Bird).
2. Check provider health in the admin panel.
3. Confirm the failure in delivery-receipt telemetry — receipts, not just sends, show where delivery broke.
4. If a provider is down for an extended window, prefer the healthy providers until receipts recover.

See **[Channels and Fallback](/guides/channels-fallback)**. Golden rule applies.
:::

::: collapsible "Toll-fraud / IRSF spike"
**Symptom:** a sudden surge in SMS/voice cost or traffic toward expensive destinations.

**Steps:**

1. The defense layers are already engaged: the `channels` anti-fraud, cooldown, rate limiting and the bot
   gate.
2. Investigate via audit and cost telemetry — look at cost-by-country and the affected destinations.
3. Tighten cooldown / rate limiting and lean on the bot gate for the targeted routes.
4. Confirm the spike subsides in the cost telemetry.

See **[Channels and Fallback](/guides/channels-fallback)**. Golden rule applies.
:::

::: collapsible "Pepper rotation"
**Symptom:** scheduled rotation, or a suspected pepper exposure.

**Steps:**

1. Add a new version to `peppers`, for example `2 => env('REBEL_PEPPER_V2')`.
2. Set `pepper_current => 2`. New hashes are produced with v2.
3. Old v1 hashes remain verifiable — both versions stay in the map.
4. **Never remove an in-use version.** Retire a version only once nothing hashed with it remains.

See **[Secrets and PII](/best-practices/secrets-pii)**. Golden rule applies.
:::

::: collapsible "Refresh-token reuse detected"
**Symptom:** the `sessions` registry flags reuse of a refresh token — a sign the token was stolen.

**Steps:**

1. Refresh-token rotation with reuse detection has already fired.
2. Revoke the entire token family for that subject.
3. Trigger "log out everywhere" so every active session for the subject is invalidated.
4. Confirm the revocation in the audit trail.

Golden rule applies.
:::

::: collapsible "Audit backlog (high volume)"
**Symptom:** audit writes are lagging under load.

**Steps:**

1. Switch `audit.mode` to `queue`. Events are then dispatched via `RecordAuditEventJob` (Horizon-ready)
   instead of synchronously.
2. Ensure a worker / Horizon is processing the queue.
3. Watch the backlog drain.

Events always persist to `rebel_auth_events`, never to the session. Golden rule applies.
:::

::: collapsible "Config drift"
**Symptom:** behavior differs between environments, or you suspect misconfiguration.

**Steps:**

1. Run the fail-fast validator: `php artisan rebel:validate-config`.
2. Fix whatever it reports before continuing.
3. Add the same command to CI so drift fails the build early — see **[Release Checklist](/operations/release-checklist)**.

Golden rule applies.
:::
