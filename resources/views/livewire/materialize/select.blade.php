<div class="input-field">
    <select 
        id="{{ $id }}" 
        name="{{ $name }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
    >
        <option value="" disabled>{{ $placeholder }}</option>
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" @if($selected == $value) selected @endif>{{ $optionLabel }}</option>
        @endforeach
    </select>
    <label for="{{ $id }}">{{ $label }}</label>
</div>
