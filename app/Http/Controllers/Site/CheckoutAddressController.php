<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutAddressController extends Controller
{
    /**
     * Carregar endereços do usuário.
     */
    public function loadUserAddresses()
    {
        if (auth()->check()) {
            $addresses = auth()->user()->addresses;
            
            return response()->json([
                'success' => true,
                'addresses' => $addresses,
                'has_addresses' => $addresses->isNotEmpty()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'addresses' => [],
            'has_addresses' => false
        ]);
    }
    
    /**
     * Salvar um novo endereço.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'zipcode' => 'required|string|size:9',
            'street' => 'required|string|max:200',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:20',
            'recipient_document' => 'nullable|string|max:20',
            'recipient_email' => 'nullable|email|max:100',
            'is_default_shipping' => 'boolean',
            'type' => 'required|in:shipping,billing,both',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Garantir que o CEP esteja no formato correto (00000-000)
        $formattedZipcode = $request->zipcode;
        if (strpos($formattedZipcode, '-') === false) {
            $zipcode = preg_replace('/[^0-9]/', '', $formattedZipcode);
            if (strlen($zipcode) === 8) {
                $formattedZipcode = substr($zipcode, 0, 5) . '-' . substr($zipcode, 5);
            }
        }
        
        try {
            $address = new Address();
            $address->name = $request->name;
            $address->zipcode = $formattedZipcode;
            $address->street = $request->street;
            $address->number = $request->number;
            $address->complement = $request->complement;
            $address->district = $request->district;
            $address->city = $request->city;
            $address->state = $request->state;
            $address->recipient_name = $request->recipient_name;
            $address->recipient_phone = $request->recipient_phone;
            $address->recipient_document = $request->recipient_document;
            $address->recipient_email = $request->recipient_email;
            $address->type = $request->type ?? 'shipping';
            $address->country = 'BR';
            
            // Se o usuário estiver logado, associar o endereço
            if (auth()->check()) {
                $user = auth()->user();
                
                // Se for o primeiro endereço ou marcado como padrão
                if ($request->is_default_shipping || $user->addresses()->count() === 0) {
                    // Remover o status de padrão de outros endereços
                    $user->addresses()->update(['is_default_shipping' => false]);
                    $address->is_default_shipping = true;
                } else {
                    $address->is_default_shipping = false;
                }
                
                // Default para cobrança
                $address->is_default_billing = false;
                
                $address->user_id = $user->id;
            } else {
                // Para usuários não autenticados, salvar na sessão
                $address->is_default_shipping = true;
                $address->is_default_billing = false;
                session(['temp_address' => $address->toArray()]);
            }
            
            if (auth()->check()) {
                $address->save();
            }
            
            // Armazenar o ID do endereço na sessão para uso no checkout
            session(['checkout_address_id' => auth()->check() ? $address->id : null]);
            // Não avançar automaticamente para o próximo passo
            // Manter o usuário na etapa de endereço para que ele possa confirmar
            session(['checkout_step' => 'address']);
            
            return response()->json([
                'success' => true,
                'address' => $address,
                'message' => 'Endereço salvo com sucesso.',
                'redirect' => route('site.checkout.index', [], false) // false para evitar a interrogação
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar o endereço: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Selecionar um endereço existente.
     */
    public function select(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|uuid|exists:addresses,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $address = Address::findOrFail($request->address_id);
            
            // Verificar se o endereço pertence ao usuário
            if (auth()->check() && $address->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endereço não pertence ao usuário logado.'
                ], 403);
            }
            
            // Armazenar o ID do endereço na sessão para uso no checkout
            session(['checkout_address_id' => $address->id]);
            session(['checkout_step' => 'shipping']);
            
            return response()->json([
                'success' => true,
                'address' => $address,
                'message' => 'Endereço selecionado com sucesso.',
                'redirect' => route('site.checkout.index')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao selecionar o endereço: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Consultar CEP e retornar dados do endereço.
     */
    public function lookupZipcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zipcode' => 'required|string|size:9',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $zipcode = preg_replace('/[^0-9]/', '', $request->zipcode);
        
        try {
            // Consultar CEP usando um serviço externo
            $response = file_get_contents("https://viacep.com.br/ws/{$zipcode}/json/");
            $data = json_decode($response);
            
            if (isset($data->erro) && $data->erro) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP não encontrado.'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'address' => [
                    'street' => $data->logradouro,
                    'neighborhood' => $data->bairro,
                    'city' => $data->localidade,
                    'state' => $data->uf,
                    'zipcode' => $zipcode,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar o CEP: ' . $e->getMessage()
            ], 500);
        }
    }
}
