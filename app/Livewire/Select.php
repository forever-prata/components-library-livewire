<?php

namespace App\Livewire;

use Livewire\Component;

class Select extends Component
{
    public string $name;
    public string $label;
    public string $id;
    public array $options = [];
    public ?string $placeholder = null;
    public ?string $wireModel = null;
    public $selected = null;
    public bool $multiple = false;

    public function mount(string $name, string $label, string $id, array $options = [], ?string $placeholder = null, ?string $wireModel = null, $selected = null, bool $multiple = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->id = $id;
        $this->options = $options;
        $this->placeholder = $placeholder ?? 'Selecione uma opção';
        $this->wireModel = $wireModel;
        $this->selected = $selected;
        $this->multiple = $multiple;
    }

    public function render()
    {
        $theme = config('design.system', 'govbr');
        return view("livewire.{$theme}.select");
    }
}