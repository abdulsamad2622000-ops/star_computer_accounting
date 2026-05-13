<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Invoice #{{ $sale->memo_no }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #0e2a4f;
            background: #f7fbff;
            padding: 20px;
        }
        .receipt {
            width: 820px;
            margin: 0 auto;
            background: #e7f1ff;
            border: 2px solid #5a7ca8;
            border-radius: 10px;
            padding: 18px 18px 12px;
        }
        .header {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 12px;
            align-items: start;
            border-bottom: 2px dashed #5a7ca8;
            padding-bottom: 12px;
            margin-bottom: 10px;
        }
        .brand-block h1 {
            font-size: 26px;
            font-weight: 900;
            color: #163a6f;
            margin: 0;
        }
        .subtitle { font-size: 13px; color: #3e5a7a; margin: 4px 0 10px; }
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px;
            margin-top: 6px;
        }
        .contact-card {
            border: 1px solid #5a7ca8;
            border-radius: 6px;
            padding: 6px 8px;
            background: #f0f6ff;
            font-size: 12px;
        }
        .cname  { font-weight: 600; color: #163a6f; }
        .clabel { color: #3e5a7a; font-size: 11px; }
        .bank-block {
            border: 1px solid #5a7ca8;
            border-radius: 8px;
            padding: 10px 12px;
            background: #f4f8ff;
            font-size: 12px;
        }
        .bank-title { font-weight: 700; color: #163a6f; margin-bottom: 6px; }
        .bank-row { display: grid; grid-template-columns: 130px 1fr; gap: 8px; margin: 3px 0; }
        .bank-label { color: #3e5a7a; }
        .bank-value { font-weight: 600; }
        .address-row { display: flex; gap: 8px; margin-top: 10px; font-size: 12px; color: #3e5a7a; }
        .addr-label { font-weight: 600; color: #163a6f; }

        /* Meta */
        .meta {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }
        .meta-box {
            border: 1px solid #5a7ca8;
            border-radius: 6px;
            background: #f0f6ff;
            padding: 6px 8px;
        }
        .meta-box label {
            display: block;
            font-size: 10px;
            color: #3e5a7a;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .meta-box span { font-weight: 600; font-size: 12px; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid #5a7ca8;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        thead th {
            background: #eaf2ff;
            color: #163a6f;
            font-size: 12px;
            padding: 8px 10px;
            border-bottom: 1px solid #5a7ca8;
            text-align: left;
            font-weight: 700;
        }
        tbody td {
            font-size: 12px;
            padding: 8px 10px;
            border-bottom: 1px dashed #c9d7ea;
        }
        tbody tr:last-child td { border-bottom: none; }
        tfoot td {
            background: #f4f8ff;
            font-weight: 700;
            color: #163a6f;
            padding: 8px 10px;
            border-top: 1px solid #5a7ca8;
        }

        /* Totals */
        .totals {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }
        .totals-box {
            border: 1px solid #5a7ca8;
            border-radius: 8px;
            background: #f0f6ff;
            padding: 10px 12px;
        }
        .t-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .t-row:last-child { border-bottom: none; }
        .t-label { color: #3e5a7a; }
        .t-value { font-weight: 700; color: #163a6f; }
        .t-value.highlight {
            background: #163a6f;
            color: #fff;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .t-value.red { color: #ef4444; }

        /* Notes */
        .note-box {
            border: 1px dashed #5a7ca8;
            border-radius: 8px;
            background: #f4f8ff;
            padding: 10px 12px;
            font-size: 11px;
            color: #3e5a7a;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        /* Footer */
        .footer {
            border-top: 2px dashed #5a7ca8;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #3e5a7a;
        }

        /* Print button */
        .no-print { text-align: right; margin-bottom: 12px; }
        .no-print button {
            background: #163a6f;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

@php $s = App\Models\BusinessSetting::first(); @endphp

<!-- Print Button -->
<div class="no-print">
    <button onclick="window.print()">🖨️ Print Invoice</button>
</div>

<div class="receipt">

    <!-- Header -->
    <div class="header">
        <div class="brand-block">
            <h1>{{ $s->business_name ?? 'STAR COMPUTER' }}</h1>
            <div class="subtitle">
                <strong>{{ $s->tagline ?? '' }}</strong>
            </div>
            <div class="contact-grid">
                @if($s && $s->contact1_name)
                <div class="contact-card">
                    <div class="cname">{{ $s->contact1_name }}</div>
                    <div class="clabel">Contact</div>
                    <div>{{ $s->contact1_phone }}</div>
                </div>
                @endif
                @if($s && $s->contact2_name)
                <div class="contact-card">
                    <div class="cname">{{ $s->contact2_name }}</div>
                    <div class="clabel">Contact</div>
                    <div>{{ $s->contact2_phone }}</div>
                </div>
                @endif
                @if($s && $s->contact3_name)
                <div class="contact-card">
                    <div class="cname">{{ $s->contact3_name }}</div>
                    <div class="clabel">Contact</div>
                    <div>{{ $s->contact3_phone }}</div>
                </div>
                @endif
            </div>
            @if($s && $s->address)
            <div class="address-row">
                <div class="addr-label">Office:</div>
                <div>{{ $s->address }}</div>
            </div>
            @endif
        </div>

        @if($s && $s->bank_name)
        <div class="bank-block">
            <div class="bank-title">
                {{ $s->bank_name }} — {{ $s->bank_account_title }}
            </div>
            <div class="bank-row">
                <div class="bank-label">Account title</div>
                <div class="bank-value">{{ $s->bank_account_title }}</div>
            </div>
            <div class="bank-row">
                <div class="bank-label">Account number</div>
                <div class="bank-value">{{ $s->bank_account_number }}</div>
            </div>
            <div class="bank-row">
                <div class="bank-label">IBAN</div>
                <div class="bank-value">{{ $s->bank_iban }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Meta -->
    <div class="meta">
        <div class="meta-box">
            <label>Memo No.</label>
            <span>{{ $sale->memo_no }}</span>
        </div>
        <div class="meta-box">
            <label>Date</label>
            <span>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</span>
        </div>
        <div class="meta-box">
            <label>Vendor Name</label>
            <span>{{ $sale->vendor->name ?? '—' }}</span>
        </div>
        <div class="meta-box">
            <label>Mobile No.</label>
            <span>{{ $sale->vendor->contact1 ?? '—' }}</span>
        </div>
        <div class="meta-box">
            <label>CNIC</label>
            <span>{{ $sale->vendor->cnic ?? '—' }}</span>
        </div>
        <div class="meta-box">
            <label>Staff</label>
            <span>{{ $sale->salesperson->name ?? '—' }}</span>
        </div>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th style="width:40px">S/N</th>
                <th>Product</th>
                <th style="width:120px">Description</th>
                <th style="width:90px">Stock Code</th>
                <th style="width:60px">Qty</th>
                <th style="width:100px">Purchase Price</th>
                <th style="width:90px">Sale Price</th>
                <th style="width:100px">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product->name ?? '—' }}</td>
                <td>{{ $item->description ?? '—' }}</td>
                <td>{{ $item->stock_code ?? '—' }}</td>
                <td>{{ $item->qty }}</td>
                <td>Rs. {{ number_format($item->rate) }}</td>
                <td>Rs. {{ number_format($item->product->sale_price ?? 0) }}</td>
                <td>Rs. {{ number_format($item->total) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7">Total</td>
                <td>Rs. {{ number_format($sale->subtotal) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Totals -->
    <div class="totals">
        <div class="totals-box">
            <div class="t-row">
                <span class="t-label">Total Amount</span>
                <span class="t-value">Rs. {{ number_format($sale->subtotal) }}</span>
            </div>
            <div class="t-row">
                <span class="t-label">Discount</span>
                <span class="t-value">Rs. {{ number_format($sale->discount) }}</span>
            </div>
            <div class="t-row">
                <span class="t-label">Net Total</span>
                <span class="t-value highlight">
                    Rs. {{ number_format($sale->total) }}
                </span>
            </div>
            <div class="t-row">
                <span class="t-label">Cash Paid</span>
                <span class="t-value">Rs. {{ number_format($sale->paid) }}</span>
            </div>
            @if($sale->balance > 0)
            <div class="t-row">
                <span class="t-label">Payable Balance</span>
                <span class="t-value red">
                    Rs. {{ number_format($sale->balance) }}
                </span>
            </div>
            @endif
        </div>

        <div class="totals-box">
            <div class="t-row">
                <span class="t-label">Payment Method</span>
                <span class="t-value">{{ ucfirst($sale->payment_type) }}</span>
            </div>
            @if($sale->description)
            <div class="t-row">
                <span class="t-label">Reference / Note</span>
                <span class="t-value">{{ $sale->description }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Notes -->
    @if($s && $s->notes)
    <div class="note-box">
        <strong>Note:</strong><br>
        @foreach(explode("\n", $s->notes) as $note)
            @if(trim($note))• {{ trim($note) }}<br>@endif
        @endforeach
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>{{ $s->thank_you_message ?? 'Thank you for your business.' }}</div>
        <div>Powered by STAR COMPUTER POS</div>
    </div>

</div>

<script>
if (window.self !== window.top) {
    window.onload = function() { window.print(); };
}
</script>

</body>
</html>