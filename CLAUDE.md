# CLAUDE.md — AI working guide for `padosoft/laravel-rebel-core`

> Working on this package with an AI agent (Claude Code, Cursor, Copilot, Codex)? Read this first.
> It's the "batteries" that make vibe-coding here land on the first try. Plain Markdown — every
> tool can read it.

## What this package is
The core primitives of the Laravel Rebel suite: the shared value objects, contracts, assurance
levels (AAL/AMR), security context, keyed hashing, audit trail and Sanctum token abstraction that
every other `padosoft/laravel-rebel-*` package builds on.

Part of the **Laravel Rebel** suite — an enterprise authentication control plane over Laravel
Fortify. The shared language (value objects, contracts, the audit trail) lives in
`padosoft/laravel-rebel-core`; this package builds on it.

## Non-negotiable conventions
- `declare(strict_types=1);` in every PHP file; `final` classes; constructor property promotion.
- **PHPStan level max** must stay green. Do NOT add `@phpstan-ignore`, baseline entries, or
  `assert()`/inline `@var` to silence errors — fix the root cause. Common recipes:
  - narrow `mixed` before casting: `is_scalar($x) ? (string) $x : null`;
  - `json_decode($s, true)` is `array<array-key, mixed>`;
  - the container's `make('request')` is already typed `Illuminate\Http\Request`;
  - use `cursor()` for large scans, `withoutGlobalScopes()` for cross-tenant admin reads;
  - nested Eloquent `where(fn ($q) => …)` closures receive `Illuminate\Database\Eloquent\Builder`.
- **Tests:** Pest, Testbench. Cover happy path, auth/fail-closed, tenant-scoping, empty state.
- **Style:** Pint (`composer pint`). **Docs/comments in English.**
- Package wiring uses `spatie/laravel-package-tools` (`configurePackage`).

## Security & telemetry rules (suite-wide)
- Never store PII in cleartext: identifiers, IPs and User-Agents are **keyed HMACs** (core
  `KeyedHasher`). Never log OTPs/secrets (the `Redactor` sanitizes audit metadata).
- **Telemetry completeness:** if this package is a channel/driver/bridge/provider, it MUST capture
  everything that fills the admin panel (sends, **delivery receipts**, cost, country, devices,
  anomalies…). Record through the core `AuditLogger` contract — it persists to `rebel_auth_events`
  (never session) and supports **configurable sync|queue** dispatch (Horizon-ready). Skip a field
  only when the driver genuinely can't supply it, and surface an honest empty state — never fake data.

## How to extend it
This is the foundation package — most extension happens by binding the host app's implementation of
a core **contract** (`src/Contracts/`) in the container:
- **Swap the `AuditLogger`** (`src/Contracts/AuditLogger.php`): the default `DatabaseAuditLogger`
  can be decorated (`ContextEnrichingAuditLogger`) or queued (`QueuedAuditLogger` /
  `RecordAuditEventJob`); bind your own to ship events elsewhere (SIEM, data lake).
- **Implement the resolver/issuer contracts** the suite consumes: `TokenIssuer` (Sanctum
  access+refresh `TokenPair`), `SubjectResolver`, `TenantResolver`, `RateLimiter`, `RiskEvaluator`,
  `DeviceTrust`, `BotProtection`, `SessionRegistry` — each ships a default but is meant to be
  overridden per app.
- **Add a value object / identifier:** new `AuthIdentifier` subtypes (`EmailIdentifier`,
  `PhoneIdentifier`, `GenericIdentifier`) or context objects (`SecurityContext`, `DeviceContext`,
  `TenantContext`) — keep them immutable and `final`.
- **Plug a different `KeyedHasher`:** the default `HmacKeyedHasher` produces `HashedValue`s; swap it
  to change the keying/peppering strategy (constant-time comparison must be preserved).

## Definition of Done (per change)
1. Red→green with Pest; `composer phpstan` (max) + `composer pint -- --test` clean.
2. One feature branch, one PR to `main`. CI matrix **PHP 8.3/8.4/8.5 × Laravel 12/13** must be green.
3. Update `README.md` + `CHANGELOG.md`. Squash-merge.
4. **Release:** `git tag vX.Y.Z && git push origin vX.Y.Z` + `gh release create`. Stay in `0.1.x`
   (Composer `^0.1` excludes `0.2.0` and would break dependents).

## Skills
This repo ships invocable skills under `.claude/skills/` — at least `rebel-package-dev` (the dev
loop + PHPStan-max recipes). Invoke it before non-trivial work.

## Session startup
At the start of each session, in this order:
1. Read `docs/LESSON.md` (accumulated knowledge — applies to you and every subagent).
2. Read `docs/PROGRESS.md` (where we left off).
3. Read `docs/IMPLEMENTATION-PLAN.md` (full plan) and `AGENTS.md` (the complete operational rules:
   branching, Definition of Done, local loop + GitHub gates, guardrails, design-lock). Update
   `PROGRESS.md` after each sub-task and `LESSON.md` whenever you learn something.
