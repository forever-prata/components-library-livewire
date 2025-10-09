<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::create(['name' => 'Eletrônicos']);
        Categoria::create(['name' => 'Roupas']);
        Categoria::create(['name' => 'Livros']);
        Categoria::create(['name' => 'Alimentos']);
        Categoria::create(['name' => 'Móveis']);
    }
}
