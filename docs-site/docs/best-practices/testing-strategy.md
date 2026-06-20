---
title: Testing Strategy
description: How to test Rebel-based code — Pest + Testbench, PHPStan-max discipline with fix-don't-silence recipes, FakeClock for time-based flows, the four coverage pillars, and the CI matrix.
---

# Testing Strategy

> Security code earns trust by being *provably* correct, not plausibly correct. Rebel's testing
> strategy is opinionated for a reason: deterministic time, static analysis at the strictest setting,
> and four coverage pillars that catch the failures auth code actually has.

The toolchain is **Pest** on **Orchestra Testbench**, with **PHPStan level MAX** and **Pint** for
style. Every PHP file is `declare(strict_types=1)`, every class is `final`, and dependencies arrive via
constructor property promotion — which also makes them trivial to fake in a test.

---

## PHPStan level MAX — fix, never silence

Level MAX must stay green. The rule is absolute: **do not** reach for `@phpstan-ignore`, a baseline
entry, `assert()`, or an inline `@var` to make an error disappear. Those hide the bug; they don't fix
it. Resolve the root cause instead.

| Error shape | Fix (not silence) |
|---|---|
| `mixed` flowing into a cast | Narrow first: `is_scalar($x) ? (string) $x : null`. |
| `json_decode($s, true)` typed loosely | It is `array<array-key, mixed>` — narrow each key before use. |
| `make('request')` looks untyped | The container's `make('request')` is already typed `Illuminate\Http\Request`. |
| Large scan eats memory | Use `cursor()` rather than `get()`. |
| Cross-tenant admin read | `withoutGlobalScopes()` deliberately, with an audited reason. |
| Nested `where(fn ($q) => …)` | The closure receives `Illuminate\Database\Eloquent\Builder`. |

---

## Deterministic time with FakeClock

Time-based flows — OTP expiry, step-up windows — must be tested without `sleep()`. Rebel injects a
PSR-20 `Clock`; in tests, bind `FakeClock` and advance it by hand so expirations are exact and fast.

```php
use Padosoft\Rebel\Core\Clock\FakeClock;

$clock = new FakeClock(now());
$otp   = $issueOtp($clock);            // minted "now"

$clock->advance(seconds: 299);
expect($verify($otp, $clock))->toBeTrue();   // still inside the window

$clock->advance(seconds: 2);
expect($verify($otp, $clock))->toBeFalse();  // expired — deterministically
```

::: callout tip
A `FakeClock` turns a flaky "wait and hope" expiry test into a precise boundary test: one assertion
just inside the window, one just outside it.
:::

---

## The four coverage pillars

Every security-significant change covers all four. Skip one and you've tested the demo, not the system.

::: grids
::: grid
::: card "Happy path" icon:check
The intended flow succeeds and emits the expected audit event.
:::
::: card "Auth / fail-closed" icon:lock
When a check can't pass — bad credential, missing assurance, error — the system **denies**, it does not
fall open.
:::
::: card "Tenant scoping" icon:building
A tenant sees only its own data; cross-tenant reads require an explicit, audited scope removal.
:::
::: card "Empty state" icon:inbox
No data behaves honestly — empty lists, zero counts — and never fabricates results.
:::
:::
:::

---

## CI matrix

Green locally isn't done — the matrix must be green too. Tests run across every supported combination:

| PHP | Laravel |
|---|---|
| 8.3 · 8.4 · 8.5 | 12 · 13 |

That's six cells; all six must pass before merge.

::: callout warning
Tenant scoping deserves its own test even when it "obviously works" — a missing `BelongsToTenant` or a
forgotten global scope is exactly the kind of silent gap that only a fail-closed test catches.
:::

::: callout info
Some of these constraints are deliberate boundaries, not bugs — know which ones before you write the
test. See **[Gotchas & Limits](/best-practices/gotchas-limits)**.
:::
