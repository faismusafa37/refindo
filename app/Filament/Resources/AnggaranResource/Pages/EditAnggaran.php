<?php

namespace App\Filament\Resources\AnggaranResource\Pages;

use App\Filament\Resources\AnggaranResource;
use Filament\Resources\Pages\EditRecord;

class EditAnggaran extends EditRecord
{
    protected static string $resource = AnggaranResource::class;

    // Override method saved() untuk memberi pemberitahuan
    protected function saved(): void
    {
        parent::saved();
        
        // Menampilkan pemberitahuan setelah update berhasil
        $this->notify('success', 'Anggaran berhasil diperbarui!');
    }
}
