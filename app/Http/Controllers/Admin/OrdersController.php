<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersController extends Controller
{
    protected $melhorEnvioApiUrl = 'https://sandbox.melhorenvio.com.br/api/v2/';
    
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        // Obter filtros da query
        $status = $request->input('status');
        $date = $request->input('date');
        $search = $request->input('search');
        
        // Consulta base
        $query = Order::query()->with(['user', 'payment']);
        
        // Aplicar filtros se fornecidos
        if ($status) {
            // Usar o valor correto do enum para filtrar por status
            $query->where('status', $status);
        }
        
        if ($date) {
            // Filtrar por data (hoje, semana, mês)
            if ($date == 'today') {
                $query->whereDate('created_at', now()->toDateString());
            } elseif ($date == 'week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($date == 'month') {
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            }
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Ordenar e paginar resultados
        $orders = $query->orderBy('created_at', 'desc')
                       ->paginate(15)
                       ->withQueryString();
        
        // Obter contadores para dashboard usando valores de enum
        $counters = [
            'total' => Order::count(),
            'pending' => Order::where('status', OrderStatus::PENDING->value)->count(),
            'payment_approved' => Order::where('status', OrderStatus::PAYMENT_APPROVED->value)->count(),
            'delivered' => Order::where('status', OrderStatus::DELIVERED->value)->count(),
            'canceled' => Order::where('status', OrderStatus::CANCELED->value)->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'counters', 'status', 'date', 'search'));
    }
    
    /**
     * Display the specified order details.
     */
    public function show(Order $order)
    {
        $order->load(['items.vinylMaster.artists', 'user', 'address', 'payment']);
        return view('admin.orders.show', compact('order'));
    }
    
    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,payment_approved,preparing,shipped,delivered,canceled',
        ]);
        
        // Status anterior para comparação
        $oldStatus = $order->status;
        
        // Definindo o status principal como um valor de enum
        switch ($validated['status']) {
            case 'pending':
                $order->status = OrderStatus::PENDING->value;
                break;
            case 'payment_approved':
                $order->status = OrderStatus::PAYMENT_APPROVED->value;
                // Atualizar o payment_status quando o pagamento for aprovado
                $order->payment_status = 'approved';
                break;
            case 'preparing':
                $order->status = OrderStatus::PREPARING->value;
                // Verificar se o pagamento está aprovado, caso contrário é inconsistente
                if ($order->payment_status !== 'approved') {
                    $order->payment_status = 'approved'; // Sincronizar
                }
                break;
            case 'shipped':
                $order->status = OrderStatus::SHIPPED->value;
                // Atualizar shipping_status para refletir que o pedido foi enviado
                $order->shipping_status = 'shipped';
                break;
            case 'delivered':
                $order->status = OrderStatus::DELIVERED->value;
                // Atualizar shipping_status para refletir a entrega
                $order->shipping_status = 'delivered';
                break;
            case 'canceled':
                $order->status = OrderStatus::CANCELED->value;
                break;
            default:
                // Se receber um status não reconhecido, volta para página com erro
                return redirect()->back()->with('error', 'Status inválido');
        }
        
        // Validar a consistência entre os status
        $this->validateStatusConsistency($order);
        
        $order->save();
        
        // Mensagem mais detalhada quando houver mudança de status
        if ($oldStatus !== $order->status) {
            $message = "Status do pedido atualizado de '" . $this->getStatusLabel($oldStatus) . "' para '" . $this->getStatusLabel($order->status) . "'";
            return redirect()->back()->with('success', $message);
        }
        
        return redirect()->back()->with('success', 'Status do pedido atualizado com sucesso!');
    }
    
    /**
     * Generate shipping label using Melhor Envio integration.
     */
    public function generateShippingLabel(Order $order)
    {
        // Status elegíveis para geração de etiqueta
        // Somente pedidos pagos, em preparação ou já enviados (para regeneração)
        $statusPermitidos = [
            OrderStatus::PAYMENT_APPROVED->value, 
            OrderStatus::PREPARING->value,
            OrderStatus::SHIPPED->value   // Permitir regeneração mesmo para pedidos já enviados
        ];
        
        // Garantir que temos uma string para comparação
        $orderStatus = $order->status;
        if (is_object($orderStatus) && method_exists($orderStatus, 'value')) {
            $orderStatus = $orderStatus->value;
        }
        
        // Verificar se o status é elegível
        if (!in_array($orderStatus, $statusPermitidos)) {
            $statusLabels = implode(', ', array_map(function($status) {
                return $this->getStatusLabel($status);
            }, $statusPermitidos));
            
            return redirect()->back()->with('error', "Este pedido não está em um status elegível para gerar etiqueta de envio. Status permitidos: {$statusLabels}.");
        }
        
        // Verificar se o pedido tem endereço de entrega
        if (!$order->address) {
            return redirect()->back()->with('error', 'O pedido não possui endereço de entrega.');
        }
        
        try {
            // Simular a preparação dos dados para o Melhor Envio
            // Na implementação real, você faria uma chamada à API do Melhor Envio aqui
            
            // Construir os dados para a etiqueta baseado no pedido
            $packageData = $this->preparePackageData($order);
            
            // Em uma implementação real, você enviaria esses dados para o Melhor Envio
            // e receberia o link da etiqueta ou o PDF
            
            // Simulação de chamada para API do Melhor Envio
            $response = $this->simulateMelhorEnvioRequest($packageData);
            
            if ($response['success']) {
                // Simulando URL da etiqueta gerada
                $labelUrl = $response['label_url'];
                
                // Verificar se o pedido já tem uma etiqueta gerada
                $isRegeneration = !empty($order->shipping_label_url);
                
                // Salvar a URL da etiqueta no pedido
                $order->shipping_label_url = $labelUrl;
                $order->save();
                
                // Atualizar o status do pedido para enviado
                $order->status = 'shipped';
                $order->shipping_status = 'label_generated';
                $order->shipping_tracking = 'ME' . rand(1000000, 9999999); // Simulação de código de rastreio
                $order->save();
                
                // Mensagem apropriada para geração ou regeneração
                $message = $isRegeneration ? 'Etiqueta de envio regenerada com sucesso!' : 'Etiqueta de envio gerada com sucesso!';
                
                // Redirecionar de volta com mensagem de sucesso e URL da etiqueta
                return redirect()->back()->with([
                    'success' => $message,
                    'label_url' => $labelUrl
                ]);
            } else {
                // Redirecionar de volta com mensagem de erro
                return redirect()->back()->with('error', 'Erro ao gerar etiqueta: ' . $response['message']);
            }
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao gerar etiqueta: ' . $e->getMessage());
        }
    }
    
    /**
     * Prepare package data for Melhor Envio API
     */
    private function preparePackageData(Order $order)
    {
        // Obter dados do endereço de entrega
        $shippingAddress = $order->address;
        
        // Preparar informações do destinatário
        $recipient = [
            'name' => $order->user->name,
            'phone' => $order->user->phone ?? '(00) 00000-0000',
            'email' => $order->user->email,
            'address' => $shippingAddress->street,
            'number' => $shippingAddress->number,
            'complement' => $shippingAddress->complement,
            'district' => $shippingAddress->district,
            'city' => $shippingAddress->city,
            'state' => $shippingAddress->state,
            'postal_code' => $shippingAddress->zipcode
        ];
        
        // Calcular peso e dimensões com base nos itens do pedido
        $totalWeight = 0;
        $items = [];
        
        foreach ($order->items as $item) {
            $weight = $item->vinylSec->weight ?? 0.3; // Peso padrão se não estiver definido
            $totalWeight += $weight * $item->quantity;
            
            $items[] = [
                'name' => $item->name,
                'quantity' => $item->quantity,
                'weight' => $weight
            ];
        }
        
        // Dados da embalagem (valores padrão para discos de vinil)
        $package = [
            'weight' => $totalWeight,
            'width' => 32, // Largura típica de uma embalagem para disco de vinil em cm
            'height' => 32, // Altura típica de uma embalagem para disco de vinil em cm
            'length' => 5 * count($order->items), // Espessura estimada baseada no número de itens
        ];
        
        return [
            'order_id' => $order->id,
            'recipient' => $recipient,
            'package' => $package,
            'items' => $items,
        ];
    }
    
    /**
     * Validate and ensure consistency between different status fields
     */
    private function validateStatusConsistency(Order $order)
    {
        // Garantir que payment_status e status geral sejam consistentes
        if (in_array($order->status, [OrderStatus::PAYMENT_APPROVED->value, OrderStatus::PREPARING->value, 
                                     OrderStatus::SHIPPED->value, OrderStatus::DELIVERED->value])) {
            // Todos esses status exigem que o pagamento esteja aprovado
            if ($order->payment_status !== 'approved') {
                $order->payment_status = 'approved';
            }
        }
        
        // Garantir que shipping_status e status geral sejam consistentes
        if ($order->status === OrderStatus::SHIPPED->value && $order->shipping_status !== 'shipped') {
            $order->shipping_status = 'shipped';
        } else if ($order->status === OrderStatus::DELIVERED->value && $order->shipping_status !== 'delivered') {
            $order->shipping_status = 'delivered';
        }
        
        // Se o pedido for cancelado
        if ($order->status === OrderStatus::CANCELED->value) {
            // Se tiver sido enviado, o shipping_status continua como estava
            // Caso contrário, cancelamos também o shipping_status
            if (!in_array($order->shipping_status, ['shipped', 'delivered'])) {
                $order->shipping_status = 'cancelled';
            }
        }
        
        return $order;
    }
    
    /**
     * Get human-readable status label
     */
    private function getStatusLabel($status)
    {
        // Extrair o valor do enum se for um objeto OrderStatus
        if (is_object($status) && $status instanceof OrderStatus) {
            $status = $status->value;
        }
        
        $labels = [
            'pending' => 'Aguardando Pagamento',
            'payment_approved' => 'Pagamento Aprovado',
            'preparing' => 'Em Preparação',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'canceled' => 'Cancelado'
        ];
        
        return $labels[$status] ?? ucfirst($status);
    }
    
    /**
     * Simulate API request to Melhor Envio
     * In a real implementation, this would make an actual API request
     */
    private function simulateMelhorEnvioRequest($packageData)
    {
        // Aqui você faria a solicitação real à API do Melhor Envio
        // Exemplo com Guzzle (que você precisaria instalar):
        // $client = new \GuzzleHttp\Client();
        // $response = $client->post($this->melhorEnvioApiUrl . 'shipments/generate', [
        //     'headers' => [
        //         'Accept' => 'application/json',
        //         'Content-Type' => 'application/json',
        //         'Authorization' => 'Bearer ' . config('services.melhorenvio.token')
        //     ],
        //     'json' => $packageData
        // ]);
        // $result = json_decode($response->getBody()->getContents());
        
        // Simular uma resposta bem-sucedida (em produção seria a resposta real da API)
        // Gerando um ID de rastreamento único para simular diferentes etiquetas
        $trackingId = 'ME' . date('Ymd') . rand(1000, 9999);
        $labelUrl = 'https://sandbox.melhorenvio.com.br/etiquetas/' . $trackingId . '.pdf';
        
        // Em uma implementação real, você trataria erros e retornaria mensagens apropriadas
        return [
            'success' => true,
            'label_url' => $labelUrl,
            'tracking_code' => $trackingId,
            'message' => 'Etiqueta gerada com sucesso!'
        ];
    }
}
