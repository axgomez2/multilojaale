<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\User;
use App\Models\VinylSec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosSalesController extends Controller
{
    /**
     * Exibe a página principal do PDV
     */
    public function index()
    {
        $recentSales = PosSale::with('items.vinyl.vinylMaster')
            ->latest()
            ->take(5)
            ->get();
            
        return view('admin.pos.index', compact('recentSales'));
    }
    
    /**
     * Exibe a página de nova venda
     */
    public function create()
    {
        return view('admin.pos.create');
    }
    
    /**
     * Busca usuários para autocompletar
     */
    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'email']);
            
        return response()->json($users);
    }
    
    /**
     * Busca vinis para adicionar à venda
     */
    public function searchVinyls(Request $request)
    {
        $query = $request->input('query');
        
        $vinyls = VinylSec::with(['vinylMaster', 'vinylMaster.artists'])
            ->where('in_stock', true)
            ->where(function($q) use ($query) {
                $q->whereHas('vinylMaster', function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%");
                })
                ->orWhereHas('vinylMaster.artists', function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->orWhere('catalog_number', 'like', "%{$query}%");
            })
            ->take(10)
            ->get();
            
        return response()->json($vinyls->map(function($vinyl) {
            return [
                'id' => $vinyl->id,
                'title' => $vinyl->vinylMaster->title,
                'artist' => $vinyl->vinylMaster->artists->pluck('name')->implode(', '),
                'catalog_number' => $vinyl->catalog_number,
                'price' => $vinyl->price,
                'cover_image' => $vinyl->vinylMaster->cover_image,
            ];
        }));
    }
    
    /**
     * Processa a venda
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.vinyl_sec_id' => 'required|exists:vinyl_secs,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.item_discount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Cálculos dos valores
            $subtotal = collect($validated['items'])->sum(function ($item) {
                return $item['price'] * $item['quantity'] - ($item['item_discount'] ?? 0);
            });
            
            $discount = $validated['discount'] ?? 0;
            $shipping = $validated['shipping'] ?? 0;
            $total = $subtotal - $discount + $shipping;
            
            // Criar a venda
            $sale = PosSale::create([
                'user_id' => $validated['user_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? 'Venda no Balcão',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'invoice_number' => PosSale::generateInvoiceNumber(),
            ]);
            
            // Adicionar os itens e atualizar o estoque
            foreach ($validated['items'] as $itemData) {
                $vinyl = VinylSec::findOrFail($itemData['vinyl_sec_id']);
                
                // Verifica se o disco está em estoque
                if (!$vinyl->in_stock) {
                    throw new \Exception("O disco {$vinyl->vinylMaster->title} não está mais disponível.");
                }
                
                // Criar o item da venda
                $itemTotal = $itemData['price'] * $itemData['quantity'] - ($itemData['item_discount'] ?? 0);
                
                PosSaleItem::create([
                    'pos_sale_id' => $sale->id,
                    'vinyl_sec_id' => $vinyl->id,
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'item_discount' => $itemData['item_discount'] ?? 0,
                    'item_total' => $itemTotal,
                ]);
                
                // Atualizar o estoque
                $vinyl->in_stock = false;
                $vinyl->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.pos.show', $sale)
                ->with('success', 'Venda realizada com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Erro ao processar a venda: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibe detalhes da venda
     */
    public function show(PosSale $posSale)
    {
        $sale = $posSale->load('items.vinyl.vinylMaster', 'user');
        
        return view('admin.pos.show', compact('sale'));
    }
    
    /**
     * Lista todas as vendas
     */
    public function list()
    {
        $sales = PosSale::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.pos.list', compact('sales'));
    }
}
