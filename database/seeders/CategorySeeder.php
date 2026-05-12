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

        // Categoria nível 1: Filtro (raiz)
        $filtro = Category::create([
            'user_id' => $user->id,
            'store_id' => $storeId,
            'title' => 'Linha X',
            'section' => CategorySection::FILTRO->value,
            'id_slug' => 'linha-x',
            'description' => 'Linha x do filtro',
            'icon' => 'http://apps.mvlco.com.br/oticafamilia/wp-content/uploads/2025/11/icon_tipo.png',
            'next' => CategorySection::PRODUTO->value,
            'product' => ['id' => null],
        ]);

        echo "✅ Filtro criado: {$filtro->id_slug}\n";

        // Categoria nível 2: Produto
        $produto = Category::create([
            'user_id' => $user->id,
            'store_id' => $storeId,
            'title' => 'Produto X',
            'section' => CategorySection::PRODUTO->value,
            'id_slug' => 'produto-x',
            'description' => 'Produto X do filtro X',
            'icon' => 'http://apps.mvlco.com.br/oticafamilia/wp-content/uploads/2025/11/icon_produto.png',
            'next' => null,
            'parent_id' => $filtro->id,
            'product' => ['id' => 12345],
        ]);

        echo "✅ Produto criado: {$produto->id_slug}\n";
    }
}
