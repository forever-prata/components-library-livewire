<div>
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <select
        class="form-select"
        id="{{ $id }}"
        name="{{ $multiple ? $name . '[]' : $name }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
        {{ $multiple ? 'multiple' : '' }}
    >
        @if(!$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" {{ (is_array($selected) && in_array($value, $selected)) || $selected == $value ? 'selected' : '' }}>{{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
