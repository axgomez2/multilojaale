<x-app-layout>
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-8">
    <div class="w-full max-w-xl bg-white p-8 rounded-2xl shadow border border-gray-200">
        <!-- Mensagem de Boas-Vindas -->
        <div class="mb-6 text-sm text-gray-600">
            <p>
                Obrigado por se registrar! Antes de começar, verifique seu endereço de e-mail clicando no link que acabamos de enviar para você. <br>
                Se você não recebeu o e-mail, teremos prazer em enviar outro.
            </p>
        </div>

        <!-- Mensagem de status -->
        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 flex items-center p-4 text-sm text-green-800 rounded-lg bg-green-100" role="alert">
                ✅ Um novo link de verificação foi enviado para o endereço de e-mail fornecido durante o registro.
            </div>
        @endif

        <!-- Formulários -->
        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <!-- Botão de reenviar e-mail -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    Reenviar e-mail de verificação
                </button>
            </form>

            <!-- Botão de logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sair
                </button>
            </form>
        </div>
    </div>
</div>

</x-app-layout>
