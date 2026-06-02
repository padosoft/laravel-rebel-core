<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Identifiers;

/**
 * Un identificatore con cui un utente si autentica (email, telefono, username...).
 *
 * Tre responsabilità:
 *  - type():       il tipo ('email' | 'phone' | 'generic'), utile per routing/policy;
 *  - normalized(): la forma canonica usata per lookup e per l'HMAC (es. email lowercase);
 *  - masked():     una forma offuscata, sicura da mostrare in UI o nei log (no PII piena).
 *
 * Nota: gli identificatori NON calcolano da soli l'HMAC: per quello c'è il
 * KeyedHasher (che hasha normalized()). Così i value object restano "puri" e testabili.
 */
interface AuthIdentifier
{
    public function type(): string;

    public function normalized(): string;

    public function masked(): string;
}
