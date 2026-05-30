<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\Page;
use App\Models\Product;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\RawJs;
use Filament\Actions;

class ReviewPriceUpdate extends Page implements HasForms
{
    use InteractsWithRecord;
    use InteractsWithForms;
    protected static string $resource = ProductResource::class;
    public $newCostPrice = null;
    public ?array $data = [];

    protected string $view = 'filament.resources.products.pages.review-price-update';
    

    public function mount($record): void
    {
        $this->record = Product::findOrFail($record);

        $this->newCostPrice = request('new_cost_price');

        $this->form->fill([
            'latest_cost_price' =>
                $this->record->baseUnit->cost_price,
            'new_cost_price' =>
                $this->newCostPrice,
            'latest_selling_price' =>
                $this->record->baseUnit->sell_price,

        ]);
    }

    public function form(Schema $schema): Schema
    {
       
        return $schema->statePath('data')->components([
            TextInput::make('latest_cost_price')
                ->label('Latest Cost Price')
                ->prefix('Rp')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->numeric()
                ->readonly()
                ->required(),
            TextInput::make('new_cost_price')
                ->label('New Cost Price')
                ->prefix('Rp')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->numeric()
                ->readonly(),
            TextInput::make('latest_selling_price')
                ->label('Latest Sell Price')
                ->prefix('Rp')
                ->numeric()
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->readonly()
                ->required(),
            TextInput::make('sell_price')
                ->label('New Sell Price')
                ->prefix('Rp')
                ->numeric()
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->required(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->action(function(): void{
                    $data = $this->form->getState();
                    
                    $this->record->baseUnit->update([
                        'cost_price' => $data['new_cost_price'],
                        'sell_price' => $data['sell_price'],

                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Price Updated Successfully')
                        ->success()
                        ->send();
                })
        ];
    }
}
