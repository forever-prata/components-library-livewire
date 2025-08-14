<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Botao extends Component
{
    public string $tipo;
    public string $tamanho;
    public string $tipoBotao;
    public string $classeExtra;
    public ?string $href;
    public ?string $action;
    public string $ds;

    public function __construct(
        string $tipo = 'primary',
        string $tamanho = '',
        string $tipoBotao = 'button',
        string $classeExtra = '',
        string $href = null,
        string $action = null
    ) {
        $this->tipo = $tipo;
        $this->tamanho = $tamanho;
        $this->tipoBotao = $tipoBotao;
        $this->classeExtra = $classeExtra;
        $this->href = $href;
        $this->action = $action;
        $this->ds = config('design.system');
    }

    public function render(): View
    {
        $theme = config('design.system');
        return view('components.' . $theme . '.botao');
    }
}
