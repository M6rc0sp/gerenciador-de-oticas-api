<?php

namespace Database\Seeders;

use App\Enums\CategorySection;
use App\Models\Category;
use App\Models\User;
use App\Models\Store;
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

        // Usar primeira store disponível ou criar uma para seed
        $store = Store::first();
        if (! $store) {
            $store = Store::create([
                'nuvemshop_id' => 1,
                'access_token' => 'seed-token',
                'name' => 'Loja Seed',
                'email' => 'seed@loja.com',
                'is_active' => true,
            ]);
        }

        $storeId = $store->nuvemshop_id;

        // Categoria nível 1: Tipo de Lente (raiz)
        $tipo = Category::create([
            'user_id' => $user->id,
            'store_id' => $storeId,
            'title' => 'Grau',
            'section' => CategorySection::TIPO->value,
            'id_slug' => 'grau',
            'description' => 'Classificação de acordo com o grau de prescrição',
            'icon' => 'https://apps.mvlco.com.br/oticafamilia/wp-content/uploads/2025/11/icon_tipo.png',
            'next' => CategorySection::ESPESSURA->value,
            'product' => ['id' => null],
        ]);

        echo "✅ Tipo criado: {$tipo->id_slug}\n";

        // Categoria nível 2: Espessura
        $espessura = Category::create([
            'user_id' => $user->id,
            'store_id' => $storeId,
            'title' => 'Normal',
            'section' => CategorySection::ESPESSURA->value,
            'id_slug' => 'normal-grau',
            'description' => "Miopia\nde 0 a -2 graus\n\nHipermetropia\nde 0 a +2 graus\n\nAstigmatismo\nde 0 a -2 graus\n",
            'icon' => 'https://apps.mvlco.com.br/oticafamilia/wp-content/uploads/2025/11/icon_normal.png',
            'next' => CategorySection::PRODUTO->value,
            'parent_id' => $tipo->id,
            'product' => ['id' => null],
        ]);

        echo "✅ Espessura criada: {$espessura->id_slug}\n";

        // Categoria nível 3: Produto
        $produto = Category::create([
            'user_id' => $user->id,
            'store_id' => $storeId,
            'title' => 'Lente Super Resistente',
            'section' => CategorySection::PRODUTO->value,
            'id_slug' => 'lente-super-resistente-normal',
            'description' => 'Lente com tratamento especial',
            'icon' => 'https://apps.mvlco.com.br/oticafamilia/wp-content/uploads/2025/11/icon_produto.png',
            'next' => null,
            'parent_id' => $espessura->id,
            'product' => ['id' => 12345],
        ]);

        echo "✅ Produto criado: {$produto->id_slug}\n";
    }
}
