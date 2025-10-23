<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::create(['name' => 'Laravel', 'color' => '#f55247']);
        Tag::create(['name' => 'PHP', 'color' => '#777bb3']);
        Tag::create(['name' => 'Livewire', 'color' => '#4e56a6']);
        Tag::create(['name' => 'Componentes', 'color' => '#4a5568']);
        Tag::create(['name' => 'Frontend', 'color' => '#3490dc']);
    }
}