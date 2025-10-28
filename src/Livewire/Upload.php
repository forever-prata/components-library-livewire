<?php

namespace GovbrComponentsLivewire\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    public string $label;
    public ?string $id;
    public bool $multiple;
    public ?string $wireModel;
    public $files = [];

    public function mount(
        string $label = 'Envio de arquivos',
        string $id = null,
        bool $multiple = false,
        string $wireModel = null
    ) {
        $this->label = $label;
        $this->id = $id ?? uniqid('upload-');
        $this->multiple = $multiple;
        $this->wireModel = $wireModel;
    }

    public function updatedFiles()
    {
        $this->validate([
            'files.*' => 'max:102400', // 100MB Max
        ]);
    }

    public function remove($index)
    {
        if (is_array($this->files)) {
            array_splice($this->files, $index, 1);
        }
    }


    public function render()
    {
        $theme = config('design.system');
        return view('livewire.' . $theme . '.upload');
    }
}
