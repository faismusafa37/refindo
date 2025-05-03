<?php

namespace App\Filament\Resources\PartDismantleResource\Pages;

use App\Filament\Resources\PartDismantleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPartDismantle extends EditRecord
{
    protected static string $resource = PartDismantleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Aksi Delete hanya terlihat oleh Admin
            Actions\DeleteAction::make()
            ->hidden(fn () => Auth::user()->hasRole('user')),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
