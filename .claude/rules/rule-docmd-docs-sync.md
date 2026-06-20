# Rule: centralized Rebel docmd docs sync

This rule is mandatory.

When a user-facing feature, config key, command, route, migration, public contract, README section or package behavior changes in any `laravel-rebel-*` repository, update `laravel-rebel-core/docs-site/docs/**` in the same work. If the change adds a page, register it in `docs-site/docmd.config.json` navigation.

Before closing the work, run:

```bash
cd docs-site
npm ci --progress=false
npm run check
npm run build
```

Cloudflare Pages runs a clean install before the build. Therefore `package.json` and `package-lock.json` must be in sync before every push. If docs dependencies change, validate with `npm ci --progress=false`, not only with `npm install`. Keep `docmd-search` compatible with the peer requirements declared by `@docmd/core`; do not leave the lockfile on `docmd-search@0.1.0-alpha.0` when `@docmd/core` requires `>=0.1.0-alpha.1`.

A docs update is not required for purely internal refactors, dependency-only tooling fixes, test-only cleanup, or cosmetic formatting. If skipped, state why in the PR or changelog.

Anti-patterns:

- Shipping a package feature without updating the central docs.
- Adding Markdown under `docs-site/docs/**` without navigation.
- Reintroducing MDX/JSX syntax in Markdown.
- Creating separate package-local doc sites for Rebel packages instead of the centralized `laravel-rebel-core/docs-site`.

