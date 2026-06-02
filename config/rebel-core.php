<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Pepper keyed-HMAC con rotazione
    |--------------------------------------------------------------------------
    |
    | Gli identificatori (email/telefono), gli IP e gli OTP vengono protetti con
    | un HMAC "keyed" (chiave segreta lato server = "pepper"). Per poter ruotare
    | il pepper senza rompere gli hash già salvati, ogni riga memorizza la
    | "key_version" usata. Qui definiamo le versioni disponibili e quella attiva.
    |
    | IMPORTANTE: NON committare i valori reali. Vanno SOLO in .env / secrets.
    |
    */
    'peppers' => [
        1 => env('REBEL_PEPPER_V1', ''),
        // 2 => env('REBEL_PEPPER_V2', ''),  // aggiungere una nuova versione per ruotare
    ],

    // Versione di pepper usata per i NUOVI hash. La verifica prova la corrente e poi le deprecate.
    'pepper_current' => (int) env('REBEL_PEPPER_CURRENT', 1),

    // Algoritmo per gli HMAC. sha256 è lo standard.
    'hmac_algo' => env('REBEL_HMAC_ALGO', 'sha256'),

    /*
    |--------------------------------------------------------------------------
    | Privacy (GDPR)
    |--------------------------------------------------------------------------
    |
    | Per data-minimization gli IP e gli User-Agent vengono salvati come HMAC
    | (keyed) e non in chiaro. Un hash "semplice" di un IPv4 sarebbe reversibile,
    | quindi si usa SEMPRE l'HMAC con pepper.
    |
    */
    'hash_ip' => (bool) env('REBEL_HASH_IP', true),
    'hash_user_agent' => (bool) env('REBEL_HASH_USER_AGENT', true),

];
