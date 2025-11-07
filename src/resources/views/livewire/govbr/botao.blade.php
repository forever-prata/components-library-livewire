@php
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    @if ($action) wire:click="{{ $action }}" @endif
    type="{{ $buttonType }}"
    class="br-button {{ $type }} {{ $size }} {{ $extraClass }}"
>
    {{ $label }}
</{{ $tag }}>
