<div>
    <div class="br-upload">
        <label class="upload-label" for="{{ $id }}"><span>{{ $label }}</span></label>
        <input class="upload-input" id="{{ $id }}" type="file" {{ $multiple ? 'multiple' : '' }} wire:model="files" aria-label="enviar arquivo"/>
        <div class="upload-list"></div>
    </div>

    @if (!empty($files))
        <div class="upload-list mt-2">
            @foreach ($files as $index => $file)
                <div class="br-item">
                    <div class="row align-items-center">
                        <div class="col-auto"><i class="fas fa-file"></i></div>
                        <div class="col">
                            {{ $file->getClientOriginalName() }}
                            <span class="text-muted">({{ round($file->getSize() / 1024, 2) }} KB)</span>
                        </div>
                        <div class="col-auto">
                            <button class="br-button circle small" type="button" wire:click="remove({{ $index }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <p class="text-base mt-1">Clique ou arraste os arquivos para cima do componente Upload.</p>
</div>
