---
title: Release Checklist
description: The end-to-end Definition of Done for a change to any Laravel Rebel package — local gates, one PR, the green CI matrix, squash-merge, tag and GitHub release.
---

# Release Checklist

> One change, one branch, one PR. This is the path every change to a `laravel-rebel-*` package takes from a
> green local loop to a tagged release — nothing ships until each gate is green.

::: callout warning
Stay in the `0.1.x` line. Dependents pin with Composer `^0.1`, which **excludes** `0.2.0`. Cutting `0.2.0`
silently breaks every package that depends on this one. Bump the patch (`0.1.x`), not the minor, unless the
whole suite is moving together.
:::

## The Definition of Done

::: steps

1. **Red → green with Pest** — write the failing test first, then make it pass. Cover the happy path,
   auth/fail-closed, tenant-scoping and the empty state.

2. **Static analysis and style** — both must be clean:

   ```bash
   composer phpstan
   composer pint -- --test
   ```

   PHPStan runs at level **max**. Fix the root cause; do not silence errors with `@phpstan-ignore`, baseline
   entries or inline `@var`.

3. **One feature branch, one PR** — branch from `main`, open a single pull request back to `main`. Keep the
   scope tight.

4. **Green CI matrix** — CI runs **PHP 8.3 / 8.4 / 8.5 × Laravel 12 / 13**. Every cell must pass before
   merge.

5. **Update `README.md` and `CHANGELOG.md`** — and, if any user-facing surface changed, the centralized docs
   under `docs-site/docs/**` (see **[Docs Maintenance](/operations/docs-maintenance)**).

6. **Squash-merge** — one PR collapses to one commit on `main`.

7. **Tag and release** — cut the version tag and publish the GitHub release:

   ```bash
   git tag vX.Y.Z
   git push origin vX.Y.Z
   gh release create vX.Y.Z
   ```

:::

## Gate summary

| Gate | Command / action | Must be |
|---|---|---|
| Tests | `composer test` (Pest) | green |
| Static analysis | `composer phpstan` (level max) | green |
| Style | `composer pint -- --test` | clean |
| Pull request | one branch → one PR to `main` | open |
| CI matrix | PHP 8.3/8.4/8.5 × Laravel 12/13 | all green |
| Docs | `README.md`, `CHANGELOG.md`, central docs | updated |
| Merge | squash-merge | one commit |
| Release | tag `vX.Y.Z` + `gh release create` | published |

::: callout tip
Config-level drift fails the build fast: `php artisan rebel:validate-config` is the fail-fast guard you can
run in CI and locally. See the **[Runbooks](/operations/runbooks)** for the operational side of releases.
:::
