@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <h1>Create New Item</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('produtos.store') }}" method="POST">
            @csrf
            <div class="mb-3"><livewire:input type="text" name="name" id="produtos_name" label="Name" placeholder="Enter Name" /></div>
            <div class="mb-3"><livewire:input type="textarea" name="description" id="produtos_description" label="Description" placeholder="Enter Description" /></div>
            <div class="mb-3"><livewire:input type="number" name="price" id="produtos_price" label="Price" placeholder="Enter Price" /></div>
            <div class="mb-3"><livewire:checkbox name="in_stock" id="produtos_in_stock" label="In stock" /></div>

            <livewire:botao tipo="submit" label="Save" />
            <livewire:botao tipo="button" label="Back" href="{{ route('produtos.index') }}" />
        </form>
    </div>
@endsection