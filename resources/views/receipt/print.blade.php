<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: monospace;
            font-size: 10px;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
    </style>
</head>
<body>

<div class="center">
    <strong>{{ $company->name }}</strong><br>
    {{ $company->address }}<br>
    {{ $company->phone }}
</div>

<div class="line"></div>

<div>
    Invoice: {{ $sale->invoice_number }}<br>
    Date: {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y H:i') }}
</div>

<div class="line"></div>
@if($sale->customer)
    <div>
        <span>Cust: {{ $sale->customer->name }}</span>
    </div>
@else
    <div>
        <span>Cust: Pelanggan Umum</span>
    </div>
@endif
<div class="line"></div>

@foreach($sale->items as $item)
    <div>{{ $item->product->name }}</div>
    <div>
        {{ (int) $item->quantity }} x {{ number_format($item->price) }}
        <span style="float:right">
            {{ number_format($item->subtotal) }}
        </span>
    </div>
@endforeach

<div class="line"></div>
    <table style='width:100%;'>
        <tr>
            <td>Subtotal</td>
            <td class="right">Rp {{ number_format($sale->total) }}</td>
        </tr>
        @if($sale->discount > 0)
            <tr>
                <td>Diskon</td>
                <td class="right">-Rp {{ number_format($sale->discount) }}</td>
            </tr>
        @endif

        
        <tr class="font-bold">
            <td>TOTAL</td>
            <td class="right">Rp {{ number_format($sale->grand_total) }}</td>
        </tr>
        
        @php
            $totalBayar = $sale->total_payment;
            $kembalian = $totalBayar - $sale->grand_total;
        @endphp
        <tr>
            <td>Bayar (Cash)</td>
            <td class="right">Rp {{ number_format($totalBayar) }}</td>
        </tr>
        
        @if($kembalian > 0)
            <tr>
                <td>Kembalian</td>
                <td class="right">Rp {{ number_format($kembalian) }}</td>
            </tr>
        @elseif($kembalian < 0)
            {{-- Kalau bon / kurang bayar --}}
            <tr class="font-bold" style="color: red;">
                <td>Sisa Bon</td>
                <td class="right">Rp {{ number_format(abs($kembalian)) }}</td>
            </tr>
        @endif
    </table>

<div class="line"></div>

<div class="center">
    Terima kasih
</div>

</body>
</html>