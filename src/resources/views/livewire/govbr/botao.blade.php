@php
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    @if ($action) wire:click="{{ $action }}" @endif
    type="{{ $tipoBotao }}"
    class="br-button {{ $tipo }} {{ $tamanho }} {{ $classeExtra }}"
>
    {{ $label }}
</{{ $tag }}>
