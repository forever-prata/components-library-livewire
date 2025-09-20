<div class="responsive-table">
    <table class="{{ $classeExtra }}">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th style="white-space: nowrap;">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
                <tr>
                    @foreach($row as $index => $cell)
                        <td data-th="{{ $headers[$index] ?? '' }}" style="white-space: nowrap;">
                            @if(is_array($cell) && isset($cell['show'], $cell['edit'], $cell['delete']))
                                <div style="display:flex; gap: 5px; align-items:center;">
                                    {{-- Botão Show --}}
                                    <livewire:botao tipo="secondary" tamanho="small" label="Show" :href="$cell['show']" />

                                    {{-- Botão Edit --}}
                                    <livewire:botao tipo="secondary" tamanho="small" label="Edit" :href="$cell['edit']" />

                                    {{-- Botão Delete --}}
                                    <form action="{{ $cell['delete'] }}" method="POST"
                                          onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <livewire:botao tipo="danger" tamanho="small" label="Delete" tipoBotao="submit" />
                                    </form>
                                </div>
                            @else
                                <span style="white-space: nowrap;">{{ $cell }}</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
