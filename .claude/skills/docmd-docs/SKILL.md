---
name: docmd-docs
description: Use when working in doc-site/, adding Laravel Rebel ecosystem docs, changing docmd navigation/plugins/search, or keeping package README links in sync with centralized documentation.
---

# docmd-docs

The Laravel Rebel docs are centralized in `laravel-rebel-core/doc-site` and published at `https://doc.laravel-rebel.padosoft.com`.

Commands:

```bash
cd doc-site
npm run dev
npm run check
npm run build
```

Rules:

- Keep Markdown in `doc-site/docs/**` and register every page in `docmd.config.json` navigation.
- Use docmd containers only: `::: callout`, `::: tabs`, `::: steps`, `::: collapsible`, `::: grids`, `::: grid`, `::: card`.
- Do not use MDX/JSX components or raw capitalized component tags.
- Keep semantic search enabled and keep `.docmd-search/config.json` committed to avoid CI wizard prompts.
- Keep `node_modules/`, `_site/`, and search cache ignored except `.docmd-search/config.json`.
- For deep pages, include motivation, theory, Mermaid design, data/contract, ADR, worked example and gotchas.
- README files in all `laravel-rebel-*` packages must link to the centralized docs URL.
- Cloudflare Pages: root directory `doc-site`, build command `npm run build`, output `_site`, Node pinned by `.node-version`.
