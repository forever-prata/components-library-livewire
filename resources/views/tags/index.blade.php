{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Tags</h1>
            <livewire:botao tipo="primary" label="Novo" href="{{ route('tags.create') }}" />
        </div>

        <livewire:table
            :collection="$collection"
            :busca="true"
            :selecionavel="false"
            titulo="Tags"
        />
    </div>
@endsection