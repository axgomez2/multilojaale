<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatStyleShop extends Model
{
    use HasFactory;

    protected $table = 'cat_style_shop';
    protected $fillable = ['nome', 'slug'];

    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class);
    }

    // Método simplificado para obter todas as categorias
    public static function getMainCategories()
    {
        return self::select('id', 'nome as name', 'slug')
            ->orderBy('nome')
            ->get();
    }

    // Relacionamento com categorias pai
    public function parent()
    {
        return $this->belongsTo(CatStyleShop::class, 'parent_id');
    }

    // Relacionamento com categorias filhas
    public function children()
    {
        return $this->hasMany(CatStyleShop::class, 'parent_id');
    }

    // Relacionamento com produtos (vinyls)
    public function vinyls()
    {
        return $this->belongsToMany(Vinyl::class, 'vinyl_category', 'category_id', 'vinyl_id');
    }

    // Método para obter o caminho completo da categoria
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

    // Método para verificar se a categoria tem subcategorias
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    // Método para obter todas as subcategorias (incluindo subcategorias de subcategorias)
    public function getAllChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }
}
