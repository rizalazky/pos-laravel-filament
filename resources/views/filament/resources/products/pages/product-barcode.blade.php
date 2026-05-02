<x-filament-panels::page>
    <div class="grid grid-cols-3 gap-4">

        @foreach($products as $product)
            <div class="border p-2 text-center flex flex-col justify-center items-center">

                <div class="text-sm">
                    {{ $product->name }}
                </div>

                <div class="w-full overflow-hidden flex justify-center">
                    <div class="inline-block origin-center scale-x-75">
                        {!! \App\Services\BarcodeService::generate($product->sku) !!}
                    </div>
                </div>

                <div class="text-xs">
                    {{ $product->sku }}
                </div>

                <div class="font-bold">
                    Rp {{ number_format($product->baseUnit->sell_price ?? 0) }}
                </div>

            </div>
        @endforeach

    </div>

    <div class="mt-4">
        <form method="POST" action="{{ route('print.barcode') }}">
            @csrf

            @foreach($products as $product)
                <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
            @endforeach

            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded">
                Print PDF
            </button>
        </form>
    </div>
</x-filament-panels::page>
