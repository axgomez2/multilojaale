<?php

return [
    'base_url' => env('MELHORENVIO_ENV') === 'production'
        ? 'https://api.melhorenvio.com.br'
        : 'https://sandbox.melhorenvio.com.br',

    'token' => env('MELHORENVIO_ENV') === 'production'
        ? env('MELHORENVIO_TOKEN_PRODUCTION')
        : env('MELHORENVIO_TOKEN_SANDBOX'),
        
    'from' => [
        'postal_code' => env('MELHORENVIO_FROM_POSTAL_CODE', '09220360'),
        'address' => env('MELHORENVIO_FROM_ADDRESS', 'montevideu'),
        'number' => env('MELHORENVIO_FROM_NUMBER', '174'),
        'complement' => env('MELHORENVIO_FROM_COMPLEMENT', ''),
        'district' => env('MELHORENVIO_FROM_DISTRICT', 'utinga'),
        'city' => env('MELHORENVIO_FROM_CITY', 'santo andré'),
        'state_abbr' => env('MELHORENVIO_FROM_STATE', 'sp'),
        'country' => env('MELHORENVIO_FROM_COUNTRY', 'BR'),
        'phone' => env('MELHORENVIO_FROM_PHONE', '5511940464843'),
        'email' => env('MELHORENVIO_FROM_EMAIL', 'ax.gomez2@gmail.com'),
        'document' => env('MELHORENVIO_FROM_DOCUMENT', '28856412829'),
    ],
    
    'options' => [
        'receipt' => env('MELHORENVIO_RECEIPT', false),
        'own_hand' => env('MELHORENVIO_OWN_HAND', false),
        'collect' => env('MELHORENVIO_COLLECT', false),
        'non_commercial' => env('MELHORENVIO_NON_COMMERCIAL', true),
    ],
    
    /*
     * Serviços disponíveis no Melhor Envio:
     * 1 = PAC
     * 2 = SEDEX
     * 3 = Jadlog Package
     * 4 = Jadlog Com
     * 17 = Mini Envios
     * 22 = Azul Cargo Express
     * 37 = Latam Cargo
     */
    'services' => [1, 2, 3, 4, 17, 22, 37], // Todos os serviços disponíveis
    
    'sandbox' => env('MELHORENVIO_ENV', 'sandbox') !== 'production',
];
