@php
    $hasIndexRoute = $routeBase && Route::has("{$routeBase}.index");
    $hasEditRoute    = $routeBase && $itemId && Route::has("{$routeBase}.edit");
    $hasDestroyRoute = $routeBase && $itemId && Route::has("{$routeBase}.destroy");

    $hasActions = $hasIndexRoute || $hasEditRoute || $hasDestroyRoute;
@endphp

<div class="card-container {{ $extraClass }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="card-title">{{ $title }}</h2>

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

    <div class="br-card">
        {{-- Imagem --}}
        @if($withImage && data_get($data, $imageField))
        <div class="card-content text-center">
            <img src="{{ data_get($data, $imageField) }}"
                 alt="{{ $title }}"
                 style="{{ $imageStyle }}"
                 class="mb-3 {{ $imageClass }}"/>
        </div>
        @endif

        {{-- Header com Avatar --}}
        @if($withAvatar && data_get($data, $avatarField))
        <div class="card-header">
            <div class="d-flex align-items-center">
                <span class="br-avatar" title="{{ data_get($data, 'nome') ?? data_get($data, 'name') ?? $title }}">
                    <span class="content">
                        <img src="{{ data_get($data, $avatarField) }}"/>
                    </span>
                </span>
                <div class="ml-3">
                    <div class="text-weight-semi-bold text-up-02">
                        {{ data_get($data, 'nome') ?? data_get($data, 'name') ?? $title }}
                    </div>
                    @if(data_get($data, 'cargo') || data_get($data, 'position'))
                    <div>{{ data_get($data, 'cargo') ?? data_get($data, 'position') }}</div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Conteúdo do Card --}}
        <div class="card-content">
            @if(empty($cardData))
                <p class="text-center text-muted">Nenhum dado disponível para exibição.</p>
            @else
                <div class="row">
                    @foreach($cardData as $label => $value)
                        <div class="col-md-6 mb-3">
                            <div class="field-group">
                                <strong class="field-label d-block text-up-01 text-medium pb-1">
                                    {{ $label }}:
                                </strong>
                                <span class="field-value">
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
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Ações customizadas --}}
        @if(!empty($actionButtons))
        <div class="card-footer">
            <div class="d-flex justify-content-end">
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
        </div>
        @endif
    </div>
</div>
