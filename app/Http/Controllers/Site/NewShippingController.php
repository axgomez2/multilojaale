<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\MelhorEnvioService;

class NewShippingController extends Controller
{
    /**
     * Constructor do controlador
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Exibe a página de frete com resumo do pedido
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obter os itens do carrinho com relacionamentos necessários
        $cartItems = $user->cartItems()
                          ->with(['product.productable', 'vinylMaster.vinylSec', 'vinylMaster.artists'])
                          ->where('saved_for_later', false)
                          ->get();

        // Se não tiver itens no carrinho, redirecionar para o carrinho
        if ($cartItems->isEmpty()) {
            return redirect()->route('site.cart.index')->with('error', 'Seu carrinho está vazio.');
        }
        
        // Calcular subtotal do pedido
        $subtotal = 0;
        foreach ($cartItems as $item) {
            // Usar o método getSubtotalAttribute ou o preço direto dependendo de como estiver implementado
            $itemPrice = $item->price ?? ($item->product->price ?? ($item->vinylMaster->vinylSec->price ?? 0));
            $subtotal += $itemPrice * $item->quantity;
        }
        
        // Obter o carrinho do usuário
        $cart = Cart::where('user_id', $user->id)
                   ->first();
        
        // Verificar se o usuário tem os dados básicos preenchidos (CPF, telefone, email)
        $userHasRequiredData = !empty($user->cpf) && !empty($user->phone) && !empty($user->email);
        
        // Obter os endereços do usuário
        $addresses = Address::where('user_id', $user->id)
                           ->where('is_active', true)
                           ->get();
        
        // Verificar se o usuário tem pelo menos um endereço cadastrado
        $userHasAddress = $addresses->count() > 0;
        
        // Obter as opções de frete da sessão se já foram calculadas anteriormente
        $shippingOptions = Session::get('shipping_options', []);
        $selectedAddress = Session::get('selected_address', null);
        $selectedShipping = Session::get('selected_shipping', null);

        return view('site.shipping.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'cart' => $cart,
            'user' => $user,
            'userHasRequiredData' => $userHasRequiredData,
            'addresses' => $addresses,
            'userHasAddress' => $userHasAddress,
            'shippingOptions' => $shippingOptions,
            'selectedAddress' => $selectedAddress,
            'selectedShipping' => $selectedShipping
        ]);
    }
    
    /**
     * Salva os dados básicos do usuário (CPF, telefone)
     */
    public function saveUserData(Request $request)
    {
        $validated = $request->validate([
            'cpf' => 'required|string|min:11|max:14',
            'phone' => 'required|string|min:10|max:15',
        ]);
        
        $user = Auth::user();
        $user->cpf = $validated['cpf'];
        $user->phone = $validated['phone'];
        $user->save();
        
        return redirect()->route('site.shipping.index')
                         ->with('success', 'Dados atualizados com sucesso!');
    }
    
    /**
     * Salva um novo endereço para o usuário
     */
    public function saveAddress(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:100',
            'recipient_document' => 'required|string|max:20',
            'recipient_phone' => 'required|string|max:20',
            'zipcode' => 'required|string|max:9',
            'street' => 'required|string|max:200',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
        ]);
        
        $user = Auth::user();
        
        $address = new Address();
        // Definir nome do endereço (ex: Casa, Trabalho)
        $address->name = 'Endereço ' . (Address::where('user_id', $user->id)->count() + 1);
        $address->type = 'both'; // shipping e billing
        $address->user_id = $user->id;
        
        // Dados do destinatário
        $address->recipient_name = $validated['recipient_name'];
        $address->recipient_document = $validated['recipient_document'];
        $address->recipient_phone = $validated['recipient_phone'];
        $address->recipient_email = $user->email; // Usar email do usuário atual
        
        // Endereço
        $address->zipcode = $validated['zipcode'];
        $address->street = $validated['street'];
        $address->number = $validated['number'];
        $address->complement = $validated['complement'] ?? null;
        $address->district = $validated['district'];
        $address->city = $validated['city'];
        $address->state = $validated['state'];
        $address->country = 'BR';
        
        // Se for o primeiro endereço, marca como default para shipping e billing
        $existingAddresses = Address::where('user_id', $user->id)->count();
        if ($existingAddresses == 0) {
            $address->is_default_shipping = true;
            $address->is_default_billing = true;
        }
        
        $address->is_active = true;
        $address->created_by = $user->id;
        
        $address->save();
        
        // Armazenar o endereço selecionado na sessão
        Session::put('selected_address', $address->id);
        
        // Limpar opções de frete anteriores
        Session::forget(['shipping_options', 'selected_shipping']);
        
        return redirect()->route('site.shipping.index')
                         ->with('success', 'Endereço cadastrado com sucesso!');
    }
    
    /**
     * Seleciona um endereço para entrega
     */
    public function selectAddress(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);
        
        // Verificar se o endereço pertence ao usuário atual
        $user = Auth::user();
        $address = Address::where('user_id', $user->id)
                         ->where('id', $request->address_id)
                         ->firstOrFail();
        
        // Armazenar o endereço selecionado na sessão
        Session::put('selected_address', $address->id);
        
        // Limpar opções de frete anteriores
        Session::forget(['shipping_options', 'selected_shipping']);
        
        // Calcular opções de frete usando o Melhor Envio
        $cart = Cart::where('user_id', $user->id)
            ->first();
            
        if ($cart) {
            $melhorEnvio = new MelhorEnvioService();
            $shippingOptions = $melhorEnvio->calculateShipping($address, $cart);
            
            // Armazenar as opções de frete na sessão
            Session::put('shipping_options', $shippingOptions);
        } else {
            // Fallback se não houver carrinho ativo
            $shippingOptions = [
                ['id' => 1, 'name' => 'PAC', 'price' => 15.90, 'days' => 7, 'company' => 'Correios', 'error' => true],
                ['id' => 2, 'name' => 'SEDEX', 'price' => 25.50, 'days' => 3, 'company' => 'Correios', 'error' => true],
            ];
            Session::put('shipping_options', $shippingOptions);
        }
        
        return redirect()->route('site.shipping.index')
                         ->with('success', 'Endereço selecionado com sucesso!');
    }
    
    /**
     * Seleciona uma opção de frete
     */
    public function selectShipping(Request $request)
    {
        $validated = $request->validate([
            'shipping_id' => 'required|integer',
        ]);
        
        // Verificar se a opção de frete existe nas opções disponíveis
        $shippingOptions = Session::get('shipping_options', []);
        $selectedOption = null;
        
        foreach ($shippingOptions as $option) {
            if ($option['id'] == $validated['shipping_id']) {
                $selectedOption = $option;
                break;
            }
        }
        
        if (!$selectedOption) {
            return redirect()->route('site.shipping.index')
                             ->with('error', 'Opção de frete inválida!');
        }
        
        // Armazenar a opção de frete na sessão
        Session::put('selected_shipping', $selectedOption);
        
        // Atualizar o carrinho com o valor do frete
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)
                   ->first();
        
        if ($cart) {
            $cart->shipping_cost = $selectedOption['price'];
            $cart->total = $cart->subtotal + $cart->shipping_cost - $cart->discount;
            $cart->save();
        }
        
        return redirect()->route('site.shipping.index')
                         ->with('success', 'Opção de frete selecionada!');
    }
}
