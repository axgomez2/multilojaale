<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Reset de estilos de email */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            color: #333333;
            background-color: #f5f5f5;
        }
        
        /* Container principal */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        
        /* Cabeçalho */
        .email-header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eeeeee;
        }
        
        .email-logo {
            max-width: 150px;
            height: auto;
        }
        
        /* Conteúdo */
        .email-content {
            padding: 30px 20px;
            line-height: 1.6;
        }
        
        /* Rodapé */
        .email-footer {
            text-align: center;
            padding: 15px 0;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #eeeeee;
        }
        
        /* Botão de ação */
        .btn-primary {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
        }
        
        .btn-primary:hover {
            background-color: #4338CA;
        }
        
        /* Mensagem de código fallback */
        .verification-code {
            font-family: monospace;
            font-size: 18px;
            background-color: #f8f8f8;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin: 20px 0;
            letter-spacing: 2px;
        }
        
        /* Disco de vinil decorativo */
        .vinyl-icon {
            width: 60px;
            height: 60px;
            background-color: #000000;
            border-radius: 50%;
            margin: 0 auto 20px;
            position: relative;
        }
        
        .vinyl-icon::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: #f5f5f5;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        /* Cores e elementos de destaque */
        .text-primary {
            color: #4F46E5;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-4 {
            margin-top: 20px;
        }
        
        .mb-4 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="vinyl-icon"></div>
            <h1 style="margin:0;color:#4F46E5;font-size:28px;">{{ config('app.name') }}</h1>
            <p style="margin:5px 0 0;color:#777777;">Sua Loja de Vinis</p>
        </div>
        
        <div class="email-content">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
            <p>Se você não solicitou este email, nenhuma ação é necessária.</p>
        </div>
    </div>
</body>
</html>
