<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'logo_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            $brand->slug = Str::slug($brand->name);
        });
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
