<?php

namespace App\ViewModels;

use App\Models\VinylMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VinylCardViewModel
{
    /**
     * O objeto VinylMaster
     * 
     * @var VinylMaster
     */
    protected $vinyl;

    /**
     * Construtor
     * 
     * @param VinylMaster $vinyl
     */
    public function __construct(VinylMaster $vinyl)
    {
        $this->vinyl = $vinyl;
    }

    /**
     * Verifica se o vinil está disponível para compra
     * 
     * @return bool
     */
    public function isAvailable(): bool
    {
        return isset($this->vinyl->vinylSec) && 
               $this->vinyl->vinylSec->in_stock && 
               $this->vinyl->vinylSec->stock > 0 && 
               $this->vinyl->vinylSec->price > 0;
    }

    /**
     * Retorna o texto de disponibilidade para exibição
     * 
     * @return string
     */
    public function availabilityText(): string
    {
        return $this->isAvailable() ? 'Disponível' : 'Indisponível';
    }

    /**
     * Retorna a classe CSS para o badge de disponibilidade
     * 
     * @return string
     */
    public function availabilityClass(): string
    {
        return $this->isAvailable() ? 'bg-green-500 text-white' : 'bg-red-500 text-white';
    }

    /**
     * Retorna o preço formatado
     * 
     * @return string|null
     */
    public function formattedPrice(): ?string
    {
        if (!isset($this->vinyl->vinylSec) || !$this->vinyl->vinylSec->price) {
            return null;
        }
        
        return 'R$ ' . number_format($this->vinyl->vinylSec->price, 2, ',', '.');
    }

    /**
     * Retorna o nome do artista principal
     * 
     * @return string
     */
    public function artistName(): string
    {
        return $this->vinyl->artists->count() > 0 
            ? $this->vinyl->artists->first()->name 
            : 'artista';
    }

    /**
     * Retorna todos os nomes de artistas em uma string
     * 
     * @return string
     */
    public function artistsString(): string
    {
        return $this->vinyl->artists->pluck('name')->join(', ');
    }

    /**
     * Retorna o slug do artista principal
     * 
     * @return string
     */
    public function artistSlug(): string
    {
        // Verificar se há artistas associados
        if ($this->vinyl->artists->count() > 0) {
            $artist = $this->vinyl->artists->first();
            
            // Usa o slug real do artista se disponível
            if (isset($artist->slug) && !empty($artist->slug)) {
                return $artist->slug;
            }
        }
        
        // Fallback para geração de slug do nome do artista
        return Str::slug($this->artistName());
    }

    /**
     * Retorna o título do vinil
     * 
     * @return string
     */
    public function title(): string
    {
        return $this->vinyl->title ?? 'Disco de Vinil';
    }

    /**
     * Retorna o slug do título
     * 
     * @return string
     */
    public function titleSlug(): string
    {
        // Usa o slug real do banco de dados em vez de gerar um novo
        if (isset($this->vinyl->slug) && !empty($this->vinyl->slug)) {
            return $this->vinyl->slug;
        }
        
        // Fallback para geração de slug (apenas por segurança)
        return Str::slug($this->title());
    }

    /**
     * Retorna o caminho da imagem de capa
     * 
     * @return string|null
     */
    public function coverImagePath(): ?string
    {
        return $this->vinyl->cover_image ?? null;
    }

    /**
     * Verifica se o vinil está na wishlist do usuário
     * 
     * @return bool
     */
    public function inWishlist(): bool
    {
        return $this->vinyl->inWishlist();
    }

    /**
     * Verifica se o vinil está na wantlist do usuário
     * 
     * @return bool
     */
    public function inWantlist(): bool
    {
        return $this->vinyl->inWantlist();
    }

    /**
     * Retorna o ID do vinil
     * 
     * @return int
     */
    public function id(): int
    {
        return $this->vinyl->id;
    }

    /**
     * Retorna as classes CSS para o container com base no tamanho
     * 
     * @param string $size small|normal|large
     * @return string
     */
    public function containerClasses(string $size): string
    {
        return [
            'small' => 'max-w-xs',
            'normal' => 'max-w-sm',
            'large' => 'max-w-md'
        ][$size] ?? 'max-w-sm';
    }

    /**
     * Retorna as classes CSS para a altura da imagem com base no tamanho
     * 
     * @param string $size small|normal|large
     * @return string
     */
    public function imageHeightClass(string $size): string
    {
        return [
            'small' => 'h-48',
            'normal' => 'h-64',
            'large' => 'h-80'
        ][$size] ?? 'h-64';
    }

    /**
     * Retorna as classes CSS para o tamanho do texto do título
     * 
     * @param string $size small|normal|large
     * @return string
     */
    public function titleTextClass(string $size): string
    {
        return [
            'small' => 'text-xs',
            'normal' => 'text-sm',
            'large' => 'text-base'
        ][$size] ?? 'text-sm';
    }

    /**
     * Retorna as classes CSS para o tamanho do texto do preço
     * 
     * @param string $size small|normal|large
     * @return string
     */
    public function priceTextClass(string $size): string
    {
        return [
            'small' => 'text-sm',
            'normal' => 'text-base',
            'large' => 'text-lg'
        ][$size] ?? 'text-base';
    }
}
