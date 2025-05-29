@extends('emails.layouts.base')

@section('content')
<h2 style="color:#333333;font-size:22px;margin-top:0;">Verifique seu endereço de email</h2>

<p>Olá {{ $user->name }},</p>

<p>Obrigado por se cadastrar na nossa loja de vinis! Antes de começar a explorar nossa coleção exclusiva, precisamos verificar seu endereço de email.</p>

<p>Clique no botão abaixo para confirmar seu email e ter acesso completo à sua conta:</p>

<div class="text-center">
    <a href="{{ $verificationUrl }}" class="btn-primary" style="color:#ffffff;text-decoration:none;">Verificar meu email</a>
</div>

<p class="mt-4">Este link expirará em {{ config('auth.passwords.users.expire', 60) }} minutos.</p>

<p>Se você não conseguir clicar no botão acima, copie e cole o link abaixo no seu navegador:</p>
<p style="word-break:break-all;font-size:14px;color:#666666;">{{ $verificationUrl }}</p>

<p class="mt-4">Se você não criou uma conta, nenhuma ação adicional é necessária.</p>

<div class="text-center mt-4">
    <p style="font-size:14px;color:#777777;">Estamos ansiosos para te apresentar nossa coleção exclusiva de vinis!</p>
</div>
@endsection
