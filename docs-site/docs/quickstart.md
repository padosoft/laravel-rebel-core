---
title: Quickstart
description: Install Laravel Rebel, set the pepper, run the migrations and validate the config — the fastest safe path from zero to a working enterprise auth control plane.
---

# Quickstart

> Get from zero to a working Rebel control plane in a few commands. Install the meta-package for the
> recommended bundle, or pick the exact packages your app needs — the core supplies the contracts and
> value objects, the feature packages add the concrete flows.

::: steps
1. **Install the suite (or just the core)**
   ```bash
   # recommended: the curated bundle, wired together
   composer require padosoft/laravel-rebel-auth

   # or just the primitives (contracts + value objects)
   composer require padosoft/laravel-rebel-core
   ```

2. **Publish the config**
   ```bash
   php artisan vendor:publish --tag=rebel-core-config
   ```

3. **Set the pepper** — the server-side secret behind every keyed HMAC
   ```dotenv
   # generate a strong value: php -r "echo bin2hex(random_bytes(32));"
   REBEL_PEPPER_V1=paste-a-long-random-value
   REBEL_PEPPER_CURRENT=1
   ```

4. **Run the migrations** (creates `rebel_auth_events` for the audit trail)
   ```bash
   php artisan migrate
   ```

5. **Validate the config** — fail-fast, CI-friendly
   ```bash
   php artisan rebel:validate-config
   ```

6. **Add the channels and bridges you need**
   ```bash
   composer require padosoft/laravel-rebel-channels padosoft/laravel-rebel-channel-twilio
   composer require padosoft/laravel-rebel-bridge-fortify padosoft/laravel-rebel-bridge-passkeys
   ```
:::

::: callout tip
Not sure which packages you need? The **[Install Matrix](/install-matrix)** lists the exact set per
layer, and the **[Capability Matrix](/ecosystem/capability-matrix)** maps each capability to its
package. New to the concepts? Start with **[Passwordless Login](/guides/passwordless-login)** or the
full **[Worked Example](/guides/worked-example)**.
:::

::: callout warning
Do not treat a channel provider as an assurance source by itself. Assurance is established by the
Rebel core model and the step-up result — **not** by an SMS vendor's response alone. A delivery
receipt means "the message was sent", never "the user is authenticated".
:::
