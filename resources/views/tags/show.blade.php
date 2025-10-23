{{-- gerado automaticamente pela biblioteca --}}
@extends('layouts.scaffold')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <livewire:card
                    :data="$tag"
                    titulo="Detalhes do Tag"
                    :routeBase="'tags'"
                />
            </div>
        </div>
    </div>
@endsection