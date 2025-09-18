{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Produtos</h1>
            <livewire:botao tipo="primary" label="Create New" href="{{ route('produtos.create') }}" />
        </div>

        <livewire:table
            :collection="$collection"
            :busca="true"
            :selecionavel="false"
            titulo="Produtos"
        />
    </div>
@endsection