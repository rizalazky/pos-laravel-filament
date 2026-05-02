<div class="label">
    <div>{{ $item['name'] }}</div>

    <div class="barcode">
        {!! $item['barcode'] !!}
    </div>

    <div>{{ $item['sku'] }}</div>

    <div>Rp {{ number_format($item['price']) }}</div>
</div>