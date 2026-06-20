# Install Matrix

| Layer | Packages | Typical install command |
|---|---|---|
| Core language | `laravel-rebel-core` | `composer require padosoft/laravel-rebel-core` |
| Passwordless | `laravel-rebel-email-otp` | `composer require padosoft/laravel-rebel-email-otp` |
| Step-up | `laravel-rebel-step-up`, bridges | `composer require padosoft/laravel-rebel-step-up padosoft/laravel-rebel-bridge-fortify` |
| Channels | `laravel-rebel-channels` plus providers | `composer require padosoft/laravel-rebel-channels padosoft/laravel-rebel-channel-twilio` |
| Operations | admin, recovery, sessions, AI guard | `composer require padosoft/laravel-rebel-admin-api padosoft/laravel-rebel-admin` |

## Composer dependencies by package

### `laravel-rebel-bridge-passkeys`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-bridge-spatie-otp`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-channel-bird`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-channels` | `^0.1` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-channel-discord`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-channels` | `^0.1.2` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-channels`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-channel-telegram`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-channels` | `^0.1.2` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-channel-twilio`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-channels` | `^0.1` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |
| `twilio/sdk` | `^8.3` |

### `laravel-rebel-channel-vonage`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-channels` | `^0.1` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-core`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `php` | `^8.3` |
| `psr/clock` | `^1.0` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-demo`

| Dependency | Constraint |
|---|---|
| `benbjurstrom/otpz` | `0.7` |
| `laragear/two-factor` | `4.0` |
| `laravel/fortify` | `^1.25` |
| `laravel/framework` | `^13.8` |
| `laravel/sanctum` | `^4.0` |
| `laravel/tinker` | `^3.0` |
| `padosoft/laravel-rebel-admin-api` | `0.1.7` |
| `padosoft/laravel-rebel-auth` | `^0.1` |
| `padosoft/laravel-rebel-bot-protection` | `0.1` |
| `padosoft/laravel-rebel-bridge-laragear-2fa` | `0.1.1` |
| `padosoft/laravel-rebel-bridge-otpz` | `0.1` |
| `padosoft/laravel-rebel-bridge-passkeys` | `0.1` |
| `padosoft/laravel-rebel-bridge-spatie-otp` | `0.1` |
| `padosoft/laravel-rebel-channel-bird` | `0.1` |
| `padosoft/laravel-rebel-channel-discord` | `0.1` |
| `padosoft/laravel-rebel-channel-telegram` | `0.1` |
| `padosoft/laravel-rebel-channel-twilio` | `^0.1` |
| `padosoft/laravel-rebel-channel-vonage` | `0.1` |
| `padosoft/laravel-rebel-channels` | `0.1.2` |
| `php` | `^8.3` |
| `spatie/laravel-one-time-passwords` | `1.1` |
| `spatie/laravel-passkeys` | `1.8` |

### `laravel-rebel-email-otp`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-recovery`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-sessions`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-step-up`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-admin`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-admin-api` | `^0.1` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-admin-api`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-ai-guard`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-auth`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-admin` | `^0.1` |
| `padosoft/laravel-rebel-admin-api` | `^0.1` |
| `padosoft/laravel-rebel-ai-guard` | `^0.1` |
| `padosoft/laravel-rebel-bridge-fortify` | `^0.1` |
| `padosoft/laravel-rebel-channels` | `^0.1` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-email-otp` | `^0.1` |
| `padosoft/laravel-rebel-recovery` | `^0.1` |
| `padosoft/laravel-rebel-sessions` | `^0.1` |
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-bot-protection`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-bridge-fortify`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-bridge-laragear-2fa`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |

### `laravel-rebel-bridge-otpz`

| Dependency | Constraint |
|---|---|
| `illuminate/contracts` | `^12.0|^13.0` |
| `illuminate/support` | `^12.0|^13.0` |
| `padosoft/laravel-rebel-core` | `^0.1` |
| `padosoft/laravel-rebel-step-up` | `^0.1` |
| `php` | `^8.3` |
| `spatie/laravel-package-tools` | `^1.92` |
