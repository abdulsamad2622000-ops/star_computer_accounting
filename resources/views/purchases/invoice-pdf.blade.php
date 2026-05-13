<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #0e2a4f;
            background: #e7f1ff;
            padding: 15px;
        }
        .receipt {
            background: #e7f1ff;
            border: 2px solid #5a7ca8;
            border-radius: 8px;
            padding: 15px;
        }
        .header { border-bottom: 2px dashed #5a7ca8; padding-bottom: 10px; margin-bottom: 10px; }
        .header-grid { width: 100%; }
        .brand-col { width: 55%; vertical-align: top; }
        .bank-col  { width: 45%; vertical-align: top; }
        .brand h1 { font-size: 20px; font-weight: bold; color: #163a6f; margin-bottom: 2px; }
        .subtitle { font-size: 11px; color: #3e5a7a; margin-bottom: 8px; }
        .contacts { width: 100%; margin-bottom: 6px; }
        .contact-box { border: 1px solid #5a7ca8; border-radius: 4px; padding: 4px 6px; background: #f0f6ff; width: 30%; vertical-align: top; }
        .c-name  { font-weight: bold; color: #163a6f; font-size: 10px; }
        .c-label { color: #3e5a7a; font-size: 9px; }
        .c-phone { font-size: 10px; }
        .bank-box { border: 1px solid #5a7ca8; border-radius: 6px; padding: 8px 10px; background: #f4f8ff; }
        .bank-title { font-weight: bold; color: #163a6f; font-size: 11px; margin-bottom: 5px; }
        .bank-row { width: 100%; margin: 2px 0; }
        .bank-label { color: #3e5a7a; font-size: 10px; width: 45%; }
        .bank-value { font-weight: bold; font-size: 10px; }
        .address { font-size: 10px; color: #3e5a7a; margin-top: 6px; }
        .addr-label { font-weight: bold; color: #163a6f; }
        .meta { width: 100%; margin-bottom: 12px; border: 1px solid #5a7ca8; border-radius: 6px; background: #e7f1ff; }
        .meta-cell { padding: 6px 10px; vertical-align: top; width: 16%; }
        .meta-label { font-size: 8px; color: #3e5a7a; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .meta-value { font-weight: bold; font-size: 10px; color: #0e2a4f; }
        .items { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #5a7ca8; margin-bottom: 10px; }
        .items thead th { background: #eaf2ff; color: #163a6f; font-size: 10px; padding: 6px 8px; border-bottom: 1px solid #5a7ca8; text-align: left; font-weight: bold; }
        .items tbody td { font-size: 10px; padding: 6px 8px; border-bottom: 1px dashed #c9d7ea; color: #0e2a4f; }
        .items tbody tr:nth-child(even) td { background: #f4f8ff; }
        .items tfoot td { background: #f4f8ff; font-weight: bold; color: #163a6f; padding: 6px 8px; border-top: 1px solid #5a7ca8; font-size: 11px; }
        .totals { width: 100%; margin-bottom: 10px; }
        .totals-left  { width: 48%; vertical-align: top; }
        .totals-right { width: 4%; }
        .totals-right2 { width: 48%; vertical-align: top; }
        .totals-box { border: 1px solid #5a7ca8; border-radius: 6px; background: #f0f6ff; padding: 8px 10px; }
        .t-row { width: 100%; margin: 4px 0; }
        .t-label { color: #3e5a7a; font-size: 10px; width: 55%; vertical-align: middle; }
        .t-value { font-weight: bold; color: #163a6f; font-size: 11px; text-align: right; background: #fff; border-radius: 4px; padding: 3px 6px; vertical-align: middle; }
        .t-value.highlight { background: #163a6f; color: #fff; }
        .t-value.red { color: #ef4444; background: #fef2f2; }
        .notes-box { border: 1px dashed #5a7ca8; border-radius: 6px; background: #f4f8ff; padding: 8px 10px; font-size: 10px; color: #3e5a7a; line-height: 1.6; margin-bottom: 8px; }
        .notes-title { font-weight: bold; color: #163a6f; }
        .footer { border-top: 2px dashed #5a7ca8; padding-top: 6px; width: 100%; }
        .footer-left  { font-size: 10px; color: #3e5a7a; }
        .footer-right { font-size: 10px; color: #3e5a7a; text-align: right; }
    </style>
</head>
<body>

@php $s = App\Models\BusinessSetting::first(); @endphp

<div class="receipt">

    <!-- Header -->
    <div class="header">
        <table class="header-grid" cellpadding="0" cellspacing="0">
            <tr>
                <td class="brand-col">
                    <div class="brand">
                        <h1>{{ $s->business_name ?? 'STAR COMPUTER' }}</h1>
                        <div class="subtitle"><strong>{{ $s->tagline ?? '' }}</strong></div>
                    </div>
                    @if($s && ($s->contact1_name || $s->contact2_name || $s->contact3_name))
                    <table class="contacts" cellpadding="3" cellspacing="3">
                        <tr>
                            @if($s->contact1_name)
                            <td class="contact-box">
                                <div class="c-name">{{ $s->contact1_name }}</div>
                                <div class="c-label">Contact</div>
                                <div class="c-phone">{{ $s->contact1_phone }}</div>
                            </td>
                            @endif
                            @if($s->contact2_name)
                            <td width="3%"></td>
                            <td class="contact-box">
                                <div class="c-name">{{ $s->contact2_name }}</div>
                                <div class="c-label">Contact</div>
                                <div class="c-phone">{{ $s->contact2_phone }}</div>
                            </td>
                            @endif
                            @if($s->contact3_name)
                            <td width="3%"></td>
                            <td class="contact-box">
                                <div class="c-name">{{ $s->contact3_name }}</div>
                                <div class="c-label">Contact</div>
                                <div class="c-phone">{{ $s->contact3_phone }}</div>
                            </td>
                            @endif
                        </tr>
                    </table>
                    @endif
                    @if($s && $s->address)
                    <div class="address">
                        <span class="addr-label">Office: </span>{{ $s->address }}
                    </div>
                    @endif
                </td>
                <td width="5%"></td>
                <td class="bank-col">
                    @if($s && $s->bank_name)
                    <div class="bank-box">
                        <div class="bank-title">{{ $s->bank_name }} — {{ $s->bank_account_title }}</div>
                        <table class="bank-row" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="bank-label">Account title</td>
                                <td class="bank-value">{{ $s->bank_account_title }}</td>
                            </tr>
                            <tr>
                                <td class="bank-label">Account number</td>
                                <td class="bank-value">{{ $s->bank_account_number }}</td>
                            </tr>
                            <tr>
                                <td class="bank-label">IBAN</td>
                                <td class="bank-value">{{ $s->bank_iban }}</td>
                            </tr>
                        </table>
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Meta -->
    <table class="meta" cellpadding="3" cellspacing="0">
        <tr>
            <td class="meta-cell">
                <span class="meta-label">Memo no.</span>
                <span class="meta-value">{{ $sale->memo_no }}</span>
            </td>
            <td width="1%"></td>
            <td class="meta-cell">
                <span class="meta-label">Date</span>
                <span class="meta-value">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</span>
            </td>
            <td width="1%"></td>
            <td class="meta-cell">
                <span class="meta-label">Vendor Name</span>
                <span class="meta-value">{{ $sale->vendor->name ?? '—' }}</span>
            </td>
            <td width="1%"></td>
            <td class="meta-cell">
                <span class="meta-label">Mobile No.</span>
                <span class="meta-value">{{ $sale->vendor->contact1 ?? '—' }}</span>
            </td>
            <td width="1%"></td>
            <td class="meta-cell">
                <span class="meta-label">CNIC</span>
                <span class="meta-value">{{ $sale->vendor->cnic ?? '—' }}</span>
            </td>
            <td width="1%"></td>
            <td class="meta-cell">
                <span class="meta-label">Staff</span>
                <span class="meta-value">{{ $sale->salesperson->name ?? '—' }}</span>
            </td>
        </tr>
    </table>

    <!-- Items -->
    <table class="items" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:30px">S/N</th>
                <th>Product</th>
                <th style="width:90px">Description</th>
                <th style="width:80px">Stock Code</th>
                <th style="width:50px">Qty</th>
                <th style="width:85px">P. Price</th>
                <th style="width:85px">S. Price</th>
                <th style="width:85px">Amount</th>
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
                <td>Rs. {{ number_format($item->rate, 2) }}</td>
                <td>Rs. {{ number_format($item->product->sale_price ?? 0, 2) }}</td>
                <td>Rs. {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7">Total</td>
                <td>Rs. {{ number_format($sale->subtotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Totals -->
    <table class="totals" cellpadding="0" cellspacing="0">
        <tr>
            <td class="totals-left">
                <div class="totals-box">
                    <table width="100%" cellpadding="2" cellspacing="0">
                        <tr class="t-row">
                            <td class="t-label">Total Amount</td>
                            <td class="t-value">Rs. {{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        <tr class="t-row">
                            <td class="t-label">Discount</td>
                            <td class="t-value">Rs. {{ number_format($sale->discount, 2) }}</td>
                        </tr>
                        <tr class="t-row">
                            <td class="t-label" style="font-weight:bold;color:#163a6f">Net Total</td>
                            <td class="t-value highlight">Rs. {{ number_format($sale->total, 2) }}</td>
                        </tr>
                        <tr class="t-row">
                            <td class="t-label">Cash Paid</td>
                            <td class="t-value">Rs. {{ number_format($sale->paid, 2) }}</td>
                        </tr>
                        @if($sale->balance > 0)
                        <tr class="t-row">
                            <td class="t-label" style="color:#ef4444;font-weight:bold">Payable Balance</td>
                            <td class="t-value red">Rs. {{ number_format($sale->balance, 2) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
            <td class="totals-right"></td>
            <td class="totals-right2">
                <div class="totals-box">
                    <table width="100%" cellpadding="2" cellspacing="0">
                        <tr class="t-row">
                            <td class="t-label">Payment Method</td>
                            <td class="t-value">{{ ucfirst($sale->payment_type) }}</td>
                        </tr>
                        @if($sale->description)
                        <tr class="t-row">
                            <td class="t-label">Reference / Note</td>
                            <td class="t-value">{{ $sale->description }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Notes -->
    @if($s && $s->notes)
    <div class="notes-box">
        <span class="notes-title">Note:</span><br>
        @foreach(explode("\n", $s->notes) as $note)
            @if(trim($note))• {{ trim($note) }}<br>@endif
        @endforeach
    </div>
    @endif

    <!-- Footer -->
    <table class="footer" cellpadding="0" cellspacing="0">
        <tr>
            <td class="footer-left">{{ $s->thank_you_message ?? 'Thank you for your business.' }}</td>
            <td class="footer-right">Powered by STAR COMPUTER POS</td>
        </tr>
    </table>

</div>
</body>
</html>