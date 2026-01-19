<?php

namespace Arkhas\LivewireDatatable\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;

class ColumnAction
{
    protected string $name;
    protected string|Closure|null $label = null;
    protected string|Closure|null $icon = null;
    protected string|Closure|null $url = null;
    protected array $props = [];
    protected bool $separator = false;
    protected ?Closure $handleCallback = null;
    protected ?Closure $confirmCallback = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a new column action instance.
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
    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the action label for a model.
     */
    public function getLabel(?Model $model = null): string
    {
        if ($this->label instanceof Closure && $model) {
            return call_user_func($this->label, $model);
        }

        return $this->label ?? ucfirst(str_replace('_', ' ', $this->name));
    }

    /**
     * Set the action icon.
     */
    public function icon(string|Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the action icon for a model.
     */
    public function getIcon(?Model $model = null): ?string
    {
        if ($this->icon instanceof Closure && $model) {
            return call_user_func($this->icon, $model);
        }

        return $this->icon;
    }

    /**
     * Set the URL callback or string.
     */
    public function url(string|Closure $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the URL for a model.
     */
    public function getUrl(?Model $model = null): ?string
    {
        if ($this->url instanceof Closure && $model) {
            return call_user_func($this->url, $model);
        }

        return $this->url;
    }

    /**
     * Check if this action has a URL.
     */
    public function hasUrl(): bool
    {
        return $this->url !== null;
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
     * Add a separator after this action.
     */
    public function separator(bool $separator = true): static
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * Check if separator should be shown after this action.
     */
    public function hasSeparator(): bool
    {
        return $this->separator;
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
     * Execute the action on a model.
     */
    public function execute(Model $model): array
    {
        if ($this->handleCallback) {
            return call_user_func($this->handleCallback, $model);
        }

        return ['success' => false, 'message' => 'No handler defined'];
    }

    /**
     * Check if the action has a handler.
     */
    public function hasHandler(): bool
    {
        return $this->handleCallback !== null;
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
     * Get the confirmation data for a model.
     */
    public function getConfirmation(Model $model): ?array
    {
        if ($this->confirmCallback) {
            return call_user_func($this->confirmCallback, $model);
        }

        return null;
    }

    /**
     * Convert the action to an array for a specific model.
     */
    public function toArrayForModel(Model $model): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel($model),
            'icon' => $this->getIcon($model),
            'url' => $this->getUrl($model),
            'props' => $this->props,
            'separator' => $this->separator,
            'hasHandler' => $this->hasHandler(),
            'requiresConfirmation' => $this->requiresConfirmation(),
            'type' => 'action',
        ];
    }

    /**
     * Convert the action to an array (without model context).
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => is_string($this->label) ? $this->label : $this->name,
            'icon' => is_string($this->icon) ? $this->icon : null,
            'url' => is_string($this->url) ? $this->url : null,
            'props' => $this->props,
            'separator' => $this->separator,
            'hasHandler' => $this->hasHandler(),
            'requiresConfirmation' => $this->requiresConfirmation(),
            'type' => 'action',
        ];
    }
}
