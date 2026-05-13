<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #0e2a4f;
            padding: 15px;
        }

        /* Header */
        .header {
            border-bottom: 2px solid #5a7ca8;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header h2 {
            font-size: 16px;
            color: #163a6f;
            font-weight: bold;
        }
        .header p {
            color: #3e5a7a;
            font-size: 10px;
            margin-top: 2px;
        }

        /* Customer Info */
        .customer-info {
            width: 100%;
            margin-bottom: 12px;
            border: 1px solid #5a7ca8;
            border-radius: 6px;
            background: #e7f1ff;
        }
        .info-cell {
            padding: 6px 10px;
            vertical-align: top;
            width: 16%;
        }
        .info-label {
            font-size: 8px;
            color: #3e5a7a;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
        }
        .info-value {
            font-weight: bold;
            font-size: 10px;
            color: #0e2a4f;
        }

        /* Stats */
        .stats {
            width: 100%;
            margin-bottom: 12px;
        }
        .stat-box {
            border: 1px solid #5a7ca8;
            border-radius: 6px;
            padding: 8px 10px;
            background: #f0f6ff;
            width: 30%;
            vertical-align: top;
        }
        .stat-label {
            font-size: 8px;
            color: #3e5a7a;
            text-transform: uppercase;
            font-weight: bold;
        }
        .stat-value {
            font-size: 13px;
            font-weight: bold;
            color: #163a6f;
            margin-top: 3px;
        }
        .stat-value.red { color: #ef4444; }

        /* Table */
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .ledger-table thead th {
            background: #163a6f;
            color: #fff;
            padding: 6px 8px;
            font-size: 9px;
            text-align: left;
            font-weight: bold;
        }
        .ledger-table tbody td {
            padding: 6px 8px;
            border-bottom: 1px dashed #c9d7ea;
            font-size: 9px;
            color: #0e2a4f;
        }
        .ledger-table tbody tr:nth-child(even) td {
            background: #f4f8ff;
        }
        .ledger-table tfoot td {
            padding: 6px 8px;
            font-weight: bold;
            font-size: 10px;
            border-top: 2px solid #5a7ca8;
            background: #e7f1ff;
        }

        /* Footer */
        .footer {
            margin-top: 12px;
            border-top: 1px dashed #5a7ca8;
            padding-top: 8px;
            font-size: 9px;
            color: #3e5a7a;
            width: 100%;
        }
    </style>
</head>
<body>

@php $s = App\Models\BusinessSetting::first(); @endphp

<!-- Header -->
<div class="header">
    <h2>{{ $s->business_name ?? 'STAR COMPUTER' }}</h2>
    <p>Customer Ledger Statement</p>
    <p>Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>

<!-- Customer Info -->
<table class="customer-info" cellpadding="0" cellspacing="0">
    <tr>
        <td class="info-cell">
            <span class="info-label">Name</span>
            <span class="info-value">{{ $customer->name }}</span>
        </td>
        <td class="info-cell">
            <span class="info-label">Contact 1</span>
            <span class="info-value">{{ $customer->contact1 ?? '—' }}</span>
        </td>
        <td class="info-cell">
            <span class="info-label">Contact 2</span>
            <span class="info-value">{{ $customer->contact2 ?? '—' }}</span>
        </td>
        <td class="info-cell">
            <span class="info-label">CNIC</span>
            <span class="info-value">{{ $customer->cnic ?? '—' }}</span>
        </td>
        <td class="info-cell">
            <span class="info-label">Address</span>
            <span class="info-value">{{ $customer->address ?? '—' }}</span>
        </td>
        <td class="info-cell">
            <span class="info-label">Opening Balance</span>
            <span class="info-value">
                Rs. {{ number_format($customer->opening_balance) }}
            </span>
        </td>
    </tr>
</table>

<!-- Stats -->
<table class="stats" cellpadding="5" cellspacing="0">
    <tr>
        <td class="stat-box">
            <div class="stat-label">Total Bill Amount</div>
            <div class="stat-value">Rs. {{ number_format($totalBill) }}</div>
        </td>
        <td width="5%"></td>
        <td class="stat-box">
            <div class="stat-label">Total Received</div>
            <div class="stat-value">Rs. {{ number_format($totalReceived) }}</div>
        </td>
        <td width="5%"></td>
        <td class="stat-box">
            <div class="stat-label">Balance (Receivable)</div>
            <div class="stat-value red">Rs. {{ number_format($balance) }}</div>
        </td>
    </tr>
</table>

<!-- Ledger Table -->
<table class="ledger-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>Date</th>
            <th>Memo #</th>
            <th>Name</th>
            <th>Item</th>
            <th>Stock Code</th>
            <th>Qty</th>
            <th>Bill Amount</th>
            <th>Received</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $sale)
            @foreach($sale->items as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y') }}</td>
                <td>{{ $sale->memo_no }}</td>
                <td>{{ $customer->name }}</td>
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
                <td>{{ $sale->description ?? '—' }}</td>
            </tr>
            @endforeach
        @empty
        <tr>
            <td colspan="9" style="text-align:center;padding:15px">
                Koi transaction nahi mili
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align:right">Total:</td>
            <td>Rs. {{ number_format($totalBill) }}</td>
            <td>Rs. {{ number_format($totalReceived) }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="6" style="text-align:right;color:#ef4444">
                Balance (Receivable):
            </td>
            <td colspan="2" style="color:#ef4444">
                Rs. {{ number_format($balance) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
</table>

<!-- Footer -->
<table class="footer" cellpadding="0" cellspacing="0">
    <tr>
        <td>{{ $s->thank_you_message ?? 'Thank you for your business.' }}</td>
        <td style="text-align:right">Powered by STAR COMPUTER POS</td>
    </tr>
</table>

</body>
</html>