<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size:10px; color:#0e2a4f; padding:15px; }
        .header { border-bottom:2px solid #5a7ca8; padding-bottom:10px; margin-bottom:12px; }
        .header h2 { font-size:16px; color:#163a6f; font-weight:bold; }
        .header p { color:#3e5a7a; font-size:10px; margin-top:2px; }
        table { width:100%; border-collapse:collapse; margin-bottom:10px; }
        thead th { background:#163a6f; color:#fff; padding:6px 8px; font-size:9px; text-align:left; font-weight:bold; }
        tbody td { padding:6px 8px; border-bottom:1px dashed #c9d7ea; font-size:9px; }
        tbody tr:nth-child(even) td { background:#f4f8ff; }
        .low { color:#ef4444; font-weight:bold; }
        .footer { margin-top:12px; border-top:1px dashed #5a7ca8; padding-top:8px; font-size:9px; color:#3e5a7a; display:flex; justify-content:space-between; }
    </style>
</head>
<body>

@php $s = App\Models\BusinessSetting::first(); @endphp

<div class="header">
    <h2>{{ $s->business_name ?? 'STAR COMPUTER' }}</h2>
    <p>Inventory Report — Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Stock Code</th>
            <th>Product</th>
            <th>Vendor</th>
            <th>Received</th>
            <th>Sold</th>
            <th>Remaining</th>
            <th>P. Price</th>
            <th>S. Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $i => $product)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $product->stock_code ?? '—' }}</td>
            <td><strong>{{ $product->name }}</strong></td>
            <td>{{ $product->vendor->name ?? '—' }}</td>
            <td>{{ $product->received_qty }}</td>
            <td>{{ $product->sold_qty }}</td>
            <td @if($product->remaining_qty <= $product->alert_qty) class="low" @endif>
                {{ $product->remaining_qty }}
                @if($product->remaining_qty <= $product->alert_qty) ⚠️ @endif
            </td>
            <td>Rs. {{ number_format($product->purchase_price) }}</td>
            <td>Rs. {{ number_format($product->sale_price) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <span>Total Products: {{ $products->count() }}</span>
    <span>{{ $s->thank_you_message ?? 'Star Computer POS' }}</span>
</div>

</body>
</html>