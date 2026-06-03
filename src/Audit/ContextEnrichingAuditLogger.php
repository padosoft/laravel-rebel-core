<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Padosoft\Rebel\Core\Contracts\AuditLogger;

/**
 * Cross-cutting audit decorator that enriches every event with request-derived
 * context that individual call sites don't always have — currently the country.
 *
 * The country is read from a configurable request header (default `CF-IPCountry`,
 * which Cloudflare sets when in front of the app). It is resolved here, synchronously,
 * BEFORE the event reaches the inner logger — so it is correct even when the inner
 * logger queues the write (the request is gone by the time the job runs).
 *
 * Apps that don't sit behind Cloudflare can point `rebel-core.geo.country_header` at
 * whatever their proxy sets (e.g. `X-Country`, `X-Geo-Country`), or disable it.
 */
final class ContextEnrichingAuditLogger implements AuditLogger
{
    public function __construct(
        private readonly AuditLogger $inner,
        private readonly Repository $config,
        private readonly Container $container,
    ) {}

    public function record(AuditEvent $event): void
    {
        $this->inner->record($event->withCountry($event->country ?? $this->resolveCountry()));
    }

    private function resolveCountry(): ?string
    {
        if ($this->config->get('rebel-core.geo.enabled', true) !== true) {
            return null;
        }

        // Resolve the request lazily (this logger is a singleton): never capture a
        // stale request, and stay null outside an HTTP context (queue/CLI).
        if (! $this->container->bound('request')) {
            return null;
        }

        $request = $this->container->make('request');

        $header = $this->config->get('rebel-core.geo.country_header', 'CF-IPCountry');
        $value = $request->header(is_string($header) ? $header : 'CF-IPCountry');

        if (! is_string($value) || $value === '') {
            return null;
        }

        // Normalize to a 2-letter uppercase code; ignore Cloudflare's "XX" placeholder.
        $code = strtoupper(substr(trim($value), 0, 2));

        return preg_match('/^[A-Z]{2}$/', $code) === 1 && $code !== 'XX' ? $code : null;
    }
}
