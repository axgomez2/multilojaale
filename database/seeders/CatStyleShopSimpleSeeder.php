<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatStyleShop;
use Illuminate\Support\Str;

class CatStyleShopSimpleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar categorias para as diferentes seções
        $categorias = [
            // Seção para DJs
            'DJ House' => 'Vinis de House para DJs',
            'DJ Techno' => 'Vinis de Techno para DJs',
            'DJ Hip Hop' => 'Vinis de Hip Hop para DJs',
            
            // Seção para Colecionadores
            'Raridades' => 'Vinis raros para colecionadores',
            'Edições Limitadas' => 'Edições limitadas e especiais',
            'Clássicos' => 'Clássicos imperdíveis para coleção',
            
            // Seção para Lotes
            'Lote Iniciante' => 'Pacotes para DJs iniciantes',
            'Lote Premium' => 'Pacotes de vinis premium',
            'Lote Mix' => 'Pacotes com mix de estilos variados',
        ];

        foreach ($categorias as $nome => $descricao) {
            CatStyleShop::firstOrCreate(
                ['nome' => $nome],
                [
                    'slug' => Str::slug($nome),
                ]
            );
        }
    }
}
