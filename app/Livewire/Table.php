<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Table extends Component
{
    public Collection $collection;

    public string $titulo = 'Tabela';
    public bool $busca = false;
    public bool $selecionavel = false;
    public bool $colapsavel = false;
    public array $actionButtons = [];
    public string $classeExtra = '';
    public string $actionsTitle = 'Ações';
    public bool $gerarAcoes = true;

    public array $headers = [];
    public array $rows = [];
    public array $columns = [];

    public function mount(
        Collection $collection,
        string $titulo = 'Tabela',
        bool $busca = false,
        bool $selecionavel = false,
        bool $colapsavel = false,
        array $actionButtons = [],
        string $classeExtra = '',
        array $columns = [],
        string $actionsTitle = 'Ações',
        bool $gerarAcoes = true
    )
    {
        $this->collection = $collection;
        $this->titulo = $titulo;
        $this->busca = $busca;
        $this->selecionavel = $selecionavel;
        $this->colapsavel = $colapsavel;
        $this->actionButtons = $actionButtons;
        $this->classeExtra = $classeExtra;
        $this->columns = $columns;
        $this->actionsTitle = $actionsTitle;
        $this->gerarAcoes = $gerarAcoes;
    }

    private function prepareData()
    {
        if ($this->collection->isNotEmpty()) {
            $firstItem = $this->collection->first();

            $this->headers = !empty($this->columns)
                ? array_values($this->columns)
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
                    $rowData[$this->actionsTitle] = [
                        'show' => route("{$tableName}.show", $item->id),
                        'edit' => route("{$tableName}.edit", $item->id),
                        'delete' => route("{$tableName}.destroy", $item->id),
                    ];
                }

                return $rowData;
            })->toArray();

            if (!empty($this->rows)) {
                $hasActions = $this->gerarAcoes && (!empty($this->actionButtons) || collect($this->rows)->contains(fn($row) => isset($row[$this->actionsTitle])));
                if ($hasActions && !in_array($this->actionsTitle, $this->headers)) {
                    $this->headers[] = $this->actionsTitle;
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
