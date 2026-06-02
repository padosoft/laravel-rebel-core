# ADR-0005 — Design lock (decisioni implementative)

Stato: accettato · Data: 2026-06-02 · Risolve i 32 punti dell'audit.

## ID & storage
- **ULID** per tabelle ad alto volume/temporali (`rebel_email_otp_challenges`, `rebel_step_up_challenges`, `rebel_auth_events`, `rebel_metric_buckets`). **UUID** per `rebel_devices`, `rebel_sessions`, recovery codes.
- `rebel_email_otp_challenges` ha `code_salt` (server-only) + `key_version`.
- Migrazioni auto-discovered; test up/down in CI.

## OTP / verifica
- Verifica atomica: **Redis Lua** se disponibile, altrimenti **DB `lockForUpdate()`**. Config `rebel.store`.
- Anti-enumeration: timing target fisso (250ms) + jitter, payload identico, **Clock PSR-20** iniettabile (`Carbon::setTestNow` nei test).
- Idempotency-key (header o derivata), store Redis TTL.
- Pepper keyed-HMAC con `key_version` + rotazione (verifica current poi deprecate).

## Login / token / step-up
- `LoginResult` (web=session | mobile=token-pair) via `TokenIssuer` (wrappa Sanctum: access+refresh). Token con claim `tenant_id`.
- Step-up token-native: `device_id` dal token; `fortify_password_confirm` web-only.
- `binding_hash` = HMAC(canonical_json([amount,currency,payee,orderRef]), pepper[v]); congelato su `require()`; cambia importo → 423.

## Assurance / config / tenant / errori
- `Aal`/`AssuranceLevel` di prima classe; resolver rifiuta driver sotto soglia (fail fast); `rebel:validate-config` in CI.
- `tenant_id` nullable solo tabelle di sistema; resto via global scope `BelongsToTenant`.
- Forma errore JSON normalizzata; redaction log (otp/secret/bearer/pepper) testata.

## Testing
- Fakes: `FakeClock`, `FakeTokenIssuer`, `FakeTwilioProvider`, `FakeAiClient`.
- Suite "live" (gruppo Pest `live`) con API reali, gated su env/secrets, auto-skip offline.
