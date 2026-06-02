<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Padosoft\Rebel\Core\Assurance\Aal;
use Padosoft\Rebel\Core\Concerns\BelongsToTenant;

/**
 * Modello Eloquent della tabella `rebel_auth_events`.
 *
 * Lo scrive il DatabaseAuditLogger (via query builder, per leggerezza); questo
 * modello serve a LEGGERE/filtrare gli eventi (es. admin-api) con i cast giusti e
 * l'isolamento per tenant (BelongsToTenant). Id ULID (stringa, non auto-increment).
 */
final class RebelAuthEvent extends Model
{
    use BelongsToTenant;

    protected $table = 'rebel_auth_events';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    // Sola lettura via Eloquent: le scritture passano dal DatabaseAuditLogger
    // (query builder). Nessun campo mass-assignable per sicurezza.
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
