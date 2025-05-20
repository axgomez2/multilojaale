<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EquipmentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'parent_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    public function parent()
    {
        return $this->belongsTo(EquipmentCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(EquipmentCategory::class, 'parent_id');
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
