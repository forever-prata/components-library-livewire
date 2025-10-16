<?php

// gerado automaticamente pela biblioteca

namespace App\Http\Controllers;

use App\Models\Post;use App\Models\Tag;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $collection = Post::with(['tags'])->get();
        return view('posts.index', compact('collection'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('posts.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $data = $request->all();
        $data['published'] = $request->has('published');

        $post = Post::create($data);
                $post->tags()->sync($request->input('tags', []));


        return redirect()->route('posts.index')
            ->with('success', 'Post criado com sucesso.');
    }

    public function show(Post $post)
    {
        $post->load(['tags']);
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $post->load(['tags']);
        $tags = Tag::all();
        return view('posts.edit', compact('post', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $data = $request->all();
        $data['published'] = $request->has('published');

        $post->update($data);
                $post->tags()->sync($request->input('tags', []));


        return redirect()->route('posts.index')
            ->with('success', 'Post atualizado com sucesso.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post excluido com sucesso.');
    }
}
