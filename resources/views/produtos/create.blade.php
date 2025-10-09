{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
<h1 class="mb-4">Adicionar Novo Produto</h1>
        <form action="{{ route('produtos.store') }}" method="POST">
            @csrf
                        @livewire('select', [
                'name' => 'categoria_id',
                'label' => 'Categoria',
                'id' => 'categoria_id',
                'options' => $categorias->pluck('name', 'id')->toArray(),
                'placeholder' => 'Selecione uma Categoria'
            ])
<livewire:input type="text" name="name" label="Name" id="name" value="{{ old('name') }}" />
                <livewire:input type="textarea" name="description" label="Description" id="description" value="{{ old('description') }}" />
                <livewire:input type="number" name="price" label="Price" id="price" value="{{ old('price') }}" />
                <livewire:checkbox name="in_stock" label="In stock" id="in_stock" :checked="old('in_stock', true)" />
                
            <div class="mt-4">
                <livewire:botao tipo="primary" label="Salvar" tipoBotao="submit" />
                <livewire:botao tipo="secondary" label="Voltar" href="{{ route('produtos.index') }}" />
            </div>
        </form>
    </div>
@endsection