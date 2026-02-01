<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Product;
use App\Models\ProductUnit;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Hidden;
use Filament\Actions\Action;


class SaleForm
{
    // public function playSound($sound){
    //     $this->dispatchBrowserEvent()
    // }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                // TextInput::make('sound')
                //     ->reactive()
                //     ->live()
                //     ->extraAttributes([
                //         'x-on:input' => "
                //             if (\$el.value) {
                //             console.log('test')
                //                 new Audio('/sounds/' + \$el.value + '.wav').play()
                //             }
                //         ",
                //     ]),
                DatePicker::make('date')
                ->required()
                ->default(now()),

                TextInput::make('invoice_number')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('note')
                    ->columnSpanFull(),

                TextInput::make('sku')
                    ->label('Barcode Scan')
                    ->autofocus()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {

                        if (! $state) {
                            return;
                        }

                        $product = Product::where('sku', $state)->first();

                        if (! $product) {
                            // reset sku kalau ga ketemu
                            $set('sku', null);
                            Notification::make()
                                ->title('Produk tidak ditemukan')
                                ->danger()
                                ->send();
                            // $set('sound','error');
                            return;
                        }

                        $items = $get('items') ?? [];

                        // cari apakah produk sudah ada di items
                        $index = collect($items)->search(fn ($item) =>
                            $item['product_id'] == $product->id
                        );

                        // ambil unit default (base / smallest)
                        $productUnit = ProductUnit::where('product_id', $product->id)
                            ->orderBy('conversion_rate') // paling kecil
                            ->first();

                        if (! $productUnit) {
                            return;
                        }
                        $availableStock = floor(
                            $product->stock / $productUnit->conversion_rate
                        );

                        if ($index !== false) {
                            // sudah ada → qty +1
                             if ($items[$index]['quantity'] + 1 > $availableStock) {
                                Notification::make()
                                    ->title('Stok tidak mencukupi')
                                    ->danger()
                                    ->send();

                                $set('sku', null);
                                return;
                            }
                            $items[$index]['quantity'] += 1;
                        } else {
                            if ($availableStock < 1) {
                                Notification::make()
                                    ->title('Stok tidak mencukupi')
                                    ->danger()
                                    ->send();

                                $set('sku', null);
                                return;
                            }
                            // belum ada → push baru
                            $items[] = [
                                'product_id' => $product->id,
                                'unit_id'    => $productUnit->unit_id,
                                'quantity'   => 1,
                                'stock'   => $availableStock,
                                'price'      => $productUnit->sell_price ?? 0,
                            ];
                        }

                        $set('items', $items);
                        $set('sku', null); // fokus balik ke scan
                    })
                    ->extraInputAttributes([
                        'inputmode' => 'none',
                    ])
                    ->hint('Scan Product'),

                Placeholder::make('total')
                    ->disabled()
                    ->dehydrated(false)
                    ->reactive()
                    ->live(onBlur : false)
                    ->formatStateUsing(function (Get $get) {
                        return collect($get('items') ?? [])
                            ->sum(fn ($item) =>
                                ($item['price'] ?? 0) * ($item['quantity'] ?? 0)
                            );
                    }),
                
                Repeater::make('items')
                    ->table([
                        TableColumn::make('Product'),
                        TableColumn::make('Unit'),
                        TableColumn::make('Quantity'),
                        TableColumn::make('Price'),
                        TableColumn::make('Subtotal'),
                        TableColumn::make('Actions'),
                    ])
                    // ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->options(Product::pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn ($set) => $set('unit_id', null))
                            ->required(),

                        Select::make('unit_id')
                            ->options(function (Get $get) {
                                $productId = $get('product_id');

                                if (! $productId) {
                                    return [];
                                }

                                return ProductUnit::where('product_id', $productId)
                                    ->with('unit')
                                    ->get()
                                    ->pluck('unit.name', 'unit.id')
                                    ->toArray();
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if (! $state) {
                                    return;
                                }

                                $productId = $get('product_id');

                                $product = Product::find($productId);

                                $productUnit = ProductUnit::where('product_id', $productId)
                                    ->where('unit_id', $state)
                                    ->first();

                                if (! $product || ! $productUnit) {
                                    return;
                                }

                                // set harga jual
                                $set('price', $productUnit->sell_price ?? 0);

                                // 🔥 stok tersedia dalam unit terpilih
                                $availableStock = floor(
                                    $product->stock / $productUnit->conversion_rate
                                );

                                $set('stock', $availableStock);
                            })
                            ->disabled(fn (Get $get) => ! $get('product_id'))
                            ->required(),


                        Hidden::make('stock')
                            ->live()
                            ->reactive(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->reactive()
                            ->live(onBlur: false)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {

                                $stock = $get('stock');

                                if (! $state || $stock === null) {
                                    return;
                                }

                                if ($state > $stock) {
                                    Notification::make()
                                        ->title('Stok tidak mencukupi')
                                        ->danger()
                                        ->send();

                                    // rollback ke max stok
                                    $set('quantity', $stock);
                                }
                            })
                            ->required(),


                        TextInput::make('price')
                            // ->mask(RawJs::make('$money($input)'))
                            ->numeric()
                            ->live(onBlur: false)
                            ->reactive()
                            ->required(),

                        Placeholder::make('subtotal') // currency format
                            ->content(fn ($get) =>
                                ($get('quantity') ?? 0) * ($get('price') ?? 0)
                            ),
                    ])
                    ->defaultItems(0)
                    ->columns(5)
                    ->columnSpanFull()
                    ->required()
            ]);
    }
}
