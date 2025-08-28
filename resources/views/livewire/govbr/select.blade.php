<div class="br-select">
    <div class="br-input">
      <label for="{{ $id }}">{{ $label }}</label>
      <input id="{{ $id }}" type="text" placeholder="Selecione o item" readonly />
      <button class="br-button" type="button" aria-label="Exibir lista" tabindex="-1" data-trigger>
        <i class="fas fa-angle-down" aria-hidden="true"></i>
      </button>
    </div>
    <div class="br-list" tabindex="0">
      @foreach($options as $value => $optionLabel)
      <div class="br-item" tabindex="-1">
        <div class="br-radio">
          <input id="{{ $id . '-' . $loop->index }}" type="radio" name="{{ $name }}" value="{{ $value }}"
            @if($wireModel) wire:model="{{ $wireModel }}" @endif />
          <label for="{{ $id . '-' . $loop->index }}">{{ $optionLabel }}</label>
        </div>
      </div>
      @endforeach
    </div>
  </div>
