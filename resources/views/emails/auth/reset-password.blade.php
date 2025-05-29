@extends('emails.layouts.base')

@section('content')
<h2 style="color:#333333;font-size:22px;margin-top:0;">Recuperação de Senha</h2>

<p>Olá,</p>

<p>Você está recebendo este email porque recebemos uma solicitação de recuperação de senha para sua conta.</p>

<p>Clique no botão abaixo para redefinir sua senha:</p>

<div class="text-center">
    <a href="{{ $resetUrl }}" class="btn-primary" style="color:#ffffff;text-decoration:none;">Redefinir Senha</a>
</div>

<p class="mt-4">Este link expirará em {{ config('auth.passwords.users.expire', 60) }} minutos.</p>

<p>Se você não conseguir clicar no botão acima, copie e cole o link abaixo no seu navegador:</p>
<p style="word-break:break-all;font-size:14px;color:#666666;">{{ $resetUrl }}</p>

<p class="mt-4">Se você não solicitou a recuperação de senha, nenhuma ação adicional é necessária.</p>

<div class="text-center mt-4">
    <p style="font-size:14px;color:#777777;">Volte logo para descobrir mais sobre nossa coleção exclusiva de vinis!</p>
</div>
@endsection
