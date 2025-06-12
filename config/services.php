<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    
    'melhorenvio' => [
        'url' => env('MELHOR_ENVIO_URL', 'https://sandbox.melhorenvio.com.br/api/v2/'),
        'token' => env('MELHOR_ENVIO_TOKEN'),
        'store_zip' => env('STORE_ZIP_CODE', '01001000'), // CEP padrão da loja em São Paulo
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI')
    ],

    'discogs' => [
        'token' => env('DISCOGS_TOKEN')
    ],
    
    'mercadopago' => [
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'sandbox' => env('MERCADOPAGO_SANDBOX', true),
        'webhook_url' => env('MERCADOPAGO_WEBHOOK_URL'),
        'site_id' => 'MLB'  // MLB = Brasil
    ],

    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'),
    ],  


];
