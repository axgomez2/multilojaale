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
        // Lista completa de categorias de estilo para a loja
        $categorias = [
            'DESTAQUE',
            'VINYL 4 DJ\'S',
            'COLECIONÁVEIS',
            'POP',
            'ROCK',
            'METAL',
            'PUNK',
            'HIP HOP',
            'R&B',
            'REGGAE',
            'FOLK',
            'MPB',
            'SERTANEJO',
            'MUSICA BRASILEIRA',
            'SAMBA',
            'PAGODE',
            'MUSICA LATINA',
            'WORLD MUSIC',
            'CLASSICA E ERUDITA',
            'HOUSE',
            'TECHNO',
            'DRUM & BASS',
            'EURO DANCE',
            'FLASH HOUSE',
        ];

        // Não podemos truncar a tabela porque há referências em outras tabelas
        // Em vez disso, vamos atualizar os registros existentes e criar novos quando necessário

        // Inserir as categorias
        foreach ($categorias as $categoria) {
            CatStyleShop::firstOrCreate(
                ['nome' => $categoria],
                [
                    'slug' => Str::slug($categoria)
                ]
            );
        }

        // A tabela cat_style_shop possui apenas as colunas: id, nome, slug e timestamps
    }
}
