<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API PORTICO
    |--------------------------------------------------------------------------
    | URL base y API key de la API intermediaria (ResellerClub) a la que el
    | comando "sync:portico" envía los datos para la app de escritorio PorticoVV.
    | La API key debe coincidir con la configurada en la API y en PorticoVV.
    */
    'api_url' => env('PORTICO_API_URL'),
    'api_key' => env('PORTICO_API_KEY'),
];
