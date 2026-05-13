<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size:10px; padding:15px; }
        .header { border-bottom:2px solid #163a6f; padding-bottom:8px; margin-bottom:12px; }
        .header h2 { font-size:16px; color:#163a6f; font-weight:bold; }
        table { width:100%; border-collapse:collapse; }
        thead th { background:#163a6f; color:#fff; padding:6px 8px; font-size:9px; text-align:left; }
        tbody td { padding:6px 8px; border-bottom:1px dashed #ccc; font-size:9px; }
        tbody tr:nth-child(even) td { background:#f4f8ff; }
        .balance { color:#ef4444; font-weight:bold; }
    </style>
</head>
<body>
@php $s = App\Models\BusinessSetting::first(); @endphp
<div class="header">
    <h2>{{ $s->business_name ?? 'STAR COMPUTER' }}</h2>
    <p>Customer List — {{ now()->format('d M Y') }}</p>
</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Contact</th>
            <th>CNIC</th>
            <th>Address</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $i => $customer)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->contact1 ?? '—' }}</td>
            <td>{{ $customer->cnic ?? '—' }}</td>
            <td>{{ $customer->address ?? '—' }}</td>
            <td class="{{ $customer->balance > 0 ? 'balance' : '' }}">
                Rs. {{ number_format($customer->balance) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>