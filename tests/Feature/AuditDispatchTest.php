<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Padosoft\Rebel\Core\Audit\AuditEvent;
use Padosoft\Rebel\Core\Audit\RecordAuditEventJob;
use Padosoft\Rebel\Core\Contracts\AuditLogger;

function recordWithRequest(Request $request): void
{
    app()->instance('request', $request);
    app(AuditLogger::class)->record(new AuditEvent(type: 'login.succeeded', guard: 'web'));
}

it('enriches the event with the country from the CF-IPCountry header by default', function (): void {
    recordWithRequest(Request::create('/', 'GET', [], [], [], ['HTTP_CF_IPCOUNTRY' => 'it']));

    expect(DB::table('rebel_auth_events')->value('country'))->toBe('IT');
});

it('reads the country from a configurable header', function (): void {
    config()->set('rebel-core.geo.country_header', 'X-Geo-Country');

    recordWithRequest(Request::create('/', 'GET', [], [], [], ['HTTP_X_GEO_COUNTRY' => 'DE']));

    expect(DB::table('rebel_auth_events')->value('country'))->toBe('DE');
});

it('ignores Cloudflare placeholder and stores no country when geo is disabled', function (): void {
    config()->set('rebel-core.geo.enabled', false);

    recordWithRequest(Request::create('/', 'GET', [], [], [], ['HTTP_CF_IPCOUNTRY' => 'IT']));

    expect(DB::table('rebel_auth_events')->value('country'))->toBeNull();
});

it('queues the write when audit mode is queue (Horizon-compatible)', function (): void {
    config()->set('rebel-core.audit.mode', 'queue');
    config()->set('rebel-core.audit.queue', 'rebel-audit');
    Queue::fake();

    app(AuditLogger::class)->record(new AuditEvent(type: 'login.succeeded', guard: 'web'));

    // Nothing written inline; a queued job carries the (already enriched) event.
    expect(DB::table('rebel_auth_events')->count())->toBe(0);
    Queue::assertPushed(RecordAuditEventJob::class, fn (RecordAuditEventJob $job): bool => $job->event->type === 'login.succeeded' && $job->queue === 'rebel-audit');
});
