<?php

namespace GovbrComponentsLivewire\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Table extends Component
{
    public Collection $collection;

    public string $titulo = 'Tabela';
    public bool $busca = false;
    public bool $selecionavel = false;
    public bool $colapsavel = false;
    public array $botoesAcao = [];
    public string $classeExtra = '';
    public string $tituloAcoes = 'Ações';
    public bool $gerarAcoes = true;

    public array $headers = [];
    public array $rows = [];
    public array $colunas = [];

    public function mount(
        Collection $collection,
        string $titulo = 'Tabela',
        bool $busca = false,
        bool $selecionavel = false,
        bool $colapsavel = false,
        array $botoesAcao = [],
        string $classeExtra = '',
        array $colunas = [],
        string $tituloAcoes = 'Ações',
        bool $gerarAcoes = true
    )
    {
        $this->collection = $collection;
        $this->titulo = $titulo;
        $this->busca = $busca;
        $this->selecionavel = $selecionavel;
        $this->colapsavel = $colapsavel;
        $this->botoesAcao = $botoesAcao;
        $this->classeExtra = $classeExtra;
        $this->colunas = $colunas;
        $this->tituloAcoes = $tituloAcoes;
        $this->gerarAcoes = $gerarAcoes;
    }

    private function prepareData()
    {
        if ($this->collection->isNotEmpty()) {
            $firstItem = $this->collection->first();

            $this->headers = !empty($this->colunas)
                ? array_values($this->colunas)
                : (is_object($firstItem) && method_exists($firstItem, 'getAttributes')
                    ? array_keys($firstItem->getAttributes())
                    : array_keys((array) $firstItem)
                );

            $this->rows = $this->collection->map(function($item) {
                $rowData = is_array($item) ? $item : $item->getAttributes();

                if (!empty($this->columns)) {
                    $rowData = collect($this->columns)
                        ->keys()
                        ->mapWithKeys(fn($key) => [$key => $rowData[$key] ?? null])
                        ->toArray();
                }

                if ($item instanceof \Illuminate\Database\Eloquent\Model && isset($item->id) && $this->gerarAcoes) {
                    $tableName = $item->getTable();
                    $rowData[$this->tituloAcoes] = [
                        'show' => route("{$tableName}.show", $item->id),
                        'edit' => route("{$tableName}.edit", $item->id),
                        'delete' => route("{$tableName}.destroy", $item->id),
                    ];
                }

                return $rowData;
            })->toArray();

            if (!empty($this->rows)) {
                $hasActions = $this->gerarAcoes && (!empty($this->botoesAcao) || collect($this->rows)->contains(fn($row) => isset($row[$this->tituloAcoes])));
                if ($hasActions && !in_array($this->tituloAcoes, $this->headers)) {
                    $this->headers[] = $this->tituloAcoes;
                }
            }

        } else {
            $this->headers = [];
            $this->rows = [];
        }
    }

    public function render()
    {
        $this->prepareData();
        $theme = config('design.system', 'govbr');
        return view("livewire.{$theme}.table");
    }
}
