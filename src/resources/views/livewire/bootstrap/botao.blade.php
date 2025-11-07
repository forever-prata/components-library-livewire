<div class="d-inline-block">
@if ($href)
<a
    href="{{ $href }}"
    class="btn btn-{{ $type }} @if($size) btn-{{ $size }} @endif {{ $extraClass }}"
>
    {{ $label }}
</a>
@else
<button
    @if ($action)
        wire:click="{{ $action }}"
    @endif
    type="{{ $buttonType }}"
    class="btn btn-{{ $type }} @if($size) btn-{{ $size }} @endif {{ $extraClass }}"
>
    {{ $label }}
</button>
@endif
</div>
