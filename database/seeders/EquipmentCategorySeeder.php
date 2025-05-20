<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EquipmentCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Turntables',
                'description' => 'Record players for vinyl enthusiasts',
                'parent_id' => null
            ],
            [
                'name' => 'Amplifiers',
                'description' => 'Devices to increase the power of audio signals',
                'parent_id' => null
            ],
            [
                'name' => 'Speakers',
                'description' => 'Devices that convert electrical audio signals into sound',
                'parent_id' => null
            ],
            [
                'name' => 'Headphones',
                'description' => 'Personal audio listening devices',
                'parent_id' => null
            ],
            [
                'name' => 'Phono Preamps',
                'description' => 'Amplifiers specifically designed for turntables',
                'parent_id' => null
            ],
            [
                'name' => 'Cartridges',
                'description' => 'Devices that convert the vibrations from the record groove into an electrical signal',
                'parent_id' => 1 // Assuming Turntables will have ID 1
            ],
            [
                'name' => 'Accessories',
                'description' => 'Various add-ons and tools for vinyl enthusiasts',
                'parent_id' => null
            ],
        ];

        foreach ($categories as $category) {
            DB::table('equipment_categories')->insert([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'parent_id' => $category['parent_id'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
