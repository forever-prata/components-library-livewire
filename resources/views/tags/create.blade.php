{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Adicionar Novo Tag</h1>
        <form action="{{ route('tags.store') }}" method="POST">
            @csrf
            @livewire('select', [
                'name' => 'posts',
                'label' => 'Posts',
                'id' => 'posts',
                'options' => $posts->pluck('name', 'id')->toArray(),
                'multiple' => true,
                'placeholder' => 'Selecione as Posts'
            ])

            <livewire:input type="text" name="name" label="Name" id="name" value="{{ old('name') }}" />
            <livewire:input type="text" name="color" label="Color" id="color" value="{{ old('color') }}" />
            <div class="mt-4">
                <livewire:botao tipo="primary" label="Salvar" tipoBotao="submit" />
                <livewire:botao tipo="secondary" label="Voltar" href="{{ route('tags.index') }}" />
            </div>
        </form>
    </div>
@endsection