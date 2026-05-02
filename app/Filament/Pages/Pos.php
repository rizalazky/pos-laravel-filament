<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use App\Models\Product;
use App\Services\SaleService;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class Pos extends Page
{
    protected string $view = 'filament.pages.pos';
    // protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $title = 'POS';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'POS';

    public $barcode = '';
    public $cart = [];
    public $payment = 0;
    public $showPaymentModal = false;
    public $paymentFormatted = '';

    public function updatedPaymentFormatted($value)
    {
        // ambil angka saja
        $numeric = preg_replace('/[^0-9]/', '', $value);

        $this->payment = (int) $numeric;

        // format ulang ke rupiah
        $this->paymentFormatted = $numeric
            ? number_format($numeric)
            : '';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public function addProduct($productId)
    {
        $product = Product::with('baseUnit')->find($productId);

        if (!$product) return;
        if ($product->stock <= 0) {
            Notification::make()
                ->title('Stock tidak mencukupi')
                ->danger()
                ->send();
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'name' => $product->name,
                'sell_price' => $product->baseUnit->sell_price,
                'qty' => 1,
                'stock' => $product->stock,
            ];
        }
    }

    public function scanBarcode()
    {
        $product = Product::where('sku', $this->barcode)->first();

        if ($product) {
            $this->addProduct($product->id);
        }

        $this->barcode = '';
    }

    public function increaseQty($id)
    {
        if ($this->cart[$id]['stock'] < $this->cart[$id]['qty'] + 1) {
            Notification::make()
                ->title('Stock tidak mencukupi')
                ->danger()
                ->send();
            return;
        }
        $this->cart[$id]['qty']++;
    }

    public function decreaseQty($id)
    {
        if ($this->cart[$id]['qty'] > 1) {
            $this->cart[$id]['qty']--;
        }
    }

    public function removeItem($id)
    {
        unset($this->cart[$id]);
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['sell_price'] * $item['qty']);
    }

    public function getChangeProperty()
    {
        return (float) $this->payment - $this->subtotal;
    }

    public function openPaymentModal()
    {
        if ($this->subtotal > 0) {
            $this->dispatch('open-modal', id: 'edit-user');
        }
    }

    public function closePaymentModal()
    {
        $this->dispatch('hide-modal', id: 'edit-user');
    }

    public function processPayment()
    {
        if (empty($this->cart)) {
            return;
        }

        if ($this->payment < $this->subtotal) {
            return;
        }

        $items = collect($this->cart)->map(function ($item, $productId) {
            $product = \App\Models\Product::with('baseUnit')->find($productId);
            // dd($product);
            return [
                'product_id' => $productId,
                'unit_id'    => $product->baseUnit->unit_id,
                'quantity'   => $item['qty'],
                'price'      => $item['sell_price'],
            ];
        })->values()->toArray();

        $data = [
            'date'           => now(),
            'invoice_number' => 'INV-' . now()->format('YmdHis'),
            'customer_id'    => null,
            'note'           => null,
            'items'          => $items,
        ];

        app(SaleService::class)->create($data);

        // Reset POS
        $this->cart = [];
        $this->payment = 0;
        $this->showPaymentModal = false;

        $this->dispatch('notify', type: 'success', message: 'Transaction success!');
    }
}
