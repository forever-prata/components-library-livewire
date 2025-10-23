<?php

// gerado automaticamente pela biblioteca

namespace App\Http\Controllers;

use App\Models\Tag;use App\Models\Post;

use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $collection = Tag::with(['posts'])->get();
        return view('tags.index', compact('collection'));
    }

    public function create()
    {
        $posts = Post::all();
        return view('tags.create', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'color' => 'required',
        ]);


        $tag = Tag::create($request->all());
                $tag->posts()->sync($request->input('posts', []));


        return redirect()->route('tags.index')
            ->with('success', 'Tag criado com sucesso.');
    }

    public function show(Tag $tag)
    {
        $tag->load(['posts']);
        return view('tags.show', compact('tag'));
    }

    public function edit(Tag $tag)
    {
        $tag->load(['posts']);
        $posts = Post::all();
        return view('tags.edit', compact('tag', 'posts'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required',
            'color' => 'required',
        ]);


        $tag->update($request->all());
                $tag->posts()->sync($request->input('posts', []));


        return redirect()->route('tags.index')
            ->with('success', 'Tag atualizado com sucesso.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', 'Tag excluido com sucesso.');
    }
}