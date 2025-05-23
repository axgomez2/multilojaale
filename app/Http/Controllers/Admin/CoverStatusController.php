<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoverStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CoverStatusController extends Controller
{
    
    /**
     * Exibe uma lista de status de capa
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $statuses = CoverStatus::orderBy('title')->paginate(15);
        return view('admin.cover-status.index', compact('statuses'));
    }
    
    /**
     * Exibe o formulário para criar um novo status de capa
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.cover-status.create');
    }
    
    /**
     * Armazena um novo status de capa
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:50|unique:cover_status',
            'description' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Houve um erro ao cadastrar o ESTADO de capa')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            CoverStatus::create([
                'title' => $request->title,
                'description' => $request->description
            ]);
            
            return redirect()->route('admin.cover-status.index')
                ->with('success', 'ESTADO de capa cadastrado com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao cadastrar o ESTADO de capa: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibe os detalhes de um status de capa específico
     *
     * @param  \App\Models\CoverStatus  $coverStatus
     * @return \Illuminate\View\View
     */
    public function show(CoverStatus $coverStatus)
    {
        $vinylCount = $coverStatus->vinylSecs()->count();
        return view('admin.cover-status.show', compact('coverStatus', 'vinylCount'));
    }
    
    /**
     * Exibe o formulário para editar um status de capa
     *
     * @param  \App\Models\CoverStatus  $coverStatus
     * @return \Illuminate\View\View
     */
    public function edit(CoverStatus $coverStatus)
    {
        return view('admin.cover-status.edit', compact('coverStatus'));
    }
    
    /**
     * Atualiza um status de capa específico
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CoverStatus  $coverStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CoverStatus $coverStatus)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:50|unique:cover_status,title,' . $coverStatus->id,
            'description' => 'nullable|string|max:255',
            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Houve um erro ao atualizar o status de capa')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $coverStatus->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);
            
            return redirect()->route('admin.cover-status.index')
                ->with('success', 'ESTADO de capa atualizado com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar o ESTADO de capa: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Remove um status de capa específico
     *
     * @param  \App\Models\CoverStatus  $coverStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CoverStatus $coverStatus)
    {
        // Verificar se existem vinis usando este status
        $vinylCount = $coverStatus->vinylSecs()->count();
        
        if ($vinylCount > 0) {
            return redirect()->back()
                ->with('error', "Não é possível excluir este status pois está sendo utilizado por {$vinylCount} discos.");
        }
        
        try {
            $coverStatus->delete();
            return redirect()->route('admin.cover-status.index')
                ->with('success', 'ESTADO de capa removido com sucesso');
        } catch (\Exception $e) {
            return redirect()->route('admin.cover-status.index')
                ->with('error', 'Erro ao remover o ESTADO de capa: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.cover-status.index');
    }
}
