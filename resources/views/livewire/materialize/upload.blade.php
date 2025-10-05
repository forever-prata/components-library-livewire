<div>
    <div class="file-field input-field">
        <div class="btn">
            <span>{{ $label }}</span>
            <input type="file" id="{{ $id }}" {{ $multiple ? 'multiple' : '' }} wire:model="files">
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="{{ $multiple ? 'Selecione um ou mais arquivos' : 'Selecione um arquivo' }}">
        </div>
    </div>

    @if (!empty($files))
        <ul class="collection">
            @foreach ($files as $index => $file)
                <li class="collection-item">
                    <div>
                        {{ $file->getClientOriginalName() }}
                        <span class="grey-text">({{ round($file->getSize() / 1024, 2) }} KB)</span>
                        <a href="#!" class="secondary-content" wire:click="remove({{ $index }})">
                            <i class="material-icons">delete</i>
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
