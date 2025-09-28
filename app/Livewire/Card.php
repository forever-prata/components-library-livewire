<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;

class Card extends Component
{
    public mixed $data;

    public string $titulo = 'Detalhes';
    public string $classeExtra = '';
    public array $campos = [];
    public bool $comImagem = false;
    public string $campoImagem = 'imagem';
    public bool $comAvatar = false;
    public string $campoAvatar = 'avatar';
    public array $actionButtons = [];
    public ?string $routeBase = null;

    public function mount(
        mixed $data,
        string $titulo = 'Detalhes',
        string $classeExtra = '',
        array $campos = [],
        bool $comImagem = false,
        string $campoImagem = 'imagem',
        bool $comAvatar = false,
        string $campoAvatar = 'avatar',
        array $actionButtons = [],
        ?string $routeBase = null
    ) {
        $this->data = $data;
        $this->titulo = $titulo;
        $this->classeExtra = $classeExtra;
        $this->campos = $campos;
        $this->comImagem = $comImagem;
        $this->campoImagem = $campoImagem;
        $this->comAvatar = $comAvatar;
        $this->campoAvatar = $campoAvatar;
        $this->actionButtons = $actionButtons;
        $this->routeBase = $routeBase;
    }

    public function getCardData(): array
    {
        $rawData = $this->extractData();
        $cardData = [];

        if (!empty($this->campos)) {
            foreach ($this->campos as $campo => $label) {
                $cardData[is_numeric($campo) ? $label : $label] = data_get($rawData, is_numeric($campo) ? $label : $campo);
            }
        } else {
            if (is_array($rawData)) {
                $cardData = $rawData;
            } elseif (is_object($rawData)) {
                $cardData = (array) $rawData;
            }

            $formattedData = [];
            foreach ($cardData as $key => $value) {
                if (!is_array($value) && !is_object($value)) {
                    $formattedData[ucfirst(str_replace('_', ' ', $key))] = $value;
                }
            }
            $cardData = $formattedData;
        }

        return array_filter($cardData);
    }

    public function extractData(): array
    {
        if ($this->data instanceof Model) {
            return $this->data->toArray();
        } elseif ($this->data instanceof Collection) {
            return $this->data->first()?->toArray() ?? [];
        } elseif (is_object($this->data)) {
            return (array) $this->data;
        }

        return $this->data;
    }

    public function getId(): mixed
    {
        if ($this->data instanceof Model) {
            return $this->data->getKey();
        }

        return data_get($this->data, 'id') ?? null;
    }

    private function getRouteBase(): ?string
    {
        if ($this->routeBase) {
            return $this->routeBase;
        }

        if ($this->data instanceof Model) {
            return $this->data->getTable();
        }

        return null;
    }

    public function render()
    {
        $theme = config('design.system', 'govbr');
        $cardData = $this->getCardData();
        $routeBase = $this->getRouteBase();
        $itemId = $this->getId();

        return view("livewire.{$theme}.card", [
            'cardData' => $cardData,
            'routeBase' => $routeBase,
            'itemId' => $itemId,
            'hasRoutes' => !is_null($routeBase) && !is_null($itemId)
        ]);
    }
}
