{{-- Seção de Etiquetas de Envio --}}
<div class="border rounded p-3 mb-3 bg-gray-50">
    <h4 class="mb-2 font-bold text-gray-700"><i class="fas fa-tag mr-1"></i> Etiqueta de Envio</h4>
    
    @php
        // Obtém o valor de status de forma segura
        $statusValue = is_string($order->status) ? $order->status : (is_object($order->status) && method_exists($order->status, 'value') ? $order->status->value : 'unknown');
    @endphp
    
    @if($order->shipping_label_url)
        {{-- Já tem etiqueta gerada --}}
        <div class="mb-2 p-2 bg-green-100 text-green-700 rounded">
            <i class="fas fa-check-circle"></i> Etiqueta já gerada
        </div>
        <div class="flex flex-col gap-2 mb-2">
            <a href="{{ $order->shipping_label_url }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center">
                <i class="fas fa-download mr-2"></i> Baixar Etiqueta
            </a>
            <a href="{{ route('admin.orders.shipping-label', $order->id) }}" class="px-4 py-2 border border-blue-600 text-blue-600 rounded hover:bg-blue-50 flex items-center justify-center">
                <i class="fas fa-sync-alt mr-2"></i> Regenerar Etiqueta
            </a>
        </div>
    @elseif($statusValue == 'payment_approved' || $statusValue == 'preparing' || $statusValue == 'shipped')
        {{-- Elegível para gerar etiqueta --}}
        <a href="{{ route('admin.orders.shipping-label', $order->id) }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center justify-center">
            <i class="fas fa-shipping-fast mr-2"></i> Gerar Etiqueta Melhor Envio
        </a>
    @else
        {{-- Não elegível para etiqueta --}}
        <div class="p-2 bg-gray-100 text-gray-600 rounded">
            <i class="fas fa-info-circle mr-1"></i> Status atual não permite gerar etiqueta
        </div>
    @endif
</div>
