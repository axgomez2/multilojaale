<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    
    /**
     * Exibe uma lista de fornecedores
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('document', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $suppliers = $query->orderBy('name')
                     ->paginate(15)
                     ->withQueryString();
                     
        return view('admin.suppliers.index', compact('suppliers'));
    }
    
    /**
     * Exibe o formulário para criar um novo fornecedor
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }
    
    /**
     * Armazena um novo fornecedor
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'document_type' => 'nullable|string|in:cpf,cnpj',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zipcode' => 'nullable|string|max:10',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Houve um erro ao cadastrar o fornecedor')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            Supplier::create($request->all());
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Fornecedor cadastrado com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao cadastrar o fornecedor: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibe um fornecedor específico
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\View\View
     */
    public function show(Supplier $supplier)
    {
        $recentProducts = $supplier->vinylSecs()
                          ->with('vinylMaster')
                          ->orderBy('created_at', 'desc')
                          ->take(5)
                          ->get();
                          
        return view('admin.suppliers.show', compact('supplier', 'recentProducts'));
    }
    
    /**
     * Exibe o formulário para editar um fornecedor
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\View\View
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }
    
    /**
     * Atualiza um fornecedor
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'document_type' => 'nullable|string|in:cpf,cnpj',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zipcode' => 'nullable|string|max:10',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Houve um erro ao atualizar o fornecedor')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $supplier->update($request->all());
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Fornecedor atualizado com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar o fornecedor: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Remove um fornecedor
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Fornecedor removido com sucesso');
        } catch (\Exception $e) {
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Erro ao remover o fornecedor: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.suppliers.index');
    }
}
