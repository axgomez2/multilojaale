<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VinylMaster;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VinylImageController extends Controller
{
    public function index($id)
    {
        $vinylMaster = VinylMaster::findOrFail($id);
        $images = $vinylMaster->media;

        return view('admin.vinyls.images', compact('vinylMaster', 'images'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'images' => 'required|array|max:10', // Limita a 10 imagens
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $vinylMaster = VinylMaster::findOrFail($id);

        if ($request->hasFile('images')) {
            $manager = new ImageManager(new Driver());

            DB::beginTransaction();

            try {
                foreach ($request->file('images') as $image) {
                    $img = $manager->read($image);

                    // Obter as dimensões atuais
                    $width = $img->width();
                    $height = $img->height();

                    // Determinar o tamanho para cortar (menor dimensão)
                    $size = min($width, $height);

                    // Calcular a posição de corte (centro)
                    $x = ($width - $size) / 2;
                    $y = ($height - $size) / 2;

                    // Cortar a imagem
                    $img->crop($size, $size, $x, $y);

                    // Redimensionar para 400x400
                    $img->resize(400, 400);

                    // Gerar um nome de arquivo único
                    $filename = uniqid('vinyl_') . '.jpg';
                    $path = 'vinyl_images/' . $filename;

                    // Salvar a imagem
                    $savedImage = Storage::disk('public')->put($path, $img->toJpeg(80));

                    if (!$savedImage) {
                        throw new \Exception('Falha ao salvar a imagem: ' . $filename);
                    }

                    $media = new Media([
                        'file_path' => $path,
                        'file_name' => $filename,
                        'file_size' => Storage::disk('public')->size($path),
                        'file_type' => 'image/jpeg',
                    ]);
                    $vinylMaster->media()->save($media);
                }

                DB::commit();
                return redirect()->route('admin.vinyl.images', $id)->with('success', 'Imagens enviadas e cortadas com sucesso.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erro ao fazer upload das imagens: ' . $e->getMessage());
                return redirect()->route('admin.vinyl.images', $id)->with('error', 'Ocorreu um erro ao fazer upload das imagens. Por favor, tente novamente.');
            }
        }

        return redirect()->route('admin.vinyl.images', $id)->with('error', 'Nenhuma imagem foi enviada.');
    }
    public function destroy($id, $imageId)
{
    $media = Media::findOrFail($imageId);

    // Verifica se o media pertence ao VinylMaster
    if ($media->mediable_id == $id && $media->mediable_type == VinylMaster::class) {
        // Se o arquivo existir, tenta deletá-lo
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        } else {
            Log::warning("Arquivo não encontrado: " . $media->file_path);
        }

        $media->delete();
        return redirect()->route('admin.vinyl.images', $id)
                         ->with('success', 'Imagem excluída com sucesso.');
    }

    return redirect()->route('admin.vinyl.images', $id)
                     ->with('error', 'Não foi possível excluir a imagem.');
}

}
