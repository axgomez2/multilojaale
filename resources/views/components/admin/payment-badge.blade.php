@props(['payment'])

@php
$methods = [
    'credit_card' => ['label' => 'Cartão de Crédito', 'color' => 'bg-blue-400'],
    'pix' => ['label' => 'PIX', 'color' => 'bg-green-400'],
    'boleto' => ['label' => 'Boleto', 'color' => 'bg-gray-500'],
];
@endphp

@if($payment)
    @php
    // Garantir que temos uma string do método de pagamento
    $paymentMethod = $payment->payment_method;
    
    // Se for um enum, pegar o valor
    if (is_object($paymentMethod) && method_exists($paymentMethod, 'value')) {
        $paymentMethod = $paymentMethod->value;
    }
    
    // Forçar para string
    $paymentMethodStr = (string) $paymentMethod;
    @endphp
    
    <span class="px-2 py-1 text-xs rounded text-white {{ $methods[$paymentMethodStr]['color'] ?? 'bg-gray-400' }}">
        {{ $methods[$paymentMethodStr]['label'] ?? ucfirst($paymentMethodStr) }}
    </span>
@else
    <span class="px-2 py-1 text-xs rounded text-white bg-red-500">Não informado</span>
@endif
