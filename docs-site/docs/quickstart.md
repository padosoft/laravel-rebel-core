# Quickstart

## Motivazione

The fastest safe path is to install the meta-package or the exact set of Rebel packages needed by the application boundary. The core package supplies contracts and value objects; feature packages add concrete authentication flows.

::: steps
1. **Install the core or meta-package**
   ```bash
   composer require padosoft/laravel-rebel-auth
   ```

2. **Publish config and migrations**
   ```bash
   php artisan vendor:publish --tag=rebel-core-config
   php artisan migrate
   ```

3. **Select channels and bridges**
   Add provider packages such as `padosoft/laravel-rebel-channel-twilio`, `padosoft/laravel-rebel-bridge-fortify`, or `padosoft/laravel-rebel-bridge-passkeys`.

4. **Run package checks**
   ```bash
   composer test
   php artisan rebel:validate-config
   ```
:::

::: callout warning
Do not treat a channel provider as an assurance source by itself. Assurance is established by the Rebel core model and the step-up result, not by the SMS vendor response alone.
:::
