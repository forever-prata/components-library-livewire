@php
    $random = uniqid();
@endphp

<div class="br-table {{ $extraClass }}"
     @if($search) data-search="data-search" @endif
     @if($selectable) data-selection="data-selection" @endif
     @if($collapsible) data-collapse="data-collapse" @endif
     data-random="table-{{ $random }}">

  <div class="table-header">
    <div class="top-bar">
      <div class="table-title">{{ $title }}</div>

      @if($search)
        <div class="search-trigger">
          <button class="br-button circle" type="button" id="button-input-search-{{ $random }}" data-toggle="search"
            aria-label="Abrir busca" aria-controls="table-searchbox-{{ $random }}">
            <i class="fas fa-search" aria-hidden="true"></i>
          </button>
        </div>
      @endif
    </div>

    @if($search)
      <div class="search-bar">
        <div class="br-input">
          <label for="table-searchbox-{{ $random }}">Buscar na tabela</label>
          <input id="table-searchbox-{{ $random }}" type="search" placeholder="Buscar na tabela"
                 aria-labelledby="button-input-search-{{ $random }}" aria-label="Buscar na tabela"/>
          <button class="br-button" type="button" aria-label="Buscar">
            <i class="fas fa-search" aria-hidden="true"></i>
          </button>
        </div>
        <button class="br-button circle" type="button" data-dismiss="search" aria-label="Fechar busca">
          <i class="fas fa-times" aria-hidden="true"></i>
        </button>
      </div>
    @endif
  </div>

  <table>
    <thead>
      <tr>
        @if($selectable)
          <th class="column-checkbox" scope="col">
            <div class="br-checkbox hidden-label">
              <input id="check-all-{{ $random }}" type="checkbox" aria-label="Selecionar tudo"/>
              <label for="check-all-{{ $random }}">Selecionar todas as linhas</label>
            </div>
          </th>
        @endif

        @foreach($headers as $header)
          <th scope="col">{{ $header }}</th>
        @endforeach
      </tr>
    </thead>

    <tbody>
        @foreach($rows as $i => $row)
          <tr>
            @if($selectable)
              <td>
                <div class="br-checkbox hidden-label">
                  <input id="check-line-{{ $i }}-{{ $random }}" type="checkbox"
                         aria-label="Selecionar linha {{ $i + 1 }}"/>
                  <label for="check-line-{{ $i }}-{{ $random }}">Selecionar linha {{ $i + 1 }}</label>
                </div>
              </td>
            @endif

            {{-- Células normais + ações --}}
            @foreach($row as $index => $cell)
            <td data-th="{{ $headers[$index] ?? '' }}">
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
                @elseif(is_array($cell))
                    {{ json_encode($cell) }}
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
