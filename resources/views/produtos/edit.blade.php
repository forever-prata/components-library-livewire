{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Edit Produto</h1>
        <form action="{{ route('produtos.update', ${$modelVariable}->id) }}" method="POST">
            @csrf
            @method('PUT')
            <livewire:input type="text" name="name" label="Name" id="name" value="{{ old('name', ${$modelVariable}->name) }}" />
                    <livewire:input type="textarea" name="description" label="Description" id="description" value="{{ old('description', ${$modelVariable}->description) }}" />
                    <livewire:input type="number" name="price" label="Price" id="price" value="{{ old('price', ${$modelVariable}->price) }}" />
                    <livewire:checkbox name="in_stock" label="In stock" id="in_stock" :checked="old('in_stock', ${$modelVariable}->in_stock)" />
                    
            <div class="mt-4">
                <livewire:botao tipo="primary" label="Update" action="submit" />
                <livewire:botao tipo="secondary" label="Back" href="{{ route('produtos.index') }}" />
            </div>
        </form>
    </div>
@endsection