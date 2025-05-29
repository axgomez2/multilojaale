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

    /**
     * Obtém todas as categorias
     */
    public static function getAllCategories()
    {
        return self::select('id', 'nome as name', 'slug')
            ->orderBy('nome')
            ->get();
    }

    /**
     * Relacionamento com VinylMaster (produtos)
     */
    public function vinylMasters()
    {
        return $this->belongsToMany(VinylMaster::class, 'cat_style_shop_vinyl_master', 'cat_style_shop_id', 'vinyl_master_id');
    }
}
