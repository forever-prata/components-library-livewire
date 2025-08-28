<div class="input-field">
    <select 
        id="{{ $id }}" 
        name="{{ $name }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    >
        <option value="" disabled selected>{{ $placeholder }}</option>
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}">{{ $optionLabel }}</option>
        @endforeach
    </select>
    <label for="{{ $id }}">{{ $label }}</label>
</div>
