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

    public function mount($headers = [], $rows = [], $classeExtra = '', $titulo = 'Tabela', $busca = false)
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->classeExtra = $classeExtra;
        $this->titulo = $titulo;
        $this->busca = $busca;
    }

    public function render()
    {
        $theme = config('design.system', 'govbr');
        return view("livewire.$theme.table");
    }
}
