<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = ['vinyl_master_id', 'name', 'duration', 'youtube_url', 'position'];

    public function vinylMaster()
    {
        return $this->belongsTo(VinylMaster::class);
    }
}
