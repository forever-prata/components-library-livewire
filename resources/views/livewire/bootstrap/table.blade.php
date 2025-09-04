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
                            @if(is_array($cell) && isset($cell['show'], $cell['edit'], $cell['delete']))
                                {{-- Botão Show --}}
                                <livewire:botao tipo="secondary" tamanho="small" label="Show" :href="$cell['show']" />

                                {{-- Botão Edit --}}
                                <livewire:botao tipo="secondary" tamanho="small" label="Edit" :href="$cell['edit']" />

                                {{-- Botão Delete --}}
                                <form action="{{ $cell['delete'] }}" method="POST"
                                      onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <livewire:botao tipo="danger" tamanho="small" label="Delete" type="submit" />
                                </form>
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
