# Dependency Graph

```mermaid
flowchart TB
  Core[laravel-rebel-core]
  Auth[laravel-rebel-auth]
  Email[laravel-rebel-email-otp]
  Step[laravel-rebel-step-up]
  Channels[laravel-rebel-channels]
  AdminApi[laravel-rebel-admin-api]
  Admin[laravel-rebel-admin]
  Ai[laravel-rebel-ai-guard]
  Sessions[laravel-rebel-sessions]
  Recovery[laravel-rebel-recovery]
  Bot[laravel-rebel-bot-protection]
  Auth --> Core
  Core --> Email
  Core --> Step
  Core --> Channels
  Core --> AdminApi
  AdminApi --> Admin
  AdminApi --> Ai
  Core --> Sessions
  Core --> Recovery
  Core --> Bot
```

Let package dependencies be a directed graph $G=(V,E)$. Rebel keeps high-volatility integrations at graph leaves, minimizing blast radius.

$$
R(v)=\frac{outdegree(v)}{indegree(v)+1}
$$
