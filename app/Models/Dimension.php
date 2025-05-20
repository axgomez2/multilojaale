<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimension extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'height', 'width', 'depth', 'unit'];

    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class);
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
