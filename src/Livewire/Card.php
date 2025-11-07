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

    public function getProcessedData(): array
    {
        if (!$this->data instanceof Model) {
            $data = is_object($this->data) ? (array)$this->data : $this->data;
            return array_filter($data);
        }

        $modelData = $this->data;
        $processedData = [];

        // Process attributes
        foreach ($modelData->getAttributes() as $key => $value) {
            if (!in_array($key, $modelData->getHidden()) && $key !== 'id' && !str_ends_with($key, '_id')) {
                $processedData[ucfirst(str_replace('_', ' ', $key))] = $value;
            }
        }

        // Process relations
        foreach ($modelData->getRelations() as $relationName => $relation) {
            $displayName = ucfirst(str_replace('_', ' ', $relationName));

            if ($relation instanceof Collection) {
                if ($relation->isNotEmpty()) {
                    $items = $relation->map(function ($item) {
                        return $item->name ?? $item->nome ?? $item->title ?? 'ID: ' . $item->id;
                    })->all();
                    $processedData[$displayName] = $items;
                }
            } elseif ($relation instanceof Model) {
                $processedData[$displayName] = $relation->name ?? $relation->nome ?? $relation->title ?? 'ID: ' . $relation->id;
            }
        }

        return $processedData;
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
        $cardData = $this->getProcessedData();
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
