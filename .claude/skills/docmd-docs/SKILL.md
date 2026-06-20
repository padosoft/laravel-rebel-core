---
name: docmd-docs
description: Use when working in docs-site/, adding Laravel Rebel ecosystem docs, changing docmd navigation/plugins/search, or keeping package README links in sync with centralized documentation.
---

# docmd-docs

The Laravel Rebel docs are centralized in `laravel-rebel-core/docs-site` and published at `https://doc.laravel-rebel.padosoft.com`.

Commands:

```bash
cd docs-site
npm ci --progress=false
npm run dev
npm run check
npm run build
```

Rules:

- Keep Markdown in `docs-site/docs/**` and register every page in `docmd.config.json` navigation.
- Use docmd containers only: `::: callout`, `::: tabs`, `::: steps`, `::: collapsible`, `::: grids`, `::: grid`, `::: card`.
- Do not use MDX/JSX components or raw capitalized component tags.
- Keep semantic search enabled and keep `.docmd-search/config.json` committed to avoid CI wizard prompts.
- Keep `node_modules/`, `_site/`, and search cache ignored except `.docmd-search/config.json`.
- For deep pages, include motivation, theory, Mermaid design, data/contract, ADR, worked example and gotchas.
- README files in all `laravel-rebel-*` packages must link to the centralized docs URL.
- Cloudflare Pages: root directory `docs-site`, build command `npm run build`, output `_site`, Node pinned by `.node-version`.
- Before closing or pushing docs dependency changes, run `npm ci --progress=false`; Cloudflare Pages uses clean install and fails when `package.json` and `package-lock.json` diverge.
- Keep `docmd-search` aligned with `@docmd/core` peer requirements. Do not ship a lockfile with `docmd-search@0.1.0-alpha.0` when `@docmd/core` requires `>=0.1.0-alpha.1`.

