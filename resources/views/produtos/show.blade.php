{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <livewire:card
                    :data="$produto"
                    titulo="Detalhes do Produto"
                    :routeBase="'produtos'"
                />
            </div>
        </div>
    </div>
@endsection
