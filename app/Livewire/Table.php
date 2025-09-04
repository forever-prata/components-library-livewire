<?php

namespace App\Livewire;

use Livewire\Component;

class Table extends Component
{
    public $headers = [];
    public $rows = [];
    public string $classeExtra = '';
    public string $titulo = 'Tabela';
    public bool $busca = false;
    public bool $selecionavel = false;
    public bool $colapsavel = false;
    public array $actionButtons = [];

    public function mount($headers = [], $rows = [], $classeExtra = '', $titulo = 'Tabela',
                         $busca = false, $selecionavel = false, $colapsavel = false, $actionButtons = [])
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->classeExtra = $classeExtra;
        $this->titulo = $titulo;
        $this->busca = $busca;
        $this->selecionavel = $selecionavel;
        $this->colapsavel = $colapsavel;
        $this->actionButtons = $actionButtons;
    }

    public function render()
    {
        $theme = config('design.system', 'govbr');
        return view("livewire.$theme.table");
    }
}
