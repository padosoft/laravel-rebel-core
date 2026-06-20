# PROGRESS

## 2026-06-20 — Enterprise-grade docs-site overhaul

**Goal:** turn the robotic/auto-generated `docs-site/` into human, enterprise-grade docs — banner,
real introduction (what/why/moats/killer features), and competitive comparison matrices.

### Done (validated: `npm run check` OK, `npm run build` = 54 pages, 0 errors)
- Copied `Laravel-Rebel-banner.png` + admin dashboard screenshot into `docs-site/assets/`
  (`laravel-rebel-banner.png`, `laravel-rebel-admin-dashboard.png`), referenced as `/assets/...`.
- **Homepage** (`docs/index.md`): banner, badges, "what it is in 1 minute", problem→solution table,
  who-it's-for, 8 moats (cards), SOC panel screenshot, compact competitive matrix, ecosystem mermaid,
  quickstart steps, AI batteries, grouped package index.
- **New** `docs/ecosystem/why-rebel.md`: 5 moats + 6 comparison matrices (vs Fortify, hand-rolled,
  core vs alternatives, vs hosted IdP, vs Shopify, channels vs single-provider SDK) + standards +
  honest "when Rebel is overkill". Registered first in the Ecosystem nav (`docmd.config.json`).
- Humanized `ecosystem/capability-matrix.md`, `ecosystem/package-map.md` (role-grouped),
  `ecosystem/dependency-graph.md` (removed LaTeX, added install order + blast-radius rule),
  `reference/packages.md` (grouped, deduped).
- **All 22 package pages** rewritten to a shared house style: `core.md` done by hand as the exemplar;
  the other 21 via 5 parallel subagents (meta+login, bridges, channels+telco, chat+governance,
  bot+operations). Real reference data (files/contracts/routes/migrations/tests/composer) preserved
  verbatim; academic Teoria/LaTeX + boilerplate removed. No invented APIs.

### Phase 2 — done (user approved full rewrite)
- Rewrote ALL remaining stub pages (22) into distinct, topic-faithful content via 6 parallel
  subagents: `concepts/*` (4), `architecture/*` (4), `guides/*` (6), `best-practices/*` (4),
  `operations/*` (4). Enhanced `quickstart.md`. `install-matrix.md` left as-is (real dependency data).
- Validated: no leftover stub markers, no `&amp;` entities, all `:::` balanced, `npm run check` OK,
  `npm run build` = 54 pages, 0 errors.
- CHANGELOG: added `## [0.1.2] - 2026-06-20` (docs overhaul).

### Release
- Shipped as **v0.1.2** (docs release): commit + push to `main` + tag `v0.1.2` + `gh release`.
