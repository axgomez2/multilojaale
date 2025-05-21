<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MidiaStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MidiaStatusController extends Controller
{
    
    /**
     * Exibe uma lista de status de mídia
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $statuses = MidiaStatus::orderBy('title')->paginate(15);
        return view('admin.midia-status.index', compact('statuses'));
    }
    
    /**
     * Exibe o formulário para criar um novo status de mídia
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.midia-status.create');
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
            'title' => 'required|string|max:50|unique:midia_status',
            'description' => 'nullable|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Houve um erro ao cadastrar o status de mídia')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Salvando os dados para depuração
            $dados = [
                'title' => $request->title,
                'description' => $request->description
            ];
            
            // Registrando a tentativa de criação
            \Log::info('Tentando criar status de mídia com os dados:', $dados);
            
            $status = MidiaStatus::create($dados);
            
            // Verificando se o status foi criado
            if ($status) {
                \Log::info('Status criado com sucesso. ID: ' . $status->id);
                return redirect()->route('admin.midia-status.index')
                    ->with('success', 'Status de mídia cadastrado com sucesso');
            } else {
                \Log::error('Falha ao criar status. Nenhum erro lançado, mas status não foi criado.');
                return redirect()->back()
                    ->with('error', 'Falha ao criar status de mídia. Tente novamente.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao cadastrar o status de mídia: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibe os detalhes de um status de mídia específico
     *
     * @param  \App\Models\MidiaStatus  $mediaStatus
     * @return \Illuminate\View\View
     */
    public function show(MidiaStatus $midiaStatus)
    {
        $vinylCount = $midiaStatus->vinylSecs()->count();
        return view('admin.midia-status.show', compact('midiaStatus', 'vinylCount'));
    }
    
    /**
     * Exibe o formulário para editar um status de mídia
     *
     * @param  \App\Models\MidiaStatus  $mediaStatus
     * @return \Illuminate\View\View
     */
    public function edit(MidiaStatus $midiaStatus)
    {
        return view('admin.midia-status.edit', compact('midiaStatus'));
    }
    
    /**
     * Atualiza um status de mídia específico
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MidiaStatus  $mediaStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, MidiaStatus $midiaStatus)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:50|unique:midia_status,title,' . $midiaStatus->id,
            'description' => 'nullable|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Houve um erro ao atualizar o status de mídia')
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $midiaStatus->update([
                'title' => $request->title,
                'description' => $request->description
            ]);
            
            return redirect()->route('admin.midia-status.index')
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
     * @param  \App\Models\MidiaStatus  $mediaStatus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MidiaStatus $midiaStatus)
    {
        // Verificar se existem vinis usando este status
        $vinylCount = $midiaStatus->vinylSecs()->count();
        
        if ($vinylCount > 0) {
            return redirect()->back()
                ->with('error', "Não é possível excluir este status pois está sendo utilizado por {$vinylCount} discos.");
        }
        
        try {
            $midiaStatus->delete();
            return redirect()->route('admin.midia-status.index')
                ->with('success', 'Status de mídia removido com sucesso');
        } catch (\Exception $e) {
            return redirect()->route('admin.midia-status.index')
                ->with('error', 'Erro ao remover o status de mídia: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.midia-status.index');
    }
}
