<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <input
        type="{{ $type }}"
        class="form-control"
        id="{{ $id }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    />
</div>
