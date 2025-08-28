<?php

namespace App\Livewire;

use Livewire\Component;

class Checkbox extends Component
{
    public $name;
    public $label;
    public $id;
    public $checked;

    public function mount($name, $label, $id = null, $checked = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->id = $id ?? $name;
        $this->checked = $checked;
    }

    public function render()
    {
        $theme = config('design.system', 'govbr');
        return view("livewire.{$theme}.checkbox");
    }
}
