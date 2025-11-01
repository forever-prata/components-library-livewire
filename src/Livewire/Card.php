<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;

class Card extends Component
{
    public mixed $data;

    public string $title = 'Detalhes';
    public string $extraClass = '';
    public array $fields = [];
    public bool $withImage = false;
    public string $imageField = 'imagem';
    public string $imageClass = '';
    public string $imageStyle = '';
    public bool $withAvatar = false;
    public string $avatarField = 'avatar';
    public array $actionButtons = [];
    public ?string $routeBase = null;

    public function mount(
        mixed $data,
        string $title = 'Detalhes',
        string $extraClass = '',
        array $fields = [],
        bool $withImage = false,
        string $imageField = 'imagem',
        string $imageClass = '',
        string $imageStyle = '',
        bool $withAvatar = false,
        string $avatarField = 'avatar',
        array $actionButtons = [],
        ?string $routeBase = null
    ) {
        $this->data = $data;
        $this->title = $title;
        $this->extraClass = $extraClass;
        $this->fields = $fields;
        $this->withImage = $withImage;
        $this->imageField = $imageField;
        $this->imageClass = $imageClass;
        $this->imageStyle = $imageStyle;
        $this->withAvatar = $withAvatar;
        $this->avatarField = $avatarField;
        $this->actionButtons = $actionButtons;
        $this->routeBase = $routeBase;
    }

    public function getCardData(): array
    {
        $rawData = $this->extractData();
        $cardData = [];

        if (!empty($this->fields)) {
            foreach ($this->fields as $field => $label) {
                $cardData[is_numeric($field) ? $label : $label] = data_get($rawData, is_numeric($field) ? $label : $field);
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
