<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatStyleShop;
use Illuminate\Support\Str;

class CatStyleShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar seções principais
        $secoes = [
            'DJs' => [
                'descrição' => 'Discos selecionados por DJs profissionais'
            ],
            'Colecionadores' => [
                'descrição' => 'Itens raros para colecionadores'
            ],
            'Lotes' => [
                'descrição' => 'Conjuntos de discos vendidos em lote'
            ],
            'Promoções' => [
                'descrição' => 'Discos com preços promocionais'
            ],
        ];

        foreach ($secoes as $nome => $dados) {
            CatStyleShop::firstOrCreate(
                ['nome' => $nome],
                [
                    'slug' => Str::slug($nome),
                    'parent_id' => null,
                ]
            );
        }

        // Você pode adicionar subcategorias aqui se necessário
    }
}
