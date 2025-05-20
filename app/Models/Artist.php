<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Artist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'discogs_id',
        'profile',
        'images',
        'discogs_url'
    ];

    protected $casts = [
        'images' => 'array',
        'discogs_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artist) {
            if (empty($artist->slug)) {
                $artist->slug = Str::slug($artist->name);
            }
        });
    }

    public function vinylMasters()
    {
        return $this->belongsToMany(VinylMaster::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function getRouteKeyName()
{
    return 'slug';
}
}
