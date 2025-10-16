{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Produtos</h1>
            <livewire:botao tipo="primary" label="Novo" href="{{ route('produtos.create') }}" />
        </div>

        <livewire:table
            :collection="$collection"
            :busca="true"
            titulo="Produtos"
            :colunas="[
                'name' => 'Nome do Produto',
                'price' => 'Preço (R$)',
            ]"
            actionsTitle="Opções"
            :gerar-acoes="true"
        />


    </div>
@endsection
