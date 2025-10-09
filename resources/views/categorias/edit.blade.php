{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Edit Categoria</h1>
        <form action="{{ route('categorias.update', $categoria->id) }}" method="POST">
            @csrf
            @method('PUT')
            <livewire:input type="text" name="name" label="Name" id="name" value="{{ old('name', $categoria->name) }}" />
                    
            <div class="mt-4">
                <livewire:botao tipo="primary" label="Atualizar" tipoBotao="submit" />
                <livewire:botao tipo="secondary" label="Voltar" href="{{ route('categorias.index') }}" />
            </div>
        </form>
    </div>
@endsection