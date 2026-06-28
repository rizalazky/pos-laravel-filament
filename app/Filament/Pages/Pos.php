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
use App\Filament\Resources\Sales\SaleResource;

class Pos extends Page
{
    protected string $view = 'filament.pages.pos';
    // protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $title = '';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'POS';

    public $barcode = '';
    public $cart = [];
    public $payment = 0;
    public $showPaymentModal = false;
    public $paymentFormatted = '';
    public string $searchMode = 'search';
    public $search = ''; // 📦 Khusus Scan Barcode / Cari Produk
    public $customerSearch = ''; // 👤 Variabel Baru Khusus Cari Pelanggan
    public $customerId;
    
    // 👤 State Tampilan Seleksi Pelanggan Aktif
    public string $selectedCustomerName = '';
    public string $selectedCustomerPhone = '';

    // 👤 State Tambahan untuk Tambah Pelanggan Baru
    public bool $isCustomerModalOpen = false;
    public string $newCustomerName = '';
    public string $newCustomerPhone = '';
    public string $newNumberPlate = '';
    public string $newVehicleType = '';
    
    public bool $isMobileCartOpen = false;
    public bool $isRewardModalOpen = false;
    public array $appliedRewards = []; 

    public bool $isSuccessModalOpen = false;
    public ?int $lastTransactionId = null;
    public bool $isPaymentModalOpen = false; 
    public string $paymentMethod = 'cash'; 
    public ?string $paymentReceived = ''; 
    public ?string $discount = '';

    public function getProductsProperty()
    {
        // Query dasar bawaan kamu
        $query = Product::where('is_active', true)
            ->where('stock', '>', 0);

        if ($this->searchMode === 'search' && !empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        return $query->take(40)->get();
    }

    public function updatedSearch()
    {
        if (empty($this->search)) return;

        // 🔥 PENGAMAN: Jika sedang MODE CARI, stop di sini (Jangan biarkan auto-masuk keranjang)
        if ($this->searchMode === 'search') return;

        // Logika cari produk otomatis untuk scan barcode kamu tetap sama di bawah ini
        $product = Product::where('is_active', true)
            ->where(function($q) {
                $q->where('sku', $this->search) 
                ->orWhere('name', $this->search); // Untuk scan, sebaiknya match persis
            })
            ->where('stock', '>', 0)
            ->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->search = ''; 
            
            Notification::make()
                ->title($product->name . ' berhasil discan')
                ->success()
                ->send();
        }
    }

    public function getTotalProperty()
    {
       
        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $totalVoucherDiscount = collect($this->appliedRewards)
            ->where('reward_type', 'discount')
            ->sum(fn($item) => $item['discount_value'] * $item['qty']);

        return max(0, $subtotal - (float) $this->cleanDiscount - $totalVoucherDiscount);
    }

    public function getCleanDiscountProperty(): int
    {
        if (! $this->discount) {
            return 0;
        }
        return (int) preg_replace('/[^0-9]/', '', $this->discount);
    }

    public function getUnderpaymentProperty()
    {
        return max(0, $this->total - $this->cleanPaymentReceived);
    }

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            Notification::make()->title('Keranjang masih kosong!')->danger()->send();
            return;
        }

        
        $this->paymentReceived = $this->total; 
        $this->isPaymentModalOpen = true;
    }

    
 
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

    public function addToCart($productId)
    {
        $product = Product::with('baseUnit')->find($productId);
        if (!$product) return;
        // dd($product);
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'key' => $product->id,
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->baseUnit->sell_price,
                'cost_price' => $product->baseUnit->cost_price,
                'qty' => 1,
            ];
        }
    }

    public function openCustomerModal()
    {
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
        $this->isCustomerModalOpen = true;
    }

    public function saveCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|min:3',
            'newCustomerPhone' => 'required',
        ], [
            'newCustomerName.required' => 'Nama pelanggan tidak boleh kosong.',
            'newCustomerName.min' => 'Nama minimal terdiri dari 3 karakter.',
            'newCustomerPhone.required' => 'No HP tidak boleh kosong.',
        ]);

        $newCustomer = \App\Models\Customer::create([
            'name' => $this->newCustomerName,
            'phone_number' => $this->newCustomerPhone,
            'points' => 0
        ]);


        // 👤 UX MAGIC: Langsung pasangkan data ke komponen seleksi aktif
        $this->customerId = $newCustomer->id;
        $this->selectedCustomerName = $newCustomer->name;
        $this->selectedCustomerPhone = $newCustomer->phone_number ?? 'No HP (-)';
        $this->customerSearch = '';
        
        $this->isCustomerModalOpen = false;

        Notification::make()
            ->title('Pelanggan Baru Berhasil Terdaftar! 👤')
            ->success()
            ->send();
    }

    public function switchSearchMode($mode)
    {
        $this->searchMode = $mode;
        $this->search = ''; 
    }

    public function getCustomerResultsProperty()
    {
        if (empty($this->customerSearch)) {
            return [];
        }

        return \App\Models\Customer::where('name', 'like', '%' . $this->customerSearch . '%')
            ->orWhere('phone_number', 'like', $this->customerSearch . '%')
            ->take(10) // Batasi 10 data teratas demi kecepatan loading POS
            ->get();
    }

    public function selectCustomer($id)
    {
        $customer = \App\Models\Customer::find($id);
        if ($customer) {
            $this->customerId = $customer->id;
            $this->selectedCustomerName = $customer->name;
            $this->selectedCustomerPhone = $customer->phone_number ?? 'No HP (-)';
            $this->customerSearch = ''; // Reset keyword pencarian
            
            $this->resetCustomerRewards();
        }
    }

    // 👤 Method jika user ingin membatalkan/mengganti customer yang sudah dipilih
    public function clearCustomer()
    {
        $this->customerId = null;
        $this->selectedCustomerName = '';
        $this->selectedCustomerPhone = '';
        $this->customerSearch = '';
        
        $this->resetCustomerRewards();
    }

    public function updatedCustomerId($value)
    {
        // Tetap dipertahankan untuk mengantisipasi dependensi internal Filament jika ada
        $this->resetCustomerRewards();
    }

    private function resetCustomerRewards()
    {
        foreach ($this->cart as $key => $item) {
            if (isset($item['is_reward_item']) && $item['is_reward_item'] === true) {
                unset($this->cart[$key]);
            }
        }
        $this->appliedRewards = [];
        if (property_exists($this, 'rewardDiscountAmount')) {
            $this->rewardDiscountAmount = 0;
        }
    }

    public function updateQty($key, $operator)
    {
        if ($operator === '+') {
            $this->cart[$key]['qty']++;
        } else {
            $this->cart[$key]['qty']--;
            if ($this->cart[$key]['qty'] <= 0) {
                unset($this->cart[$key]);
            }
        }
    }

    public function updatePrice($key, $price, $layout ='desktop')
    {
        $price = (float) preg_replace('/[^0-9]/', '', $price);
        $this->cart[$key]['price'] = $price;
        
        $this->dispatch('close-modal', id: 'edit-price-'.$layout.'-'.$key);   
    }

    public function getCleanPaymentReceivedProperty(): int
    {
        if (! $this->paymentReceived) {
            return 0;
        }
        return (int) preg_replace('/[^0-9]/', '', $this->paymentReceived);
    }

    public function saveTransaction()
    {
        if (empty($this->cart)) {
            Notification::make()->title('Keranjang masih kosong!')->danger()->send();
            return;
        }

        if($this->underpayment > 0 && auth()->user()->cannot('multiplePaymentFeature', App\Models\Transaction::class)){
            Notification::make()->title('Total bayar tidak boleh kurang dari total belanja!')->danger()->send();
            return;
        }

        $paymentStatus = 'unpaid';

        try {
            $totalVoucherDiscount = collect($this->appliedRewards)
                ->where('reward_type', 'discount')
                ->sum(fn($item) => $item['discount_value'] * $item['qty']);
            $payload = [
                'outlet_id'    => auth()->user()->current_outlet_id,
                'type'         => 'out', 
                'total_price'  => collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']),
                'discount'     => (int) $this->cleanDiscount + $totalVoucherDiscount,
                'grand_total'  => (int) $this->total,
                'notes'        => 'Transaksi via Aplikasi POS Kustom',
                'customer_id'  => $this->customerId,
                'applied_rewards'   => $this->appliedRewards,
                'items'      => collect($this->cart)->map(function ($item) {
                    $product = \App\Models\Product::with('baseUnit')->find($item['id']);
                    return [
                        'product_id' => $item['id'],
                        'cost_price' => $item['cost_price'],
                        'price'      => $item['price'],
                        'quantity'   => $item['qty'],
                        'unit_id'    => $product->baseUnit->unit_id
                    ];
                })->toArray(),

                'payment_method'   => $this->paymentMethod,
                'payment_status'   => $paymentStatus,
                'total_paid'       => (int) $this->cleanPaymentReceived - (float) $this->change,
            ];

            $saleService = app(SaleService::class);
            $transaction = $saleService->create($payload);

            $this->lastTransactionId = $transaction->id; 
            $this->isPaymentModalOpen = false;           
            $this->isMobileCartOpen = false;
            $this->isSuccessModalOpen = true;

        } catch (\Exception $e) {
            dd($e);
            Notification::make()
                ->title('Gagal Menyimpan Transaksi')
                ->description($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function startNewTransaction()
    {
        $this->reset([
            'cart',
            'customerId',
            'selectedCustomerName',
            'selectedCustomerPhone',
            'customerSearch',
            'discount',
            'paymentReceived',
            'paymentMethod',
            'lastTransactionId',
            'isSuccessModalOpen'
        ]);

        $this->search = '';

        \Filament\Notifications\Notification::make()
            ->title('Siap menerima transaksi baru! 🚀')
            ->success()
            ->send();
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }



    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
    }

    public function getChangeProperty()
    {
       return max(0, $this->cleanPaymentReceived - $this->total);
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

        $sale = app(SaleService::class)->create($data);

        // Reset POS
        $this->cart = [];
        $this->payment = 0;
        $this->showPaymentModal = false;

        // $this->dispatch('notify', type: 'success', message: 'Transaction success!');
        return redirect(
            SaleResource::getUrl('receipt', ['record' => $sale->id])
        );
    }
}
