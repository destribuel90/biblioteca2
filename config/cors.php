<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Aquí se configuran los ajustes para la gestión de CORS. Esto determina
    | qué operaciones de origen cruzado pueden ejecutarse en el navegador.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // El comodín '*' permite peticiones desde cualquier origen (Live Server, file://, o dominios externos)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Permite la cabecera 'Authorization' para enviar el token Bearer

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // Se establece en false al usar tokens Bearer con 'allowed_origins' => ['*']

];