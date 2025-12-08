<?php

namespace Database\Seeders;

use App\Enums\CategorySection;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'marcos.dev@mvlco.com.br')->first();

        if (! $user) {
            echo "❌ Usuário não encontrado!\n";

            return;
        }

        // Categoria nível 1: Tipo de Lente (raiz)
        $tipo = Category::create([
            'user_id' => $user->id,
            'title' => 'Grau',
            'section' => CategorySection::TIPO->value,
            'id_slug' => 'grau',
            'description' => 'Classificação de acordo com o grau de prescrição',
            'icon' => 'http://apps.mvlco.com.br/oticafamilia/wp-content/uploads/2025/11/icon_tipo.png',
            'next' => CategorySection::ESPESSURA->value,
            'product' => ['id' => null],
        ]);

        echo "✅ Tipo criado: {$tipo->id_slug}\n";

        // Categoria nível 2: Espessura
        $espessura = Category::create([
            'user_id' => $user->id,
            'title' => 'Normal',
            'section' => CategorySection::ESPESSURA->value,
            'id_slug' => 'normal-grau',
            'description' => "Miopia\nde 0 a -2 graus\n\nHipermetropia\nde 0 a +2 graus\n\nAstigmatismo\nde 0 a -2 graus\n",
            'icon' => 'http://apps.mvlco.com.br/oticafamilia/wp-content/uploads/2025/11/icon_normal.png',
            'next' => CategorySection::PRODUTO->value,
            'parent_id' => $tipo->id,
            'product' => ['id' => null],
        ]);

        echo "✅ Espessura criada: {$espessura->id_slug}\n";

        // Categoria nível 3: Produto
        $produto = Category::create([
            'user_id' => $user->id,
            'title' => 'Lente Super Resistente',
            'section' => CategorySection::PRODUTO->value,
            'id_slug' => 'lente-super-resistente-normal',
            'description' => 'Lente com tratamento especial',
            'icon' => 'http://apps.mvlco.com.br/oticafamilia/wp-content/uploads/2025/11/icon_produto.png',
            'next' => null,
            'parent_id' => $espessura->id,
            'product' => ['id' => 12345],
        ]);
    }
}
