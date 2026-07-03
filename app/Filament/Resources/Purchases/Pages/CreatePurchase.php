<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\PurchaseService;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Filament\Resources\Products\ProductResource;


class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    public function mount(): void
    {

        $supplierId = request()->query('supplier_id');
        $productId = request()->query('product_id');
        $data = [];

        if ($supplierId) {
            $data['supplier_id'] = $supplierId;
        }

        if ($productId) {
            $product = \App\Models\Product::find($productId);

            $data['items'] = [
                [
                    'product_id' => $productId,
                    'unit_id' => $product ? $product->baseUnit->unit_id : null,
                    'quantity' => 1,
                    'price' => $product ? $product->baseUnit->cost_price : 0,
                ],
            ];
        }

        $this->form->fill($data);
    }
    protected function handleRecordCreation(array $data): Model
    {
        return app(PurchaseService::class)->create($data);
    }

    protected function afterCreate(): void
    {
        // Runs after the form fields are saved to the database.
        $user = auth()->user();
        foreach ($this->record->items as $item) {

            $product = $item->product;

            $lastPrice = $product->baseUnit->cost_price;

            $newPrice = $item->price;

            if (!$lastPrice) {
                continue;
            }

            if ((float) $lastPrice !== (float) $newPrice) {

                $diff = (($newPrice - $lastPrice) / $lastPrice) * 100;

                $recipient = auth()->user();
               
                    Notification::make()
                        ->title('Difference in Cost Price Detected')
                        ->body(
                            $product->name .
                            ' : Rp ' . number_format($lastPrice) .
                            ' → Rp ' . number_format($newPrice) .
                            ' (' . round($diff, 2) . '%)'
                        )
                        ->warning()
                        ->actions([
                            Action::make('view')
                                ->button()
                                ->markAsRead()
                                ->label('Update Product Price')
                                ->url(
                                    ProductResource::getUrl('price-review', [
                                        'record' => $product,
                                        'new_cost_price' => $newPrice,
                                    ]),
                                    shouldOpenInNewTab: true
                                ),
                            Action::make('markAsRead')
                                ->button()
                                ->markAsRead(),
                            Action::make('markAsUnread')
                                ->button()
                                ->markAsUnread(),
                        ])
                        ->send()
                        ->sendToDatabase($recipient);
            }
        }
    }

}
