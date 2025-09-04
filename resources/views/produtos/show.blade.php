@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <h1>Show Item</h1>
        
        <p><strong>Name:</strong> {{ $produto->name }}</p>
        <p><strong>Description:</strong> {{ $produto->description }}</p>
        <p><strong>Price:</strong> {{ $produto->price }}</p>
        <p><strong>In stock:</strong> {{ $produto->in_stock }}</p>
        <livewire:botao tipo="button" label="Back to list" href="{{ route('produtos.index') }}" />
    </div>
@endsection