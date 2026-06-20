---
title: Docs Maintenance
description: How to add and update pages in the centralized Laravel Rebel documentation — navigation registration, docmd-only containers, semantic search, README links and the pre-push checklist.
---

# Docs Maintenance

> Every word of Laravel Rebel documentation lives under `docs-site/docs/**` and is published from a single
> site. This page is the discipline that keeps it consistent: where pages go, how they are registered, and
> what must pass before you push.

::: callout info
A docs update is part of the work, not an afterthought. When a user-facing feature, config key, command,
route, migration, public contract or README section changes in any `laravel-rebel-*` repository, the
matching page under `docs-site/docs/**` changes in the same unit of work.
:::

## Add or update a page

::: steps

1. **Write the Markdown** — create or edit the file under `docs-site/docs/**`. Start with YAML frontmatter
   (`title`, `description`), then a single `# H1`, a short `>` hook, and procedural content.

2. **Register it in navigation** — every page must appear in `docs-site/docmd.config.json` under the right
   `navigation` group. An unregistered page is effectively invisible.

3. **Use docmd containers only** — no MDX, no JSX, no raw capitalized component tags. See the allowed set
   below.

4. **Run the local gate** — from `docs-site/`, run the clean install, the check and the build (below). All
   three must pass.

:::

::: callout warning
Adding Markdown under `docs-site/docs/**` without a matching entry in `docmd.config.json` navigation is the
most common mistake. If a new page is not in the navigation, it does not exist for readers.
:::

## Containers, not components

docmd renders plain Markdown plus a fixed set of container directives. Use only these, and always close
`:::` blocks in balanced pairs:

| Container | Syntax |
|---|---|
| Callout | `::: callout info` / `tip` / `warning` |
| Tabs | `::: tabs` |
| Steps | `::: steps` |
| Collapsible | `::: collapsible "Title"` |
| Grids | `::: grids` → `::: grid` → `::: card "Title" icon:shield` |

::: callout warning
No MDX or JSX, and no raw capitalized component tags — a tag like a bracketed `Foo` element is rejected by
the check. Reintroducing component syntax in Markdown is an anti-pattern that breaks the build.
:::

## Semantic search

Semantic search is enabled for the site. Keep `.docmd-search/config.json` committed so CI never has to run
the interactive search wizard. Everything else search-related stays ignored.

| Tracked | Ignored |
|---|---|
| `.docmd-search/config.json` | the rest of the search cache |
| | `node_modules/` |
| | `_site/` |

Keep `docmd-search` compatible with the peer requirement declared by `@docmd/core`. The dependency and
lockfile discipline is covered in **[Cloudflare Pages](/operations/cloudflare-pages)**.

## README links

Every `laravel-rebel-*` package README must link to the centralized docs URL,
[`https://doc.laravel-rebel.padosoft.com`](https://doc.laravel-rebel.padosoft.com). Do not create a
separate doc site for a package — point its README here instead.

## Pre-push checklist

Before pushing any docs change, run all three from `docs-site/`:

::: steps

1. **Clean install** — reproduces the Cloudflare build environment, so `package.json` and
   `package-lock.json` are proven in sync.

   ```bash
   cd docs-site
   npm ci --progress=false
   ```

2. **Check** — enforces the containers-only rule and validates structure.

   ```bash
   npm run check
   ```

3. **Build** — produces the static site into `_site`.

   ```bash
   npm run build
   ```

:::

::: callout tip
If a docs update is genuinely not needed — a purely internal refactor, a dependency-only tooling fix,
test-only cleanup or cosmetic formatting — say so explicitly in the PR or changelog rather than skipping
silently.
:::
