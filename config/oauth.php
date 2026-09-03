<?php

declare(strict_types=1);

return [
    'nexusmods' => [
        'clientId' => env('NEXUS_MODS_CLIENT_ID', ''),
        'clientSecret' => env('NEXUS_MODS_CLIENT_SECRET', ''),
        'redirectUri' => env('NEXUS_MODS_REDIRECT_URI', ''),
    ],
];
