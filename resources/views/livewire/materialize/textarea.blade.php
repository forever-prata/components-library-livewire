<div class="input-field">
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        class="materialize-textarea"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    >{{ $value }}</textarea>
    <label for="{{ $id }}">{{ $label }}</label>
</div>
