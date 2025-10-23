<div class="input-field">
    <select 
        id="{{ $id }}" 
        name="{{ $multiple ? $name . '[]' : $name }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
        {{ $multiple ? 'multiple' : '' }}
    >
        <option value="" {{ $multiple ? 'disabled' : '' }}>{{ $placeholder }}</option>
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" {{ (is_array($selected) && in_array($value, $selected)) || $selected == $value ? 'selected' : '' }}>{{ $optionLabel }}</option>
        @endforeach
    </select>
    <label for="{{ $id }}">{{ $label }}</label>
</div>
