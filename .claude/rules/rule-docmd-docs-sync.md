# Rule: centralized Rebel docmd docs sync

This rule is mandatory.

When a user-facing feature, config key, command, route, migration, public contract, README section or package behavior changes in any `laravel-rebel-*` repository, update `laravel-rebel-core/docs-site/docs/**` in the same work. If the change adds a page, register it in `docs-site/docmd.config.json` navigation.

Before closing the work, run:

```bash
cd docs-site
npm run check
npm run build
```

A docs update is not required for purely internal refactors, dependency-only tooling fixes, test-only cleanup, or cosmetic formatting. If skipped, state why in the PR or changelog.

Anti-patterns:

- Shipping a package feature without updating the central docs.
- Adding Markdown under `docs-site/docs/**` without navigation.
- Reintroducing MDX/JSX syntax in Markdown.
- Creating separate package-local doc sites for Rebel packages instead of the centralized `laravel-rebel-core/docs-site`.

