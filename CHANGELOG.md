# Changelog

Tutte le modifiche rilevanti a `padosoft/laravel-rebel-core` sono documentate qui.
Il formato segue [Keep a Changelog](https://keepachangelog.com/it/1.1.0/) e [SemVer](https://semver.org/lang/it/).

## [Unreleased]

## [0.1.0] - 2026-06-03

### Added
- Skeleton + toolchain: `RebelCoreServiceProvider` (spatie/laravel-package-tools), `config/rebel-core.php`, Pest 4, PHPStan max (Larastan), Pint, Testbench; CI matrix PHP 8.3/8.4/8.5 × Laravel 12/13.
- **Identificatori**: `AuthIdentifier` + `EmailIdentifier`/`PhoneIdentifier`/`GenericIdentifier` (normalizzazione, masking, type).
- **Hashing keyed**: `KeyedHasher`/`HmacKeyedHasher` con pepper versionato e rotazione, confronto constant-time, validazione algoritmo.
- **Assurance**: `Aal`, `AssuranceLevel` (`satisfies` con phishing-resistant/restricted) — vedi ADR-0002.
- **Context**: `SecurityContext` (immutabile, `fromRequest` hash IP/UA), `TenantContext`, `DeviceContext`.
- **Risk**: `RiskLevel`, `RecommendedAction`, `RiskAssessment` (score 0-100, reasons machine-readable).
- **Auth**: `LoginResult` (web|token), `TokenPair` (Sanctum).
- **Audit**: `AuditEvent`, `AuthEventType`, `DatabaseAuditLogger` + migration `rebel_auth_events`, `Redactor`, model `RebelAuthEvent`.
- **Contratti**: `TokenIssuer`, `SubjectResolver`, `TenantResolver`, `RiskEvaluator`, `AuditLogger`, `SessionRegistry`, `DeviceTrust`, `BotProtection`, `RateLimiter`, `ConfigValidator`.
- **Clock** PSR-20: `SystemClock` + `FakeClock`.
- **Tenancy**: `CurrentTenant` + `BelongsToTenant` global scope.
- **Config**: comando `rebel:validate-config` (fail-fast in CI).
- ADR-0001..0005; README "ecosistema" didattico.
