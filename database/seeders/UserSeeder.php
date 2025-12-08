<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'marcos.dev@mvlco.com.br'],
            [
                'name' => 'Marcos Dev',
                'password' => Hash::make('senha123'),
            ]
        );
    }
}
