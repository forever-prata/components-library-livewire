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


    public array $headers = [];
    public array $rows = [];

    public function mount(Collection $collection, string $titulo = 'Tabela', bool $busca = false, bool $selecionavel = false, bool $colapsavel = false, array $actionButtons = [], string $classeExtra = '')
    {
        $this->collection = $collection;
        $this->titulo = $titulo;
        $this->busca = $busca;
        $this->selecionavel = $selecionavel;
        $this->colapsavel = $colapsavel;
        $this->actionButtons = $actionButtons;
        $this->classeExtra = $classeExtra;
    }

    private function prepareData()
    {
        if ($this->collection->isNotEmpty()) {
            $firstItem = $this->collection->first();

            $this->headers = array_keys($firstItem->getAttributes());

            $this->rows = $this->collection->map(function($item) {
                $tableName = $item->getTable();
                $rowData = $item->getAttributes();

                $rowData['actions'] = [
                    'show' => route("{$tableName}.show", $item->id),
                    'edit' => route("{$tableName}.edit", $item->id),
                    'delete' => route("{$tableName}.destroy", $item->id),
                ];
                return $rowData;
            })->toArray();

            if (!empty($this->rows)) {
                if (!in_array('actions', $this->headers)) {
                    $this->headers[] = 'actions';
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
