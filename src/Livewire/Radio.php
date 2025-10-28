<?php

namespace GovbrComponentsLivewire\Livewire;

use Livewire\Component;

class Radio extends Component
{
    public $name;
    public $label;
    public $id;
    public $value;
    public $checked;

    public function mount($name, $label, $id = null, $value = null, $checked = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->id = $id ?? $name;
        $this->value = $value ?? $id;
        $this->checked = $checked;
    }

    public function render()
    {
        $theme = config('design.system', 'govbr');
        return view("livewire.{$theme}.radio");
    }
}
