@if ($href)
<a
    href="{{ $href }}"
    class="btn btn-{{ $tipo }} @if($tamanho) btn-{{ $tamanho }} @endif {{ $classeExtra }}"
>
    {{ $slot }}
</a>
@else
<button
    @if ($action)
        wire:click="{{ $action }}"
    @endif
    type="{{ $tipoBotao }}"
    class="btn btn-{{ $tipo }} @if($tamanho) btn-{{ $tamanho }} @endif {{ $classeExtra }}"
>
    {{ $slot }}
</button>
@endif
