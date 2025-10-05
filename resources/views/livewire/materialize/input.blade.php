<div class="input-field">
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        class="validate"
        value="{{ $value }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    />
    <label for="{{ $id }}">{{ $label }}</label>
</div>
