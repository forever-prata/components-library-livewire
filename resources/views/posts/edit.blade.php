{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Edit Post</h1>
        <form action="{{ route('posts.update', $post->id) }}" method="POST">
            @csrf
            @method('PUT')
            @livewire('select', [
                'name' => 'tags',
                'label' => 'Tags',
                'id' => 'tags',
                'options' => $tags->pluck('name', 'id')->toArray(),
                'multiple' => true,
                'placeholder' => 'Selecione as Tags',
                'selected' => old('tags', $post->tags->pluck('id')->toArray())
            ])

            <livewire:input type="text" name="title" label="Title" id="title" value="{{ old('title', $post->title) }}" />
            <livewire:input type="textarea" name="content" label="Content" id="content" value="{{ old('content', $post->content) }}" />
            <livewire:checkbox name="published" label="Published" id="published" :checked="old('published', $post->published)" />
            <div class="mt-4">
                <livewire:botao tipo="primary" label="Atualizar" tipoBotao="submit" />
                <livewire:botao tipo="secondary" label="Voltar" href="{{ route('posts.index') }}" />
            </div>
        </form>
    </div>
@endsection