<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que existe pelo menos um usuário
        User::updateOrCreate(
            ['email' => 'teste@example.com'],
            [
                'name' => 'Usuario Teste',
                'password' => bcrypt('password'),
            ]
        );

        $tags = Tag::all();

        Post::factory(10)->create()->each(function ($post) use ($tags) {
            $post->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}