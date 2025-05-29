<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Exibe a lista de endereços do usuário.
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->where('is_active', true)->get();
        
        return view('site.profile.addresses.index', [
            'addresses' => $addresses
        ]);
    }

    /**
     * Exibe o formulário para criar um novo endereço.
     */
    public function create()
    {
        return view('site.profile.addresses.create');
    }

    /**
     * Armazena um novo endereço.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'recipient' => 'required|string|max:100',
            'type' => ['required', 'string', Rule::in(['residential', 'commercial', 'other'])],
            'zipcode' => 'required|string|size:9',
            'state' => 'required|string|size:2',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'street' => 'required|string|max:200',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'reference' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $data = $validator->validated();
        $data['user_id'] = $user->id;
        
        // Se for o primeiro endereço do usuário, define como padrão automaticamente
        if ($user->addresses()->count() === 0) {
            $data['is_default'] = true;
        }
        
        $address = Address::create($data);
        
        // Se marcado como padrão, garante que seja o único padrão
        if ($request->has('is_default') && $request->is_default) {
            $address->setAsDefault();
        }
        
        return redirect()->route('site.profile.addresses.index')
                         ->with('success', 'Endereço adicionado com sucesso!');
    }

    /**
     * Exibe o formulário para editar um endereço.
     */
    public function edit(string $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        
        return view('site.profile.addresses.edit', [
            'address' => $address
        ]);
    }

    /**
     * Atualiza um endereço.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'recipient' => 'required|string|max:100',
            'type' => ['required', 'string', Rule::in(['residential', 'commercial', 'other'])],
            'zipcode' => 'required|string|size:9',
            'state' => 'required|string|size:2',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'street' => 'required|string|max:200',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'reference' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->update($validator->validated());
        
        // Se marcado como padrão, garante que seja o único padrão
        if ($request->has('is_default') && $request->is_default) {
            $address->setAsDefault();
        }
        
        return redirect()->route('site.profile.addresses.index')
                         ->with('success', 'Endereço atualizado com sucesso!');
    }

    /**
     * Remove (desativa) um endereço.
     */
    public function destroy(string $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        
        // Impede a exclusão se for o único endereço e estiver marcado como padrão
        if ($address->is_default && Auth::user()->addresses()->where('is_active', true)->count() <= 1) {
            return redirect()->back()->with('error', 'Não é possível excluir o único endereço padrão.');
        }
        
        // Desativa o endereço em vez de excluí-lo permanentemente
        $address->update(['is_active' => false]);
        
        // Se o endereço removido era o padrão, define outro como padrão
        if ($address->is_default) {
            $newDefault = Auth::user()->addresses()->where('is_active', true)->first();
            if ($newDefault) {
                $newDefault->setAsDefault();
            }
        }
        
        return redirect()->route('site.profile.addresses.index')
                         ->with('success', 'Endereço removido com sucesso!');
    }
    
    /**
     * Define um endereço como padrão.
     */
    public function setDefault(string $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->setAsDefault();
        
        return redirect()->route('site.profile.addresses.index')
                         ->with('success', 'Endereço definido como padrão!');
    }
    
    /**
     * Consulta o CEP e retorna os dados do endereço.
     */
    public function lookupZipcode(Request $request)
    {
        $zipcode = preg_replace('/[^0-9]/', '', $request->zipcode);
        
        if (strlen($zipcode) !== 8) {
            return response()->json(['error' => 'CEP inválido'], 422);
        }
        
        // Consulta o serviço ViaCEP
        $url = "https://viacep.com.br/ws/{$zipcode}/json/";
        $response = file_get_contents($url);
        $data = json_decode($response);
        
        if (isset($data->erro) && $data->erro) {
            return response()->json(['error' => 'CEP não encontrado'], 404);
        }
        
        return response()->json([
            'zipcode' => $zipcode,
            'state' => $data->uf,
            'city' => $data->localidade,
            'district' => $data->bairro,
            'street' => $data->logradouro
        ]);
    }
    
    /**
     * Armazena um novo endereço via modal (AJAX).
     */
    public function storeModal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'zipcode' => 'required|string|size:9',
            'state' => 'required|string|size:2',
            'city' => 'required|string|max:100',
            'neighborhood' => 'required|string|max:100',
            'street' => 'required|string|max:200',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'is_default' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar os dados do endereço.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Auth::user();
        $data = $validator->validated();
        $data['user_id'] = $user->id;
        
        // Mapear campos do modal para a estrutura da tabela
        $data['district'] = $data['neighborhood'];
        unset($data['neighborhood']);
        
        // Definir valores padrão para campos obrigatórios
        $data['recipient'] = $user->name;
        $data['type'] = 'residential';
        
        // Se for o primeiro endereço do usuário, define como padrão automaticamente
        if ($user->addresses()->count() === 0) {
            $data['is_default'] = true;
        }
        
        $address = Address::create($data);
        
        // Se marcado como padrão, garante que seja o único padrão
        if ($request->has('is_default') && $request->is_default) {
            $address->setAsDefault();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Endereço adicionado com sucesso!',
            'address' => $address
        ]);
    }
}
