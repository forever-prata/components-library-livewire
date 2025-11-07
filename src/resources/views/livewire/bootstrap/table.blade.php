<div>
    <table class="table {{ $extraClass }}">
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
                                    <livewire:botao type="secondary" size="small" :href="$cell['show']" label="Detalhes" />
                                    <livewire:botao type="secondary" size="small" :href="$cell['edit']" label="Editar" />
                                    <form action="{{ $cell['delete'] }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                                        @csrf
                                        @method('DELETE')
                                        <livewire:botao type="danger" size="small" label="Excluir" buttonType="submit" />
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
