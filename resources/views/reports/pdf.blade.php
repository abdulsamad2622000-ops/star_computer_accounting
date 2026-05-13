<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1f2e;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a1f2e;
            padding-bottom: 10px;
        }
        h2 { font-size: 18px; margin: 0; }
        p  { margin: 4px 0; color: #6b7280; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead th {
            background: #1a1f2e;
            color: #fff;
            padding: 8px;
            font-size: 10px;
            text-align: left;
        }
        tbody td {
            padding: 7px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        tbody tr:nth-child(even) td { background: #f9fafb; }
        .totals {
            margin-top: 16px;
            text-align: right;
        }
        .totals table {
            width: 250px;
            margin-left: auto;
        }
        .totals td { padding: 5px 8px; }
        .grand { font-weight: bold; background: #1a1f2e; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <h2>⭐ STAR COMPUTER</h2>
        <p>Daily Report:
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
            —
            {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Memo #</th>
                <th>Customer</th>
                <th>Item</th>
                <th>Stock Code</th>
                <th>Qty</th>
                <th>Bill Amt</th>
                <th>Received</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y') }}</td>
                    <td>{{ $sale->memo_no }}</td>
                    <td>{{ $sale->customer->name ?? '—' }}</td>
                    <td>{{ $item->product->name ?? '—' }}</td>
                    <td>{{ $item->stock_code ?? '—' }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>Rs. {{ number_format($item->total) }}</td>
                    <td>
                        @if($loop->first)
                            Rs. {{ number_format($sale->paid) }}
                        @else —
                        @endif
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Total Bill:</td>
                <td align="right">
                    Rs. {{ number_format($sales->sum('total')) }}
                </td>
            </tr>
            <tr>
                <td>Total Received:</td>
                <td align="right">
                    Rs. {{ number_format($sales->sum('paid')) }}
                </td>
            </tr>
            <tr class="grand">
                <td>Balance:</td>
                <td align="right">
                    Rs. {{ number_format($sales->sum('balance')) }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>