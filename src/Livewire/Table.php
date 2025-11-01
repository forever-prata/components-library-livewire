<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Table extends Component
{
    public Collection $collection;

    public string $title = 'Tabela';
    public bool $search = false;
    public bool $selectable = false;
    public bool $collapsible = false;
    public array $actionButtons = [];
    public string $extraClass = '';
    public string $actionsTitle = 'Ações';
    public bool $generateActions = true;

    public array $headers = [];
    public array $rows = [];
    public array $columns = [];

    public function mount(
        Collection $collection,
        string $title = 'Tabela',
        bool $search = false,
        bool $selectable = false,
        bool $collapsible = false,
        array $actionButtons = [],
        string $extraClass = '',
        array $columns = [],
        string $actionsTitle = 'Ações',
        bool $generateActions = true
    )
    {
        $this->collection = $collection;
        $this->title = $title;
        $this->search = $search;
        $this->selectable = $selectable;
        $this->collapsible = $collapsible;
        $this->actionButtons = $actionButtons;
        $this->extraClass = $extraClass;
        $this->columns = $columns;
        $this->actionsTitle = $actionsTitle;
        $this->generateActions = $generateActions;
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

                if ($item instanceof \Illuminate\Database\Eloquent\Model && isset($item->id) && $this->generateActions) {
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
                $hasActions = $this->generateActions && (!empty($this->actionButtons) || collect($this->rows)->contains(fn($row) => isset($row[$this->actionsTitle])));
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
