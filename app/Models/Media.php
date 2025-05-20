<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['file_path', 'file_name', 'file_type', 'file_size', 'alt_text'];

    public function mediable()
    {
        return $this->morphTo();
    }
}
