<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Padosoft\Rebel\Core\Assurance\Aal;
use Padosoft\Rebel\Core\Concerns\BelongsToTenant;

/**
 * Eloquent model for the `rebel_auth_events` table.
 *
 * The DatabaseAuditLogger writes it (via the query builder, for lightness); this
 * model is for READING/filtering the events (e.g. admin-api) with the right casts
 * and per-tenant isolation (BelongsToTenant). ULID id (string, not auto-increment).
 */
final class RebelAuthEvent extends Model
{
    use BelongsToTenant;

    protected $table = 'rebel_auth_events';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    // Read-only via Eloquent: writes go through the DatabaseAuditLogger
    // (query builder). No mass-assignable fields, for safety.
    protected $fillable = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aal' => Aal::class,
            'amr' => 'array',
            'metadata' => 'array',
            'key_version' => 'integer',
            'risk_score' => 'integer',
            'created_at' => 'immutable_datetime',
        ];
    }
}
