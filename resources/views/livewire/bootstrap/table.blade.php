<div>
    <table class="table {{ $classeExtra }}">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th scope="col">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
                <tr>
                    @foreach($row as $index => $cell)
                        <td>
                            @if(is_array($cell) && isset($cell['show']))
            <div style="display: flex; align-items: center; gap: 5px;">
                <livewire:botao tipo="secondary" tamanho="small" :href="$cell['show']" label="Show" />
                <livewire:botao tipo="secondary" tamanho="small" :href="$cell['edit']" label="Edit" />
                <form action="{{ $cell['delete'] }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                    @csrf
                    @method('DELETE')
                    <livewire:botao tipo="danger" tamanho="small" label="Delete" type="submit" />
                </form>
            </div>
        @else
            {{ $cell }}
        @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
