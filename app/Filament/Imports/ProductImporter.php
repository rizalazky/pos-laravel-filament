<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Category; // Import model Category
use App\Models\Unit;     // Import model Unit
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            // Kolom Kategori (Biar diproses manual di bawah, pakai fillRecordUsing)
            ImportColumn::make('category')
                ->requiredMapping()
                ->fillRecordUsing(fn () => null)
                ->rules(['required']),
                
            // Kolom Unit
            ImportColumn::make('unit')
                ->requiredMapping()
                ->fillRecordUsing(fn () => null)
                ->rules(['required']),

            ImportColumn::make('cost_price')
                ->requiredMapping()
                ->fillRecordUsing(fn () => null),

            ImportColumn::make('sell_price')
                ->requiredMapping()
                ->fillRecordUsing(fn () => null),
        ];
    }

    public function resolveRecord(): Product
    {
        // 1. Cek Kategori: Jika nama sudah ada, ambil. Jika belum ada, buat baru otomatis.
        $category = Category::firstOrCreate([
            'name' => trim($this->data['category']),
        ]);

        // 2. Cari atau buat instance Product berdasarkan SKU
        $product = Product::firstOrNew([
            'sku' => $this->data['sku'],
        ]);

        // 3. Pasangkan category_id yang didapat dari langkah 1 ke produk ini
        $product->category_id = $category->id;

        return $product;
    }

    protected function afterSave(): void
    {
        // 4. Cek Unit: Jika nama sudah ada, ambil. Jika belum ada, buat baru otomatis.
        $unit = Unit::firstOrCreate([
            'name' => trim($this->data['unit']),
        ]);

        // 5. Simpan ke tabel product_units
        $this->record->units()->updateOrCreate(
            [
                'unit_id' => $unit->id,
            ],
            [
                'cost_price'      => $this->data['cost_price'],
                'sell_price'      => $this->data['sell_price'],
                'conversion_rate' => 1,
                'is_base'         => true,
                'is_default'      => true,
                'is_active'       => true,
            ]
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}