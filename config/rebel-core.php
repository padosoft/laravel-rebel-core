<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Keyed-HMAC pepper with rotation
    |--------------------------------------------------------------------------
    |
    | Identifiers (email/phone), IPs and OTPs are protected with a "keyed" HMAC
    | (server-side secret key = "pepper"). To be able to rotate the pepper without
    | breaking already-stored hashes, each row stores the "key_version" it used.
    | Here we define the available versions and the active one.
    |
    | IMPORTANT: do NOT commit the real values. They go ONLY in .env / secrets.
    |
    */
    'peppers' => [
        1 => env('REBEL_PEPPER_V1', ''),
        // 2 => env('REBEL_PEPPER_V2', ''),  // add a new version to rotate
    ],

    // Pepper version used for NEW hashes. Verification tries the current one, then the deprecated ones.
    'pepper_current' => (int) env('REBEL_PEPPER_CURRENT', 1),

    // Algorithm for the HMACs. sha256 is the standard.
    'hmac_algo' => env('REBEL_HMAC_ALGO', 'sha256'),

    /*
    |--------------------------------------------------------------------------
    | Privacy (GDPR)
    |--------------------------------------------------------------------------
    |
    | For data minimization, IPs and User-Agents are stored as a (keyed) HMAC and
    | not in cleartext. A "plain" hash of an IPv4 would be reversible, so an HMAC
    | with a pepper is ALWAYS used.
    |
    */
    'hash_ip' => (bool) env('REBEL_HASH_IP', true),
    'hash_user_agent' => (bool) env('REBEL_HASH_USER_AGENT', true),

];
