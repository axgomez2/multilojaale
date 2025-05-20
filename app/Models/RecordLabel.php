<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RecordLabel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'website', 'logo'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recordLabel) {
            $recordLabel->slug = Str::slug($recordLabel->name);
        });
    }

    public function vinylMasters()
    {
        return $this->hasMany(VinylMaster::class);
    }
}
