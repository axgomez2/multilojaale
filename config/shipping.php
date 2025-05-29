<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações de Frete
    |--------------------------------------------------------------------------
    |
    | Este arquivo contém as configurações relacionadas ao cálculo de frete
    | e integrações com serviços de entrega.
    |
    */

    // CEP de origem (da loja)
    'zip_from' => '09220360',
    
    // Serviços de frete habilitados
    'enabled_services' => [1, 2, 3, 4, 15, 16],  // Códigos dos serviços no Melhor Envio
    
    // Tempo máximo de expiração das cotações em dias
    'quote_expiration_days' => 1,
];
