<?php

namespace Arkhas\LivewireDatatable\Actions;

use Closure;

class TableAction
{
    protected string $name;
    protected ?string $label = null;
    protected ?string $icon = null;
    protected string $iconPosition = 'left';
    protected array $props = [];
    protected ?string $styles = null;
    protected ?Closure $handleCallback = null;
    protected ?Closure $confirmCallback = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a new table action instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Get the action name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the action label.
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the action label.
     */
    public function getLabel(): string
    {
        return $this->label ?? ucfirst(str_replace('_', ' ', $this->name));
    }

    /**
     * Set the action icon.
     */
    public function icon(string $icon, string $position = 'left'): static
    {
        $this->icon = $icon;
        $this->iconPosition = $position;

        return $this;
    }

    /**
     * Get the action icon.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Get the icon position.
     */
    public function getIconPosition(): string
    {
        return $this->iconPosition;
    }

    /**
     * Set the button props (variant, size, etc).
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
     * Set the handle callback.
     */
    public function handle(Closure $callback): static
    {
        $this->handleCallback = $callback;

        return $this;
    }

    /**
     * Execute the action.
     */
    public function execute(array $ids): array
    {
        if ($this->handleCallback) {
            return call_user_func($this->handleCallback, $ids);
        }

        return ['success' => false, 'message' => 'No handler defined'];
    }

    /**
     * Set the confirmation callback.
     */
    public function confirm(Closure $callback): static
    {
        $this->confirmCallback = $callback;

        return $this;
    }

    /**
     * Check if confirmation is required.
     */
    public function requiresConfirmation(): bool
    {
        return $this->confirmCallback !== null;
    }

    /**
     * Get the confirmation data.
     */
    public function getConfirmation(array $ids): ?array
    {
        if ($this->confirmCallback) {
            return call_user_func($this->confirmCallback, $ids);
        }

        return null;
    }

    /**
     * Convert the action to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'icon' => $this->icon,
            'iconPosition' => $this->iconPosition,
            'props' => $this->props,
            'styles' => $this->styles,
            'requiresConfirmation' => $this->requiresConfirmation(),
            'type' => 'action',
        ];
    }
}
