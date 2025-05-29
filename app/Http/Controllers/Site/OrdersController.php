<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Exibir a lista de pedidos do cliente.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('site.profile.orders.index', compact('orders'));
    }
    
    /**
     * Exibir os detalhes de um pedido específico.
     */
    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['items.vinylMaster.artists', 'address', 'payment'])
            ->firstOrFail();
            
        return view('site.profile.orders.show', compact('order'));
    }
}
