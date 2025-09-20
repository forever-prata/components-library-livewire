{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="card-title mb-0">Detalhes do Produto</h1>
                <livewire:botao tipo="secondary" label="Voltar" href="{{ route('produtos.index') }}" />
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $produto->id }}
                </div>
                <div class="mb-3"><strong>Name:</strong> {{ $produto->name }}</div>
<div class="mb-3"><strong>Description:</strong> {{ $produto->description }}</div>
<div class="mb-3"><strong>Price:</strong> {{ $produto->price }}</div>
<div class="mb-3"><strong>In stock:</strong> {!! $produto->in_stock ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</div>

                <div class="mb-3">
                    <strong>Criado em:</strong> {{ $produto->created_at }}
                </div>
                <div class="mb-3">
                    <strong>Atualizado em:</strong> {{ $produto->updated_at }}
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <livewire:botao tipo="secondary" tamanho="small" label="Edit" href="{{ route('produtos.edit', $produto->id) }}" />
                <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este item?');">
                    @csrf
                    @method('DELETE')
                    <livewire:botao tipo="danger" tamanho="small" label="Delete" tipoBotao="submit" />
                </form>
            </div>
        </div>
    </div>
@endsection