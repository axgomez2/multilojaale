<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Comentando a criação do usuário de teste padrão
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        // Executa todos os seeders do sistema
        $this->call([
            DeveloperUserSeeder::class,
            BrandSeeder::class,
            DimensionSeeder::class,
            EquipmentCategorySeeder::class,
            ProductTypeSeeder::class,
            WeightSeeder::class,
            CatStyleShopSimpleSeeder::class,
        ]);
    }
}
