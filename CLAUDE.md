# CLAUDE.md

Vedi **`AGENTS.md`** per le regole operative complete (branching, Definition of Done, loop locale + gate GitHub, guardrail, README didattici, design-lock).

All'avvio di ogni sessione, in quest'ordine:
1. Leggi `docs/LESSON.md` (knowledge accumulato — vale per te e per ogni subagent).
2. Leggi `docs/PROGRESS.md` (dove eravamo rimasti).
3. Leggi `docs/IMPLEMENTATION-PLAN.md` (piano completo) e `AGENTS.md` (regole).

Promemoria chiave:
- **`copilot` solo con `-p`** (altrimenti si blocca).
- **Una PR per macro-task**; sotto-task = commit locali con loop locale (test + Playwright se UI + review Copilot locale).
- **README didattici e prolissi** con molti esempi: l'accessibilità per junior è un requisito.
- Aggiorna `PROGRESS.md` ad ogni sotto-task e `LESSON.md` quando impari qualcosa.
