<?php

namespace Arkhas\LivewireDatatable\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;

class ColumnActionGroup
{
    protected ?string $icon = null;
    protected array $actions = [];

    /**
     * Create a new column action group instance.
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Set the trigger icon.
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the trigger icon.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Set the actions in this group.
     */
    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get all actions.
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get an action by name.
     */
    public function getAction(string $name): ?ColumnAction
    {
        foreach ($this->actions as $action) {
            if ($action->getName() === $name) {
                return $action;
            }
        }

        return null;
    }

    /**
     * Convert the group to an array for a specific model.
     */
    public function toArrayForModel(Model $model): array
    {
        return [
            'icon' => $this->icon,
            'actions' => collect($this->actions)->map(fn($action) => $action->toArrayForModel($model))->all(),
            'type' => 'group',
        ];
    }

    /**
     * Convert the group to an array.
     */
    public function toArray(): array
    {
        return [
            'icon' => $this->icon,
            'actions' => collect($this->actions)->map->toArray()->all(),
            'type' => 'group',
        ];
    }
}
