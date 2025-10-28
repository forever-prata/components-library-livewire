@php
    $hasIndexRoute = $routeBase && Route::has("{$routeBase}.index");
    $hasEditRoute    = $routeBase && $itemId && Route::has("{$routeBase}.edit");
    $hasDestroyRoute = $routeBase && $itemId && Route::has("{$routeBase}.destroy");

    $hasActions = $hasIndexRoute || $hasEditRoute || $hasDestroyRoute;
@endphp

<div class="card {{ $classeExtra }}">
    {{-- Imagem --}}
    @if($comImagem && data_get($data, $campoImagem))
        <img src="{{ data_get($data, $campoImagem) }}" class="card-img-top {{ $classeImagem }}" alt="{{ $titulo }}" style="{{ $estiloImagem }}">
    @endif

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ $titulo }}</h5>

        @if($hasActions)
        <div class="action-buttons">
            @if($hasIndexRoute)
            <livewire:botao
                tipo="secondary"
                :href="route($routeBase . '.index')"
                label="Voltar"
            />
            @endif

            @if($hasEditRoute)
            <livewire:botao
                tipo="primary"
                :href="route($routeBase . '.edit', $itemId)"
                label="Editar"
            />
            @endif

            @if($hasDestroyRoute)
            <form action="{{ route($routeBase . '.destroy', $itemId) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                @csrf
                @method('DELETE')
                <livewire:botao
                    tipo="danger"
                    label="Excluir"
                    tipoBotao="submit"
                />
            </form>
            @endif
        </div>
        @endif
    </div>

    <div class="card-body">
        {{-- Header com Avatar --}}
        @if($comAvatar && data_get($data, $campoAvatar))
        <div class="d-flex align-items-center mb-3">
            <img src="{{ data_get($data, $campoAvatar) }}" class="rounded-circle" style="width: 50px; height: 50px;">
            <div class="ms-3">
                <h6 class="card-subtitle mb-1 text-muted">
                    {{ data_get($data, 'nome') ?? data_get($data, 'name') ?? $titulo }}
                </h6>
                @if(data_get($data, 'cargo') || data_get($data, 'position'))
                <p class="card-text small">{{ data_get($data, 'cargo') ?? data_get($data, 'position') }}</p>
                @endif
            </div>
        </div>
        @endif

        {{-- Conteúdo do Card --}}
        @if(empty($cardData))
            <p class="text-center text-muted">Nenhum dado disponível para exibição.</p>
        @else
            <ul class="list-group list-group-flush">
                @foreach($cardData as $label => $value)
                    <li class="list-group-item">
                        <strong>{{ $label }}:</strong>
                        <span>
                            @if(is_bool($value))
                                {{ $value ? 'Sim' : 'Não' }}
                            @elseif($value instanceof \DateTime)
                                {{ $value->format('d/m/Y H:i') }}
                            @else
                                {{ $value }}
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Ações customizadas --}}
    @if(!empty($actionButtons))
    <div class="card-footer text-end">
        @foreach($actionButtons as $action => $routeConfig)
            @php
                if (is_array($routeConfig)) {
                    $routeName = $routeConfig['route'];
                    $routeParams = $routeConfig['params'] ?? $itemId;
                } else {
                    $routeName = $routeConfig;
                    $routeParams = $itemId;
                }
            @endphp

            @if(\Illuminate\Support\Facades\Route::has($routeName))
                <livewire:botao
                    tipo="secondary"
                    :href="route($routeName, $routeParams)"
                    :label="$action"
                />
            @endif
        @endforeach
    </div>
    @endif
</div>
