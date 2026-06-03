# Changelog

All notable changes to `padosoft/laravel-rebel-core` are documented here.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and [SemVer](https://semver.org/).

## [Unreleased]

## [0.1.0] - 2026-06-03

### Added
- Skeleton + toolchain: `RebelCoreServiceProvider` (spatie/laravel-package-tools), `config/rebel-core.php`, Pest 4, PHPStan max (Larastan), Pint, Testbench; CI matrix PHP 8.3/8.4/8.5 × Laravel 12/13.
- **Identifiers**: `AuthIdentifier` + `EmailIdentifier`/`PhoneIdentifier`/`GenericIdentifier` (normalization, masking, type).
- **Keyed hashing**: `KeyedHasher`/`HmacKeyedHasher` with a versioned pepper and rotation, constant-time comparison, algorithm validation.
- **Assurance**: `Aal`, `AssuranceLevel` (`satisfies` with phishing-resistant/restricted) — see ADR-0002.
- **Context**: `SecurityContext` (immutable, `fromRequest` hashes IP/UA), `TenantContext`, `DeviceContext`.
- **Risk**: `RiskLevel`, `RecommendedAction`, `RiskAssessment` (score 0-100, machine-readable reasons).
- **Auth**: `LoginResult` (web|token), `TokenPair` (Sanctum).
- **Audit**: `AuditEvent`, `AuthEventType`, `DatabaseAuditLogger` + `rebel_auth_events` migration, `Redactor`, `RebelAuthEvent` model.
- **Contracts**: `TokenIssuer`, `SubjectResolver`, `TenantResolver`, `RiskEvaluator`, `AuditLogger`, `SessionRegistry`, `DeviceTrust`, `BotProtection`, `RateLimiter`, `ConfigValidator`.
- **Clock** PSR-20: `SystemClock` + `FakeClock`.
- **Tenancy**: `CurrentTenant` + `BelongsToTenant` global scope.
- **Config**: `rebel:validate-config` command (fail-fast in CI).
- ADR-0001..0005; didactic "ecosystem" README.
