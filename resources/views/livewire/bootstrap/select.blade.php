<div>
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <select 
        class="form-select" 
        id="{{ $id }}" 
        name="{{ $name }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    >
        <option selected value="">{{ $placeholder }}</option>
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}">{{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
