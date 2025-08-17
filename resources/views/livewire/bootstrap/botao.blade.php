<div class="d-inline-block">
@if ($href)
<a
    href="{{ $href }}"
    class="btn btn-{{ $tipo }} @if($tamanho) btn-{{ $tamanho }} @endif {{ $classeExtra }}"
>
    {{ $label }}
</a>
@else
<button
    @if ($action)
        wire:click="{{ $action }}"
    @endif
    type="{{ $tipoBotao }}"
    class="btn btn-{{ $tipo }} @if($tamanho) btn-{{ $tamanho }} @endif {{ $classeExtra }}"
>
    {{ $label }}
</button>
@endif
</div>
