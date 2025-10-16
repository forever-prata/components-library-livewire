{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <livewire:card
                    :data="$post"
                    titulo="Detalhes do Post"
                    :routeBase="'posts'"
                />
            </div>
        </div>
    </div>
@endsection