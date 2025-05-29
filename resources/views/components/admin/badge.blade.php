@props(['status'])

@php
// Manipulação segura de enums
if ($status instanceof \App\Enums\OrderStatus) {
    $statusValue = $status->value;
} else {
    $statusValue = $status;
}

// Garantir que temos uma string
$statusStr = (string) $statusValue;

$colors = [
    'pending' => 'bg-yellow-500',
    'payment_approved' => 'bg-green-500',
    'preparing' => 'bg-purple-500',
    'shipped' => 'bg-indigo-500',
    'delivered' => 'bg-blue-500',
    'canceled' => 'bg-red-500',
];
@endphp

<span class="px-2 py-1 text-xs rounded text-white {{ $colors[$statusStr] ?? 'bg-gray-500' }}">
    {{ ucfirst(str_replace('_', ' ', $statusStr)) }}
</span>
