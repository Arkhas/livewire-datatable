<?php

namespace Arkhas\LivewireDatatable\Actions;

class TableActionGroup
{
    protected string $name;
    protected ?string $label = null;
    protected ?string $icon = null;
    protected array $props = [];
    protected ?string $styles = null;
    protected array $actions = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a new table action group instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Get the group name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the group label.
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the group label.
     */
    public function getLabel(): string
    {
        return $this->label ?? ucfirst(str_replace('_', ' ', $this->name));
    }

    /**
     * Set the group icon.
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the group icon.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Set the button props.
     */
    public function props(array $props): static
    {
        $this->props = $props;

        return $this;
    }

    /**
     * Get the button props.
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * Set custom styles.
     */
    public function styles(string $styles): static
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * Get custom styles.
     */
    public function getStyles(): ?string
    {
        return $this->styles;
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
    public function getAction(string $name): ?TableAction
    {
        foreach ($this->actions as $action) {
            if ($action->getName() === $name) {
                return $action;
            }
        }

        return null;
    }

    /**
     * Execute an action by name.
     */
    public function execute(array $ids): array
    {
        return ['success' => false, 'message' => 'Cannot execute action group directly'];
    }

    /**
     * Check if confirmation is required (groups don't require confirmation directly).
     */
    public function requiresConfirmation(): bool
    {
        return false;
    }

    /**
     * Convert the group to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'icon' => $this->icon,
            'props' => $this->props,
            'styles' => $this->styles,
            'actions' => collect($this->actions)->map->toArray()->all(),
            'type' => 'group',
        ];
    }
}
