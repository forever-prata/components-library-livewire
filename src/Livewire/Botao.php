<?php

namespace GovbrComponentsLivewire\Livewire;

use Livewire\Component;

class Botao extends Component
{
    public string $tipo;
    public string $tamanho;
    public string $tipoBotao;
    public string $classeExtra;
    public ?string $href;
    public ?string $action;
    public string $label;

    public function mount(
        string $tipo = 'primary',
        string $tamanho = '',
        string $tipoBotao = 'button',
        string $classeExtra = '',
        string $href = null,
        string $action = null,
        string $label = ''
    ) {
        $this->tipo = $tipo;
        $this->tamanho = $tamanho;
        $this->tipoBotao = $tipoBotao;
        $this->classeExtra = $classeExtra;
        $this->href = $href;
        $this->action = $action;
        $this->label = $label;
    }

    public function render()
    {
        $theme = config('design.system');
        return view('livewire.' . $theme . '.botao');
    }
}
