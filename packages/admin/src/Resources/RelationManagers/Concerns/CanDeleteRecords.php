<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait CanDeleteRecords
{
    protected function canDelete(Model $record): bool
    {
        return $this->can('delete', $record);
    }

    protected function canDeleteAny(): bool
    {
        return $this->can('deleteAny');
    }

    protected function delete(): void
    {
        $this->callHook('beforeDelete');

        $this->getMountedTableActionRecord()->delete();

        $this->callHook('afterDelete');
    }

    protected function getDeleteLinkTableAction(): Tables\Actions\LinkAction
    {
        return Tables\Actions\LinkAction::make('delete')
            ->label('Delete')
            ->requiresConfirmation()
            ->action(fn () => $this->delete())
            ->color('danger')
            ->hidden(fn (Model $record): bool => ! static::canDelete($record));
    }

    protected function getDeleteTableBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('delete')
            ->label('Delete selected')
            ->action(fn (Collection $records) => $records->each->delete())
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion()
            ->color('danger')
            ->icon('heroicon-o-trash');
    }
}
