# AGENTS.md — regole operative per Laravel Rebel (per agenti e umani)

> Questo file è il **contratto di lavoro** per ogni repo `laravel-rebel-*`. All'avvio di OGNI sessione: leggi `docs/LESSON.md` e `docs/PROGRESS.md` (nel repo meta `laravel-rebel-auth`). Il piano completo è in `docs/IMPLEMENTATION-PLAN.md`.

## Stack & target
- **Laravel 12 + 13**, **PHP 8.3 + 8.4 + 8.5**. Constraint: `illuminate/support: ^12.0|^13.0`, `php: ^8.3`.
- Testbench `^10.0|^11.0`, **Pest 4**, **Larastan 3** (PHPStan **level max**), **Pint** (preset `laravel`), `spatie/laravel-package-tools`.
- Namespace PSR-4: `Padosoft\Rebel\...`. Composer name: `padosoft/laravel-rebel-*`.

## Branching & PR — UNA PR per macro-task
- Un branch per macro-task (package): `feat/<macro>` da `main`.
- I **sotto-task** sono **commit locali** sul branch macro (loop locale sotto). **Niente PR per sotto-task.**
- A macro completo: push → **UNA PR `feat/<macro>` → main** → gate GitHub → merge → tag/release.
- Commit: gitmoji + messaggio chiaro; `Co-Authored-By` come da regole harness.

## Definition of Done

### Loop LOCALE per ogni sotto-task (niente PR)
1. Implementa + **guardrail**:
   - **Pest** per TUTTA la logica;
   - se il sotto-task tocca **UI/UX** (Blade/JS pubblicabili) → **Vite build + Playwright** di **tutte** le interazioni;
   - solo codice (no UI) → niente Playwright.
2. Verde in locale: `composer test` · `composer phpstan` (max) · `composer pint --test` · se UI `npm run build` + `npx playwright test`.
3. **Review Copilot LOCALE** (prima di committare il sotto-task):
   - `git diff origin/main...HEAD` (TUTTO il diff del branch; se grande → file temp);
   - `copilot --yolo -p "/review <diff|@file>: bug, sicurezza, stile, guardrail mancanti"` (**MAI** `copilot` senza `-p`);
   - applica fix finché **0 commenti rilevanti**.
4. Commit locale. Aggiorna `docs/PROGRESS.md` (+ `docs/LESSON.md` se hai imparato qualcosa).

### Gate GitHub UNA volta (PR macro→main)
1. `git push` del branch; `gh pr create` (feat/<macro> → main).
2. `gh pr edit <n> --add-reviewer '@copilot'` + verifica review partita (`gh pr view <n> --json reviewRequests,reviews`). *(quota `'@copilot'` per sicurezza in bash/zsh)*
3. Attendi **CI tutti verdi** + commenti Copilot completati.
4. Verde + 0 commenti aperti → `gh pr merge --squash`. Altrimenti fixa (test + commenti), push, **richiama nuova review**, ripeti.
5. Aggiorna `docs/LESSON.md` con gli insight di Copilot. Poi `git tag vX.Y.Z` + `gh release create`.

## Guardrail = obbligatori, non opzionali
Ogni sotto-task ha: **obiettivo preciso**, **dettagli implementativi**, **guardrail** (unit test PHP sempre; Playwright per UI). Niente "fatto" senza test verdi + review Copilot locale pulita.

## README = step finale obbligatorio di ogni package (DIDATTICO)
Il README "wow" è l'ultimo sotto-task prima della PR. Deve essere **prolisso e didattico**: un **junior / non-esperto di auth** deve capire **subito** cosa fa, come funziona (passo-passo + ASCII), come si monta (install a prova di junior), **ogni opzione di config** (tabella nome/default/effetto), con **molti esempi** copia-incolla (≥4-6 per package; web + mobile/Sanctum + casi d'errore). Glossario dei termini (OTP, step-up, AAL, passkey, dynamic linking). I README di `core` e del meta `auth` spiegano **l'intero ecosistema** (mappa package + dependency DAG + flussi end-to-end). Skeleton: `docs/README-standard.md` nel repo docs di analisi.

## Sicurezza (design-lock)
Rispetta `docs/adr/ADR-0005-design-lock.md` (nel repo `core`): ULID/UUID, `code_salt`+`key_version`, verifica OTP atomica (Redis Lua/DB lock), timing anti-enumeration (Clock PSR-20), idempotency, rotazione pepper, `LoginResult`/`TokenIssuer` (Sanctum), `binding_hash` SCA, `rebel:validate-config`, forma errore JSON normalizzata, redaction log, testing fakes. Mai OTP/secret nei log.

## File di stato (canonici nel repo meta `laravel-rebel-auth/docs/`)
- `PROGRESS.md` — dove sono ora (per riprendere). Aggiorna ad ogni sotto-task.
- `LESSON.md` — cosa ho imparato. Leggi all'avvio; **passa a ogni subagent**; aggiorna dopo i commenti Copilot.

## Banner & assets
Banner condiviso in `resources/screenshoots/Laravel-Rebel-banner.png` (sorgente: `Downloads\laravel-rebel\Laravel-Rebel-banner.png`). Usa `scripts/sync-banner.ps1`.
