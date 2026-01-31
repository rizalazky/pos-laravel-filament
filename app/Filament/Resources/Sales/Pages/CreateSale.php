<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\SaleService;
use Illuminate\Database\Eloquent\Model;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        return app(SaleService::class)->create($data);
    }

    public function playSound(string $sound){
        $this->dispatchBrowserEvent('play-sound', [
            'type' => $sound,
        ]);
    }
}
