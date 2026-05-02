<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }

        .grid {
            display: flex;
            flex-wrap: wrap;
        }

        .label {
            width: 200px;
            border: 1px solid #000;
            padding: 5px;
            margin: 5px;
            text-align: center;
        }

        .barcode div {
            transform: scaleX(0.7);
            transform-origin: left;
        }
    </style>
</head>
<body>

<div class="grid">
    @foreach($items as $item)
        @include('barcode.components.label', ['item' => $item])
    @endforeach
</div>

</body>
</html>