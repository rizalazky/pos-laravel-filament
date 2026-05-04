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
    <strong>My Store</strong><br>
    Jl. Contoh No.1
</div>

<div class="line"></div>

<div>
    Invoice: {{ $sale->invoice_number }}<br>
    Date: {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y H:i') }}
</div>

<div class="line"></div>

@foreach($sale->items as $item)
    <div>{{ $item->product->name }}</div>
    <div>
        {{ $item->quantity }} x {{ number_format($item->price) }}
        <span style="float:right">
            {{ number_format($item->subtotal) }}
        </span>
    </div>
@endforeach

<div class="line"></div>

<div>
    Total:
    <span style="float:right">
        {{ number_format($sale->total) }}
    </span>
</div>

<div class="line"></div>

<div class="center">
    Terima kasih 🙏
</div>

</body>
</html>