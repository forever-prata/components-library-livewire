<?php

namespace App\Livewire;

use Livewire\Component;

class Input extends Component
{
    public string $type;
    public string $name;
    public string $label;
    public ?string $id;
    public ?string $placeholder;
    public ?string $value;
    public ?string $wireModel;
    public string $classeExtra;

    public function mount(
        string $type = 'text',
        string $name = '',
        string $label = '',
        string $id = null,
        string $placeholder = null,
        string $value = null,
        string $wireModel = null,
        string $classeExtra = ''
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->id = $id ?? $name;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->wireModel = $wireModel;
        $this->classeExtra = $classeExtra;
    }

    public function render()
    {
        $theme = config('design.system');
        return view('livewire.' . $theme . '.input');
    }
}
