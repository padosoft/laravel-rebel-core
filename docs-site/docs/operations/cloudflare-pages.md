---
title: Cloudflare Pages
description: How the centralized Laravel Rebel documentation is built and deployed on Cloudflare Pages, with the clean-install and lockfile-sync discipline that keeps every build green.
---

# Cloudflare Pages

> The centralized Laravel Rebel docs live in `laravel-rebel-core/docs-site` and are published to
> [`https://doc.laravel-rebel.padosoft.com`](https://doc.laravel-rebel.padosoft.com) by Cloudflare Pages.
> One source, one site — no package ever ships its own doc site.

::: callout info
There is exactly one documentation project for the whole suite. Do not create package-local doc sites:
every `laravel-rebel-*` README links here instead. See **[Docs Maintenance](/operations/docs-maintenance)**.
:::

## Settings

Cloudflare Pages is configured to build the site straight out of the `docs-site` directory. Match these
values exactly when creating or auditing the project.

| Setting | Value |
|---|---|
| Root directory | `docs-site` |
| Build command | `npm run build` |
| Output directory | `_site` |
| Node version | pinned by `docs-site/.node-version` |
| Production domain | `doc.laravel-rebel.padosoft.com` |

::: callout warning
The Node version is pinned by `docs-site/.node-version`. If you bump the toolchain locally, commit the
`.node-version` change too, otherwise Cloudflare and your machine build on different runtimes.
:::

## First-time setup

::: steps

1. **Create the Pages project** — connect the `padosoft/laravel-rebel-core` repository in the Cloudflare
   dashboard and select the production branch (`main`).

2. **Set the build configuration** — root directory `docs-site`, build command `npm run build`, output
   directory `_site`. Cloudflare reads the Node version from `docs-site/.node-version`.

3. **Bind the custom domain** — attach `doc.laravel-rebel.padosoft.com` and let Cloudflare issue the
   certificate.

4. **Trigger the first deploy** — push to `main`, or use *Retry deployment* from the dashboard. The build
   log should show a clean install followed by `npm run build`.

:::

## The clean-install discipline

Cloudflare runs a **clean install** (`npm ci`) before every build — it deletes `node_modules` and installs
strictly from `package-lock.json`. This is unforgiving: if `package.json` and `package-lock.json` disagree,
`npm ci` fails and the deploy dies before the docs are ever built.

::: callout warning
After changing any docs dependency, validate with a clean install — not just `npm install`:

```bash
cd docs-site
npm ci --progress=false
```

`npm install` will happily patch a drifting lockfile in place and hide the problem. `npm ci` reproduces
exactly what Cloudflare does. If it passes locally, the deploy will get past the install step.
:::

### Keep `docmd-search` in sync with `@docmd/core`

The semantic search dependency, `docmd-search`, must satisfy the peer requirement declared by `@docmd/core`.
When `@docmd/core` requires `docmd-search >=0.1.0-alpha.1`, the lockfile must not be left pinned to
`0.1.0-alpha.0` — a stale pin passes `npm install` but breaks the clean install on Cloudflare.

::: callout tip
Treat the lockfile as a build artifact: whenever it changes, run `npm ci --progress=false` and commit
`package.json` and `package-lock.json` together in the same change.
:::

## Pre-push gate

Before pushing any change that touches docs or their dependencies, run all three from `docs-site/`:

```bash
cd docs-site
npm ci --progress=false
npm run check
npm run build
```

If the clean install, the container/link check and the build all pass locally, Cloudflare Pages will
reproduce the same green build. The full content workflow is in **[Docs Maintenance](/operations/docs-maintenance)**.
