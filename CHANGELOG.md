# Changelog

Tutte le modifiche rilevanti a `padosoft/laravel-rebel-core` sono documentate qui.
Il formato segue [Keep a Changelog](https://keepachangelog.com/it/1.1.0/) e [SemVer](https://semver.org/lang/it/).

## [Unreleased]

### Added
- Skeleton del package: `RebelCoreServiceProvider` (spatie/laravel-package-tools), `config/rebel-core.php`.
- Toolchain: Pest 4, PHPStan (Larastan) level max, Pint preset `laravel`, Testbench.
- CI GitHub Actions: matrix PHP 8.3/8.4/8.5 × Laravel 12/13 + job qualità (Pint + PHPStan).
- `.env.example` documentato (pepper keyed-HMAC + privacy).
