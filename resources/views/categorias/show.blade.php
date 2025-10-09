{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <livewire:card
                    :data="$categoria"
                    titulo="Detalhes do Categoria"
                    :routeBase="'categorias'"
                />
            </div>
        </div>
    </div>
@endsection