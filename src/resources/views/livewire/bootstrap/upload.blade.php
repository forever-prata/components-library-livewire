<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <input class="form-control" type="file" id="{{ $id }}" {{ $multiple ? 'multiple' : '' }} wire:model="files">

    @if (!empty($files))
        <ul class="list-group mt-2">
            @foreach ($files as $index => $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        {{ $file->getClientOriginalName() }}
                        <span class="text-muted">({{ round($file->getSize() / 1024, 2) }} KB)</span>
                    </div>
                    <button class="btn btn-danger btn-sm" type="button" wire:click="remove({{ $index }})">
                        Remover
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</div>
