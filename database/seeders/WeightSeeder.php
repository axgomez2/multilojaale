<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeightSeeder extends Seeder
{
    public function run()
    {
        $weights = [
            ['name' => 'single', 'value' => 280, 'unit' => 'g'],
            ['name' => 'duplo', 'value' => 560, 'unit' => 'g'],
            ['name' => 'triplo', 'value' => 800, 'unit' => 'g'],
            ['name' => 'Audiophile', 'value' => 180, 'unit' => 'g'],
            ['name' => 'Super Audiophile', 'value' => 200, 'unit' => 'g'],
        ];

        foreach ($weights as $weight) {
            $weight['created_at'] = now();
            $weight['updated_at'] = now();
            DB::table('weights')->insert($weight);
        }
    }
}
