<div class="form-group {{ $classeExtra }}">
    <label for="{{ $id }}">{{ $label }}</label>
    <textarea
        class="form-control"
        id="{{ $id }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    >{{ $value }}</textarea>
</div>
