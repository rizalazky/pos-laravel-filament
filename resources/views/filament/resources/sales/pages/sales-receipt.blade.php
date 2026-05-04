<x-filament-panels::page>
    <div class="mb-4 flex justify-end">
        <form method="POST" action="{{ route('print.receipt') }}">
            @csrf
            <input type="hidden" name="sale_id" value="{{ $record->id }}">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded">
                Print PDF
            </button>
            <button 
                type="button" 
                onclick="window.history.back()" 
                class="px-4 py-2 bg-gray-500 text-white rounded"
            >
                Back
            </button>
        </form>
    </div>
    <div class="w-full flex justify-center items-center">
        <div id="print-area" class="text-sm" style="width:116mm; font-family: monospace;">
    
            <div class="text-center">
                <strong>My Store</strong><br>
                Jl. Contoh No.1
            </div>
    
            <div class="border-t border-dashed my-2"></div>
    
            <div>
                Invoice: {{ $record->invoice_number }}<br>
                Date: {{ \Carbon\Carbon::parse($record->date)->format('d/m/Y H:i') }}
            </div>
    
            <div class="border-t border-dashed my-2"></div>
    
            @foreach($record->items as $item)
                <div>
                    {{ $item->product->name }}
                </div>
                <div class="flex justify-between">
                    <span>{{ $item->quantity }} x {{ number_format($item->price) }}</span>
                    <span>{{ number_format($item->subtotal) }}</span>
                </div>
            @endforeach
    
            <div class="border-t border-dashed my-2"></div>
    
            <div class="flex justify-between font-bold">
                <span>Total</span>
                <span>{{ number_format($record->total) }}</span>
            </div>
    
            <div class="border-t border-dashed my-2"></div>
    
            <div class="text-center">
                Terima kasih 🙏
            </div>
    
        </div>
    </div>

    
</x-filament-panels::page>
