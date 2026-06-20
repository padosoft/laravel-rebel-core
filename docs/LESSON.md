# LESSON

Accumulated knowledge for this repo. Applies to you and every subagent.

## docs-site (docmd)
- **No-raw-HTML check** (`docs-site/scripts/check-no-raw-html.mjs`, run via `npm run check`) only
  rejects **capitalized** component tags (regex `</?[A-Z]...`). Lowercase HTML slips through, but
  prefer **pure Markdown** — use `![alt](/assets/file.png)` for images, never `<img>`.
- **Images/assets:** put files in `docs-site/assets/`; reference them as `/assets/<file>`. docmd
  copies the entire `assets/` dir to `_site/assets/` on build (verified: PNGs + favicon + custom.css).
- **Build** (`npm run build`, from `docs-site/`) runs a semantic-search embedding pass via
  `onnxruntime-node`/transformers (~20s, 581 chunks/54 pages). Slow but offline. Always run from
  `docs-site/` (that's where `package.json` lives); the Bash tool's working dir persists between calls.
- **docmd containers only:** `::: callout info|tip|warning`, `::: tabs`, `::: steps`,
  `::: collapsible "title"`, and grids `::: grids` / `::: grid` / `::: card "Title" icon:name`. Each
  opener needs a matching `:::`. Cards nest grids>grid>card (each closes with its own `:::`).
- **Brand color:** red `#b91c1c` (`docs-site/assets/custom.css`); dark mode is the default theme.

## Content state (2026-06-20)
- The **best human-written source** for the whole ecosystem narrative is the core repo `README.md`
  (banner, glossary, "what it is in 1 minute", end-to-end flows, competitive matrix). Mine it for docs.
- Many non-package docs pages were auto-generated **identical stubs** (same `$D=(s,a,c,r)$` body).
  The 22 package reference pages contain REAL auto-extracted data (files/routes/migrations/tests) —
  preserve it verbatim; only the narrative around it was robotic.
