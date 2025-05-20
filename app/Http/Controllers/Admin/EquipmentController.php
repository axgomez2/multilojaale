<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Brand;
use App\Models\Weight;
use App\Models\Dimension;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with(['category', 'brand', 'media'])->paginate(15);
        return view('admin.equipments.index', compact('equipments'));
    }

    public function create()
    {
        $categories = EquipmentCategory::all();
        $brands = Brand::all();
        $weights = Weight::all();
        $dimensions = Dimension::all();
        return view('admin.equipments.create', compact('categories', 'brands', 'weights', 'dimensions'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateEquipmentData($request);

        try {
            DB::beginTransaction();

            // Formatar especificações se fornecidas
            if (isset($validatedData['specifications'])) {
                $validatedData['specifications'] = $this->formatSpecifications($validatedData['specifications']);
            }

            // Gera o slug a partir do nome
            $validatedData['slug'] = Str::slug($validatedData['name']);

            // Boolean campos
            $validatedData['is_new'] = $request->has('is_new');
            $validatedData['is_promotional'] = $request->has('is_promotional');
            $validatedData['in_stock'] = $request->has('in_stock');

            // Cria o equipamento
            $equipment = Equipment::create($validatedData);

            // Processa as imagens
            if ($request->hasFile('images')) {
                $this->handleImages($request->file('images'), $equipment);
            }

            DB::commit();
            return redirect()->route('admin.equipments.index')->with('success', 'Equipamento criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar equipamento: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro ao criar o equipamento: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $equipment = Equipment::with('media')->findOrFail($id);
        $categories = EquipmentCategory::all();
        $brands = Brand::all();
        $weights = Weight::all();
        $dimensions = Dimension::all();

        return view('admin.equipments.edit', compact('equipment', 'categories', 'brands', 'weights', 'dimensions'));
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $validatedData = $this->validateEquipmentData($request);

        try {
            DB::beginTransaction();

            // Formatar especificações se fornecidas
            if (isset($validatedData['specifications'])) {
                $validatedData['specifications'] = $this->formatSpecifications($validatedData['specifications']);
            }

            // Gera o slug a partir do nome (se o nome mudar)
            if ($equipment->name != $validatedData['name']) {
                $validatedData['slug'] = Str::slug($validatedData['name']);
            }

            // Boolean campos
            $validatedData['is_new'] = $request->has('is_new');
            $validatedData['is_promotional'] = $request->has('is_promotional');
            $validatedData['in_stock'] = $request->has('in_stock');

            // Atualiza o equipamento
            $equipment->update($validatedData);

            // Processa as imagens
            if ($request->hasFile('images')) {
                $this->handleImages($request->file('images'), $equipment);
            }

            DB::commit();
            return redirect()->route('admin.equipments.index')->with('success', 'Equipamento atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar equipamento: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro ao atualizar o equipamento: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);

        try {
            DB::beginTransaction();

            // Excluir mídia associada (opcional - depende da configuração SoftDeletes)
            foreach ($equipment->media as $media) {
                Storage::delete('public/' . $media->file_path);
                $media->delete();
            }

            $equipment->delete();

            DB::commit();
            return redirect()->route('admin.equipments.index')->with('success', 'Equipamento excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir equipamento: ' . $e->getMessage());
            return redirect()->route('admin.equipments.index')->with('error', 'Ocorreu um erro ao excluir o equipamento.');
        }
    }

    public function deleteMedia($mediaId)
    {
        try {
            $media = Media::findOrFail($mediaId);

            // Verifica se a mídia pertence a um equipamento
            if ($media->mediable_type === Equipment::class) {
                // Remove o arquivo físico
                Storage::delete('public/' . $media->file_path);

                // Remove o registro do banco de dados
                $media->delete();

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Mídia não encontrada ou não associada a um equipamento.']);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir mídia: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao excluir mídia.']);
        }
    }

    private function validateEquipmentData(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'weight_id' => 'nullable|exists:weights,id',
            'dimension_id' => 'nullable|exists:dimensions,id',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'buy_price' => 'nullable|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|max:255|unique:equipments,sku,' . ($request->route('id') ?? ''),
            'images.*' => 'nullable|image|max:10240', // Max 10MB por imagem
        ]);
    }

    private function formatSpecifications($specificationsText)
    {
        // Converte texto em formato de linhas "chave: valor" para array
        $specs = [];
        $lines = explode("\n", $specificationsText);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                if (!empty($key)) {
                    $specs[$key] = $value;
                }
            }
        }

        return $specs;
    }

    private function handleImages($images, $equipment)
    {
        foreach ($images as $image) {
            // Gera um nome único para o arquivo
            $fileName = uniqid() . '_' . $image->getClientOriginalName();

            // Armazena o arquivo no storage
            $path = $image->storeAs('equipments', $fileName, 'public');

            // Cria registro na tabela de mídia
            $media = new Media([
                'file_path' => $path,
                'file_name' => $fileName,
                'file_type' => $image->getClientMimeType(),
                'file_size' => $image->getSize(),
                'alt_text' => $equipment->name,
            ]);

            // Associa a mídia ao equipamento
            $equipment->media()->save($media);
        }
    }

    /**
     * Generates an AI description for equipment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDescription(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'brand' => 'nullable|string',
                'category' => 'nullable|string',
            ]);

            $name = $request->input('name');
            $brand = $request->input('brand');
            $category = $request->input('category');

            // Construir um prompt básico com as informações fornecidas
            $brandText = $brand ? " da marca {$brand}" : "";
            $categoryText = $category ? " da categoria {$category}" : "";

            // Aqui você poderia integrar com uma API de IA como OpenAI
            // Por enquanto, retornamos um texto gerado de forma simples
            $description = "O {$name}{$brandText}{$categoryText} é um equipamento profissional de alta qualidade, " .
                "projetado para oferecer desempenho excepcional e durabilidade superior. " .
                "Fabricado com materiais premium e tecnologia de ponta, este equipamento atende às necessidades " .
                "dos profissionais mais exigentes do mercado. " .
                "Cada detalhe foi cuidadosamente pensado para garantir resultados consistentes e confiáveis, " .
                "tornando-o uma escolha ideal para estúdios, produtores e DJs que buscam excelência em seus projetos.";

            return response()->json([
                'success' => true,
                'description' => $description
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar descrição: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar descrição: ' . $e->getMessage()
            ], 500);
        }
    }
}
