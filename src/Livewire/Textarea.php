<?php

namespace GovbrComponentsLivewire\Livewire;

use Livewire\Component;

class Textarea extends Component
{
    public string $name;
    public string $label;
    public ?string $id;
    public ?string $placeholder;
    public ?string $value;
    public ?string $wireModel;
    public int $rows;
    public string $state;
    public bool $disabled;

    public function mount(
        string $name = '',
        string $label = '',
        string $id = null,
        string $placeholder = null,
        string $value = null,
        string $wireModel = null,
        int $rows = 4,
        string $state = '',
        bool $disabled = false
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->id = $id ?? $name;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->wireModel = $wireModel;
        $this->rows = $rows;
        $this->state = $state;
        $this->disabled = $disabled;
    }

    public function render()
    {
        $theme = config('design.system');
        return view('livewire.' . $theme . '.textarea');
    }
}
