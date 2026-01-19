<?php

namespace Arkhas\LivewireDatatable\Table\Concerns;

use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Actions\TableActionGroup;

trait HasActions
{
    protected array $actions = [];

    /**
     * Set the bulk actions for this table.
     */
    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get all bulk actions.
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get a bulk action by name.
     */
    public function getAction(string $name): TableAction|TableActionGroup|null
    {
        foreach ($this->actions as $action) {
            if ($action->getName() === $name) {
                return $action;
            }

            // Check in action groups
            if ($action instanceof TableActionGroup) {
                $found = $action->getAction($name);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Execute a bulk action.
     */
    public function executeAction(string $name, array $ids): array
    {
        $action = $this->getAction($name);

        if (!$action) {
            return ['success' => false, 'message' => 'Action not found'];
        }

        return $action->execute($ids);
    }
}
