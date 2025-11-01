<?php

namespace App\Livewire;

use Livewire\Component;

class Botao extends Component
{
    public string $type;
    public string $size;
    public string $buttonType;
    public string $extraClass;
    public ?string $href;
    public ?string $action;
    public string $label;

    public function mount(
        string $type = 'primary',
        string $size = '',
        string $buttonType = 'button',
        string $extraClass = '',
        string $href = null,
        string $action = null,
        string $label = ''
    ) {
        $this->type = $type;
        $this->size = $size;
        $this->buttonType = $buttonType;
        $this->extraClass = $extraClass;
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
