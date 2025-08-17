<div style="display: inline-block;">
@if ($href)
<a
    href="{{ $href }}"
    class="btn @if($tamanho) btn-{{ $tamanho }} @endif waves-effect waves-light {{ $classeExtra }}"
>
    {{ $label }}
</a>
@else
<button
    @if ($action)
        wire:click="{{ $action }}"
    @endif
    type="{{ $tipoBotao }}"
    class="btn @if($tamanho) btn-{{ $tamanho }} @endif waves-effect waves-light {{ $classeExtra }}"
>
    {{ $label }}
</button>
@endif
</div>
