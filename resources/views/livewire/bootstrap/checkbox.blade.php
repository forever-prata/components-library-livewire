<div class="form-check">
  <input class="form-check-input" type="checkbox" id="{{ $id }}" name="{{ $name }}" @if($checked) checked @endif>
  <label class="form-check-label" for="{{ $id }}">
    {{ $label }}
  </label>
</div>
