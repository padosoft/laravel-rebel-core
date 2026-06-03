# ADR-0005 — Design lock (implementation decisions)

Status: accepted · Date: 2026-06-02 · Resolves the 32 audit points.

## ID & storage
- **ULID** for high-volume/time-based tables (`rebel_email_otp_challenges`, `rebel_step_up_challenges`, `rebel_auth_events`, `rebel_metric_buckets`). **UUID** for `rebel_devices`, `rebel_sessions`, recovery codes.
- `rebel_email_otp_challenges` has `code_salt` (server-only) + `key_version`.
- Auto-discovered migrations; up/down tested in CI.

## OTP / verification
- Atomic verification: **Redis Lua** if available, otherwise **DB `lockForUpdate()`**. Config `rebel.store`.
- Anti-enumeration: fixed timing target (250ms) + jitter, identical payload, injectable **PSR-20 Clock** (`Carbon::setTestNow` in tests).
- Idempotency-key (header or derived), Redis TTL store.
- Keyed-HMAC pepper with `key_version` + rotation (verify current then deprecated).

## Login / token / step-up
- `LoginResult` (web=session | mobile=token-pair) via `TokenIssuer` (wraps Sanctum: access+refresh). Token with a `tenant_id` claim.
- Token-native step-up: `device_id` from the token; `fortify_password_confirm` web-only.
- `binding_hash` = HMAC(canonical_json([amount,currency,payee,orderRef]), pepper[v]); frozen on `require()`; amount changes → 423.

## Assurance / config / tenant / errors
- First-class `Aal`/`AssuranceLevel`; the resolver rejects drivers below the threshold (fail fast); `rebel:validate-config` in CI.
- `tenant_id` nullable only for system tables; everything else via the `BelongsToTenant` global scope.
- Normalized JSON error shape; log redaction (otp/secret/bearer/pepper) tested.

## Testing
- Fakes: `FakeClock`, `FakeTokenIssuer`, `FakeTwilioProvider`, `FakeAiClient`.
- "Live" suite (Pest `live` group) with real APIs, gated on env/secrets, auto-skip when offline.
