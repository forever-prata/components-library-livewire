@php
    $hasIndexRoute = $routeBase && Route::has("{$routeBase}.index");
    $hasEditRoute    = $routeBase && $itemId && Route::has("{$routeBase}.edit");
    $hasDestroyRoute = $routeBase && $itemId && Route::has("{$routeBase}.destroy");

    $hasActions = $hasIndexRoute || $hasEditRoute || $hasDestroyRoute;
@endphp

<div class="card {{ $extraClass }}">
    {{-- Imagem --}}
    @if($withImage && data_get($data, $imageField))
        <img src="{{ data_get($data, $imageField) }}" class="card-img-top {{ $imageClass }}" alt="{{ $title }}" style="{{ $imageStyle }}">
    @endif

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ $title }}</h5>

        @if($hasActions)
        <div class="action-buttons">
            @if($hasIndexRoute)
            <livewire:botao
                type="secondary"
                :href="route($routeBase . '.index')"
                label="Voltar"
            />
            @endif

            @if($hasEditRoute)
            <livewire:botao
                type="primary"
                :href="route($routeBase . '.edit', $itemId)"
                label="Editar"
            />
            @endif

            @if($hasDestroyRoute)
            <form action="{{ route($routeBase . '.destroy', $itemId) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                @csrf
                @method('DELETE')
                <livewire:botao
                    type="danger"
                    label="Excluir"
                    buttonType="submit"
                />
            </form>
            @endif
        </div>
        @endif
    </div>

    <div class="card-body">
        {{-- Header com Avatar --}}
        @if($withAvatar && data_get($data, $avatarField))
        <div class="d-flex align-items-center mb-3">
            <img src="{{ data_get($data, $avatarField) }}" class="rounded-circle" style="width: 50px; height: 50px;">
            <div class="ms-3">
                <h6 class="card-subtitle mb-1 text-muted">
                    {{ data_get($data, 'nome') ?? data_get($data, 'name') ?? $title }}
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
                            @if(is_array($value))
                                <ul class="list-unstyled mb-0">
                                    @foreach($value as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @elseif(is_bool($value))
                                {{ $value ? 'Sim' : 'Não' }}
                            @elseif($value instanceof \DateTime)
                                {{ $value->format('d/m/Y H:i') }}
                            @else
                                {{ $value ?? 'N/A' }}
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
                    type="secondary"
                    :href="route($routeName, $routeParams)"
                    :label="$action"
                />
            @endif
        @endforeach
    </div>
    @endif
</div>
