<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;

trait CanCreateRecords
{
    protected function canCreate(): bool
    {
        return $this->can('create');
    }

    protected function getCreateFormSchema(): array
    {
        return $this->getResourceForm()->getSchema();
    }

    protected function fillCreateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeCreateFill');

        $this->getMountedTableActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterCreateFill');
    }

    protected function create(): void
    {
        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $this->getMountedTableActionForm()->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $this->callHook('beforeCreate');

        $record = $this->getRelationship()->create($data);
        $this->getMountedTableActionForm()->model($record)->saveRelationships();

        $this->callHook('afterCreate');
    }

    protected function getCreateButtonTableHeaderAction(): Tables\Actions\ButtonAction
    {
        return Tables\Actions\ButtonAction::make('create')
            ->label('Create')
            ->form($this->getCreateFormSchema())
            ->mountUsing(fn () => $this->fillCreateForm())
            ->modalButton('Create')
            ->action(fn () => $this->create());
    }
}
