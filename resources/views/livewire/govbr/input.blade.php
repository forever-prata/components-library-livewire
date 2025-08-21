<div class="br-input {{ $classeExtra }}">
    <label for="{{ $id }}">{{ $label }}</label>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    />
</div>
