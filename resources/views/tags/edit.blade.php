{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Edit Tag</h1>
        <form action="{{ route('tags.update', $tag->id) }}" method="POST">
            @csrf
            @method('PUT')
            @livewire('select', [
                'name' => 'posts',
                'label' => 'Posts',
                'id' => 'posts',
                'options' => $posts->pluck('name', 'id')->toArray(),
                'multiple' => true,
                'placeholder' => 'Selecione as Posts',
                'selected' => old('posts', $tag->posts->pluck('id')->toArray())
            ])

            <livewire:input type="text" name="name" label="Name" id="name" value="{{ old('name', $tag->name) }}" />
            <livewire:input type="text" name="color" label="Color" id="color" value="{{ old('color', $tag->color) }}" />
            <div class="mt-4">
                <livewire:botao tipo="primary" label="Atualizar" tipoBotao="submit" />
                <livewire:botao tipo="secondary" label="Voltar" href="{{ route('tags.index') }}" />
            </div>
        </form>
    </div>
@endsection