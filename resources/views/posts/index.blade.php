{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Posts</h1>
            <livewire:botao tipo="primary" label="Novo" href="{{ route('posts.create') }}" />
        </div>

        <livewire:table
            :collection="$collection"
            :busca="true"
            :selecionavel="false"
            titulo="Posts"
        />
    </div>
@endsection