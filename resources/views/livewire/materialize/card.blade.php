@php
    $hasIndexRoute = $routeBase && Route::has("{$routeBase}.index");
    $hasEditRoute    = $routeBase && $itemId && Route::has("{$routeBase}.edit");
    $hasDestroyRoute = $routeBase && $itemId && Route::has("{$routeBase}.destroy");

    $hasActions = $hasIndexRoute || $hasEditRoute || $hasDestroyRoute;
@endphp

<div class="card {{ $classeExtra }}">
    {{-- Imagem --}}
    @if($comImagem && data_get($data, $campoImagem))
        <div class="card-image">
            <img src="{{ data_get($data, $campoImagem) }}" alt="{{ $titulo }}" class="{{ $classeImagem }}" style="{{ $estiloImagem }}">
        </div>
    @endif

    <div class="card-content">
        <div class="row mb-0">
            <div class="col s8">
                <span class="card-title">{{ $titulo }}</span>
            </div>
            <div class="col s4 right-align">
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
        </div>


        {{-- Header com Avatar --}}
        @if($comAvatar && data_get($data, $campoAvatar))
        <div class="valign-wrapper mb-2">
            <img src="{{ data_get($data, $campoAvatar) }}" alt="" class="circle responsive-img" style="width: 50px; height: 50px;">
            <div class="ms-3">
                <h6 class="subtitle">
                    {{ data_get($data, 'nome') ?? data_get($data, 'name') ?? $titulo }}
                </h6>
                @if(data_get($data, 'cargo') || data_get($data, 'position'))
                <p class="small-text">{{ data_get($data, 'cargo') ?? data_get($data, 'position') }}</p>
                @endif
            </div>
        </div>
        @endif

        {{-- Conteúdo do Card --}}
        @if(empty($cardData))
            <p class="center-align grey-text">Nenhum dado disponível para exibição.</p>
        @else
            <ul class="collection">
                @foreach($cardData as $label => $value)
                    <li class="collection-item">
                        <strong>{{ $label }}:</strong>
                        <span class="right">
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
    <div class="card-action right-align">
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
