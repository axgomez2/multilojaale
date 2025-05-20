<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductTypeSeeder extends Seeder
{
    public function run()
    {
        $types = ['Vinyl', 'Equipment'];

        foreach ($types as $type) {
            $count = DB::table('product_types')->where('name', $type)->count();
            
            if ($count == 0) {
                DB::table('product_types')->insert([
                    'name' => $type,
                    'slug' => Str::slug($type),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
