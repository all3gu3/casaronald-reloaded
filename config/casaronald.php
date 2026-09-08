<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cuenta maestra
    |--------------------------------------------------------------------------
    |
    | La siembra (Database\Seeders\MasterUserSeeder) crea o actualiza la cuenta
    | maestra con estos valores. Viven en config (y no en env() directo) para
    | que sigan funcionando cuando la configuración está cacheada en producción.
    |
    */

    'master' => [
        'name' => env('MASTER_NAME', 'Administración Casa Ronald'),
        'email' => env('MASTER_EMAIL', 'master@casaronald.local'),
        'password' => env('MASTER_PASSWORD', 'cambiame-al-instalar'),
    ],

];
