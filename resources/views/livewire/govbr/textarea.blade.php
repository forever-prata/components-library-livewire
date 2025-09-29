<div class="br-textarea {{ $state }} {{ $classeExtra }}">
    <label for="{{ $id }}">{{ $label }}</label>
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
        {{ $disabled ? 'disabled' : '' }}
    >{{ $value }}</textarea>

</div>
