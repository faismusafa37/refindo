<?php

namespace App\Filament\Resources\PartDismantleResource\Pages;

use App\Filament\Resources\PartDismantleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartDismantle extends EditRecord
{
    protected static string $resource = PartDismantleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
