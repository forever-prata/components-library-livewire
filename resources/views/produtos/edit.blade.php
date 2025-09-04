@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <h1>Edit Item</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('produtos.update', $produto->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3"><livewire:input type="text" name="name" id="produtos_name_edit" label="Name" value="{{ {$value} }}" /></div>
            <div class="mb-3"><livewire:input type="textarea" name="description" id="produtos_description_edit" label="Description" value="{{ {$value} }}" /></div>
            <div class="mb-3"><livewire:input type="number" name="price" id="produtos_price_edit" label="Price" value="{{ {$value} }}" /></div>
            <div class="mb-3"><livewire:checkbox name="in_stock" id="produtos_in_stock_edit" label="In stock" :checked="{$value}" /></div>

            <livewire:botao tipo="submit" label="Update" />
            <livewire:botao tipo="button" label="Back" href="{{ route('produtos.index') }}" />
        </form>
    </div>
@endsection