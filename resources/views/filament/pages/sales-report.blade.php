<x-filament-panels::page>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <x-filament::card>
            <h2>Total Sales</h2>
            <p>Rp {{ number_format($this->totalSales, 0, ',', '.') }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2>Total Transactions</h2>
            <p>{{ $this->totalTransactions }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2>Total Qty Sold</h2>
            <p>{{ number_format($this->totalQuantity, 2) }}</p>
        </x-filament::card>
    </div>

    <div class="mb-6">
        @livewire(\App\Filament\Widgets\DailySalesChart::class)
    </div>


    <div class="mb-6">
        <h2 class="text-lg font-bold mb-2">Top 5 Products</h2>

        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->topProducts as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->total_qty,2) }}</td>
                        <td>Rp {{ number_format($item->total_revenue,0,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <h2 class="text-lg font-bold mb-2">Sales by Cashier</h2>

        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th>Cashier</th>
                    <th>Transaction</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->salesByCashier as $cashier)
                    <tr>
                        <td>{{ $cashier->user->name ?? '-' }}</td>
                        <td>{{ $cashier->total_transaction }}</td>
                        <td>Rp {{ number_format($cashier->total_sales,0,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>



</x-filament-panels::page>
