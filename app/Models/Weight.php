<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weight extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'unit'];

    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class);
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
