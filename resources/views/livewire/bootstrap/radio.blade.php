<div class="form-check">
  <input class="form-check-input" type="radio" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" @if($checked) checked @endif>
  <label class="form-check-label" for="{{ $id }}">
    {{ $label }}
  </label>
</div>
