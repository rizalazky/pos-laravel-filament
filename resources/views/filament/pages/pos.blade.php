<x-filament-panels::page>
    <div class="grid grid-cols-3 gap-4">

        {{-- ================= LEFT SIDE ================= --}}
        <div class="col-span-2 bg-white p-4 rounded shadow space-y-4">

            {{-- BARCODE SCAN --}}
            <form wire:submit.prevent="scanBarcode">
                <input
                    type="text"
                    wire:model.live="barcode"
                    autofocus
                    placeholder="Scan barcode here..."
                    class="w-full border rounded p-3 text-lg"
                />
            </form>

            {{-- PRODUCT LIST --}}
            <div class="grid grid-cols-4 gap-3">
                @foreach(\App\Models\Product::with('baseUnit')->limit(20)->get() as $product)
                    <button
                        wire:click="addProduct({{ $product->id }})"
                        class="border rounded p-3 text-left hover:bg-gray-100"
                    >
                        <div class="font-semibold">
                            {{ $product->name }}
                        </div>
                        <div class="text-sm text-gray-500">
                            Rp {{ number_format($product->baseUnit->sell_price) }}
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ================= RIGHT SIDE (CART) ================= --}}
        <div class="bg-white p-4 rounded shadow flex flex-col">

            <div class="flex-1 overflow-y-auto space-y-3">

                @forelse($cart as $id => $item)
                    <div class="border rounded p-3">
                        <div class="flex justify-between">
                            <div>
                                <div class="font-semibold">
                                    {{ $item['name'] }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Rp {{ number_format($item['sell_price']) }}
                                </div>
                            </div>

                            <button
                                wire:click="removeItem({{ $id }})"
                                class="text-red-500"
                            >x</button>
                        </div>

                        <div class="flex items-center gap-3 mt-2">
                            <button wire:click="decreaseQty({{ $id }})">-</button>
                            <span>{{ $item['qty'] }}</span>
                            <button wire:click="increaseQty({{ $id }})">+</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400">
                        Cart is empty
                    </div>
                @endforelse

            </div>

            {{-- PROCESS PAYMENT BUTTON --}}
            <button
                wire:click="openPaymentModal"
                class="mt-4 w-full bg-primary-600 text-white p-3 rounded text-lg font-semibold"
            >
                PROCESS PAYMENT
                <br>
                Rp {{ number_format($this->subtotal) }}
            </button>
        </div>
    </div>

    {{-- ================= PAYMENT MODAL ================= --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-black bg-opacity-20 flex items-center justify-center">
            <div class="bg-white p-6 rounded shadow w-96 space-y-4">

                <h2 class="text-lg font-bold">Payment</h2>

                <div>
                    <div class="text-sm text-gray-500">Subtotal</div>
                    <div class="text-xl font-semibold">
                        Rp {{ number_format($this->subtotal) }}
                    </div>
                </div>

                <input
                    type="number"
                    wire:model.lazy="payment"
                    placeholder="Input payment amount"
                    class="w-full border rounded p-2"
                />

                <div>
                    <div class="text-sm text-gray-500">Change</div>
                    <div class="text-lg font-bold text-green-600">
                        Rp {{ number_format($this->change) }}
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        wire:click="closePaymentModal"
                        class="px-4 py-2 border rounded"
                    >
                        Cancel
                    </button>

                    <button
                        class="px-4 py-2 bg-primary-600 text-white rounded"
                        wire:click="processPayment"
                    >
                        Confirm Payment
                    </button>
                </div>

            </div>
        </div>
    @endif

</x-filament-panels::page>
