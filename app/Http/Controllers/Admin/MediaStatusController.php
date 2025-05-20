<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MediaStatusController extends Controller
{
    
    /**
     * Exibe uma lista de status de mídia
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $statuses = MediaStatus::orderBy('name')->paginate(15);
        return view('admin.media-status.index', compact('statuses'));
    }
    
    /**
     * Exibe o formulário para criar um novo status de mídia
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.media-status.create');
    }
    
    /**
     * Armazena um novo status de mídia
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:media_statuses',
            'description' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Houve um erro ao cadastrar o status de mídia')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            MediaStatus::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'color_code' => $request->color_code
            ]);
            
            return redirect()->route('admin.media-status.index')
                ->with('success', 'Status de mídia cadastrado com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao cadastrar o status de mídia: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibe os detalhes de um status de mídia específico
     *
     * @param  \App\Models\MediaStatus  $mediaStatus
     * @return \Illuminate\View\View
     */
    public function show(MediaStatus $mediaStatus)
    {
        $vinylCount = $mediaStatus->vinylSecs()->count();
        return view('admin.media-status.show', compact('mediaStatus', 'vinylCount'));
    }
    
    /**
     * Exibe o formulário para editar um status de mídia
     *
     * @param  \App\Models\MediaStatus  $mediaStatus
     * @return \Illuminate\View\View
     */
    public function edit(MediaStatus $mediaStatus)
    {
        return view('admin.media-status.edit', compact('mediaStatus'));
    }
    
    /**
     * Atualiza um status de mídia específico
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MediaStatus  $mediaStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, MediaStatus $mediaStatus)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:media_statuses,name,' . $mediaStatus->id,
            'description' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Houve um erro ao atualizar o status de mídia')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $mediaStatus->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'color_code' => $request->color_code
            ]);
            
            return redirect()->route('admin.media-status.index')
                ->with('success', 'Status de mídia atualizado com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar o status de mídia: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Remove um status de mídia específico
     *
     * @param  \App\Models\MediaStatus  $mediaStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MediaStatus $mediaStatus)
    {
        // Verificar se existem vinis usando este status
        $vinylCount = $mediaStatus->vinylSecs()->count();
        
        if ($vinylCount > 0) {
            return redirect()->back()
                ->with('error', "Não é possível excluir este status pois está sendo utilizado por {$vinylCount} discos.");
        }
        
        try {
            $mediaStatus->delete();
            return redirect()->route('admin.media-status.index')
                ->with('success', 'Status de mídia removido com sucesso');
        } catch (\Exception $e) {
            return redirect()->route('admin.media-status.index')
                ->with('error', 'Erro ao remover o status de mídia: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.media-status.index');
    }
}
