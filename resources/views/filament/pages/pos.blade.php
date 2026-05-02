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

    <x-filament::modal id="edit-user">
        <x-slot name="heading">
            Payment
        </x-slot>

        <label class="text-sm font-medium text-gray-700">
            Subtotal
        </label>
        <x-filament::input.wrapper>
            <x-slot name="label">
                Payment
            </x-slot>
            <x-slot name="prefix">
               Rp
            </x-slot>
            <x-filament::input
                type="text"
                :value="number_format($this->subtotal)"
                readonly
                label="Subtotal"
            />
        </x-filament::input.wrapper>
        <label class="text-sm font-medium text-gray-700">
            Payment
        </label>
        <x-filament::input.wrapper>
            <x-slot name="prefix">
                Rp
            </x-slot>

            <x-filament::input
                type="text"
                wire:model.live="paymentFormatted"
                placeholder="Input payment amount"
            />
        </x-filament::input.wrapper>
        <label class="text-sm font-medium text-gray-700">
            Change
        </label>
        <x-filament::input.wrapper>
            
            <x-slot name="prefix">
               Rp
            </x-slot>
            <x-filament::input
                type="text"
                :value="number_format($this->change)"
                readonly
            />
        </x-filament::input.wrapper>

        <x-slot name="footerActions">
            <div class="flex justify-end">
                <x-filament::button wire:click="processPayment">
                    Confirm Payment
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>


</x-filament-panels::page>
