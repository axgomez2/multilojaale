<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ImageService
{
    /**
     * Diretórios para cada tipo de imagem
     * 
     * @var string
     */
    private const VINYL_COVER_DIRECTORY = 'vinyl_covers';
    private const LABEL_COVER_DIRECTORY = 'label_covers';
    private const ARTIST_COVER_DIRECTORY = 'artist_covers';

    /**
     * Faz upload de uma imagem
     *
     * @param UploadedFile $image Arquivo de imagem enviado
     * @param string $id Identificador para a imagem
     * @param string|null $oldImagePath Caminho da imagem antiga para ser excluída
     * @return string Caminho da imagem salva
     * @throws \Exception
     */
    public function uploadImage(UploadedFile $image, string $id, ?string $oldImagePath = null): string
    {
        try {
            $imageName = $this->generateImageName($id, $image->getClientOriginalExtension());
            Storage::disk('public')->put($imageName, file_get_contents($image));

            // Remover imagem antiga se existir
            if ($oldImagePath) {
                $this->deleteImage($oldImagePath);
            }

            return $imageName;
        } catch (\Exception $e) {
            Log::error('Erro ao fazer upload da imagem: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Salva uma imagem a partir do conteúdo binário
     *
     * @param string $imageContents Conteúdo binário da imagem
     * @param string $id Identificador para a imagem
     * @param string $extension Extensão do arquivo
     * @param string|null $oldImagePath Caminho da imagem antiga para ser excluída
     * @param string $type Tipo de imagem ('vinyl', 'label', 'artist')
     * @return string Caminho da imagem salva
     * @throws \Exception
     */
    public function saveImageFromContents(string $imageContents, string $id, string $extension = 'jpg', ?string $oldImagePath = null, string $type = 'vinyl'): string
    {
        try {
            $imageName = $this->generateImageName($id, $extension, $type);
            Storage::disk('public')->put($imageName, $imageContents);

            // Remover imagem antiga se existir
            if ($oldImagePath) {
                $this->deleteImage($oldImagePath);
            }

            return $imageName;
        } catch (\Exception $e) {
            Log::error('Erro ao salvar imagem: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Exclui uma imagem do storage
     *
     * @param string $imagePath Caminho da imagem a ser excluída
     * @return bool True se a exclusão foi bem-sucedida
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            if (Storage::disk('public')->exists($imagePath)) {
                return Storage::disk('public')->delete($imagePath);
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Erro ao excluir imagem: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gera um nome único para a imagem
     *
     * @param string $id Identificador para a imagem
     * @param string $extension Extensão do arquivo
     * @param string $type Tipo de imagem ('vinyl', 'label', 'artist')
     * @return string Nome do arquivo com caminho
     */
    private function generateImageName(string $id, string $extension, string $type = 'vinyl'): string
    {
        $directory = match($type) {
            'label' => self::LABEL_COVER_DIRECTORY,
            'artist' => self::ARTIST_COVER_DIRECTORY,
            default => self::VINYL_COVER_DIRECTORY
        };
        
        // Verificar se o diretório existe, senão criar
        $fullPath = 'public/' . $directory;
        if (!Storage::exists($fullPath)) {
            Storage::makeDirectory($fullPath);
        }
        
        return $directory . '/' . $id . '_' . Str::random(10) . '.' . $extension;
    }
    
    /**
     * Baixa uma imagem da API do Discogs e salva no storage
     *
     * @param string $imageUrl URL da imagem no Discogs
     * @param string $discogsId ID do release no Discogs
     * @param string|null $oldImagePath Caminho da imagem antiga para ser excluída
     * @return string|null Caminho da imagem salva ou null em caso de erro
     */
    public function downloadDiscogsImage(string $imageUrl, string $discogsId, ?string $oldImagePath = null): ?string
    {
        try {
            // Verificar se a URL está vazia
            if (empty($imageUrl)) {
                return null;
            }
            
            // Fazer o download da imagem usando Http Facade
            $response = Http::timeout(10)->get($imageUrl);
            
            // Verificar se a solicitação foi bem-sucedida
            if (!$response->successful()) {
                Log::error('Erro ao baixar imagem do Discogs: ' . $response->status());
                return null;
            }
            
            // Extrair conteúdo da imagem
            $imageContents = $response->body();
            
            // Extrair extensão da URL ou usar jpg como fallback
            $extension = 'jpg';
            $urlParts = parse_url($imageUrl);
            if (isset($urlParts['path'])) {
                $pathInfo = pathinfo($urlParts['path']);
                if (isset($pathInfo['extension'])) {
                    $extension = strtolower($pathInfo['extension']);
                }
            }
            
            // Salvar imagem e retornar o caminho
            return $this->saveImageFromContents($imageContents, $discogsId, $extension, $oldImagePath);
        } catch (\Exception $e) {
            Log::error('Erro ao processar imagem do Discogs: ' . $e->getMessage());
            return null;
        }
    }
}
