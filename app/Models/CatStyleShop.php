<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CatStyleShop extends Model
{
    use HasFactory;

    protected $table = 'cat_style_shop';
    protected $fillable = ['nome', 'slug'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $baseSlug = Str::slug($category->nome);
                $uniqueSlug = $baseSlug;
                
                $count = 1;
                while (self::where('slug', $uniqueSlug)->exists()) {
                    $uniqueSlug = $baseSlug . '-' . $count++;
                }
                
                $category->slug = $uniqueSlug;
            }
        });
    }

    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class);
    }

    /**
     * Obtém as seções principais (DJs, Colecionadores, Lotes, etc)
     */
    public static function getSections()
    {
        return self::select('id', 'nome as name', 'slug', 'description', 'icon')
            ->whereNull('parent_id')
            ->where('is_section', true)
            ->orderBy('nome')
            ->get();
    }
    
    /**
     * Obtém categorias dentro de uma seção específica
     */
    public static function getCategoriesBySection($sectionId)
    {
        return self::select('id', 'nome as name', 'slug', 'description')
            ->where('parent_id', $sectionId)
            ->orderBy('nome')
            ->get();
    }
    
    /**
     * Obtém todas as categorias (incluindo seções)
     */
    public static function getAllCategories()
    {
        return self::select('id', 'nome as name', 'slug', 'parent_id', 'is_section')
            ->orderBy('is_section', 'desc')
            ->orderBy('nome')
            ->get();
    }

    /**
     * Relacionamento com categorias pai
     */
    public function parent()
    {
        return $this->belongsTo(CatStyleShop::class, 'parent_id');
    }

    /**
     * Relacionamento com categorias filhas
     */
    public function children()
    {
        return $this->hasMany(CatStyleShop::class, 'parent_id');
    }

    /**
     * Relacionamento com VinylMaster (produtos)
     */
    public function vinylMasters()
    {
        return $this->belongsToMany(VinylMaster::class, 'cat_style_shop_vinyl_master', 'cat_style_shop_id', 'vinyl_master_id');
    }

    /**
     * Método para obter o caminho completo da categoria
     */
    public function getFullPath()
    {
        $path = collect([$this]);
        $parent = $this->parent;

        while ($parent) {
            $path->prepend($parent);
            $parent = $parent->parent;
        }

        return $path;
    }

    /**
     * Método para verificar se a categoria tem subcategorias
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Método para obter todas as subcategorias (incluindo subcategorias de subcategorias)
     */
    public function getAllChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }
    
    /**
     * Obtém todos os produtos (VinylMaster) associados a esta categoria e suas subcategorias
     */
    public function getAllProducts()
    {
        // Primeiro, pega produtos diretamente ligados a esta categoria
        $products = $this->vinylMasters()->with(['vinylSec'])->get();
        
        // Depois, adiciona produtos de todas as subcategorias
        foreach ($this->getAllChildren() as $child) {
            $childProducts = $child->vinylMasters()->with(['vinylSec'])->get();
            $products = $products->merge($childProducts);
        }
        
        return $products->unique('id');
    }
}
