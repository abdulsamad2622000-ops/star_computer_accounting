<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>STAR COMPUTER — Invoice #{{ $sale->memo_no }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <style>
    :root {
      --brand-blue: #e7f1ff;
      --border: #5a7ca8;
      --text: #0e2a4f;
      --muted: #3e5a7a;
      --accent: #163a6f;
    }
    * { box-sizing: border-box; }
    html, body {
      margin: 0; padding: 0;
      color: var(--text);
      font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: #f7fbff;
    }
    .receipt {
      width: 820px;
      margin: 24px auto;
      background: var(--brand-blue);
      border: 2px solid var(--border);
      border-radius: 10px;
      padding: 18px 18px 12px;
      box-shadow: 0 4px 18px rgba(22,58,111,0.12);
    }
    .header {
      display: grid;
      grid-template-columns: 1.3fr 1fr;
      gap: 12px;
      align-items: start;
      border-bottom: 2px dashed var(--border);
      padding-bottom: 12px;
      margin-bottom: 10px;
    }
    .brand-block h1 {
      margin: 0;
      font-size: 28px;
      letter-spacing: 0.8px;
      color: var(--accent);
    }
    .brand-block .subtitle {
      margin: 4px 0 10px;
      font-size: 14px;
      color: var(--muted);
      letter-spacing: 0.3px;
    }
    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 6px;
      margin-top: 6px;
    }
    .contact-card {
      border: 1px solid var(--border);
      border-radius: 6px;
      padding: 6px 8px;
      background: #f0f6ff;
      font-size: 13px;
      line-height: 1.35;
    }
    .contact-card .name { font-weight: 600; color: var(--accent); }
    .contact-card .label { color: var(--muted); }
    .bank-block {
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 10px 12px;
      background: #f4f8ff;
      font-size: 13px;
    }
    .bank-title {
      font-weight: 700;
      color: var(--accent);
      margin-bottom: 6px;
    }
    .bank-row {
      display: grid;
      grid-template-columns: 140px 1fr;
      gap: 8px;
      margin: 3px 0;
    }
    .bank-label { color: var(--muted); }
    .bank-value { font-weight: 600; color: var(--text); }
    .address-row {
      display: flex;
      gap: 12px;
      margin-top: 12px;
      font-size: 13px;
      color: var(--muted);
    }
    .address-row .addr-label { font-weight: 600; color: var(--accent); }
    .meta {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 8px;
      margin-bottom: 10px;
    }
    .meta .field {
      border: 1px solid var(--border);
      border-radius: 6px;
      background: #f0f6ff;
      padding: 6px 8px;
      font-size: 13px;
    }
    .field label {
      display: block;
      font-size: 12px;
      color: var(--muted);
      margin-bottom: 4px;
    }
    .field input {
      width: 100%;
      border: none;
      outline: none;
      background: transparent;
      font-weight: 600;
      color: var(--text);
      font-size: 13px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      background: #ffffff;
      border: 1px solid var(--border);
      border-radius: 8px;
      overflow: hidden;
    }
    thead th {
      background: #eaf2ff;
      color: var(--accent);
      text-align: left;
      font-size: 13px;
      padding: 8px 10px;
      border-bottom: 1px solid var(--border);
    }
    tbody td {
      font-size: 13px;
      padding: 8px 10px;
      border-bottom: 1px dashed #c9d7ea;
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody td input {
      width: 100%;
      border: none;
      outline: none;
      background: transparent;
      font-size: 13px;
      color: var(--text);
    }
    tfoot td {
      background: #f4f8ff;
      font-weight: 700;
      color: var(--accent);
      padding: 8px 10px;
      border-top: 1px solid var(--border);
    }
    .totals {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin: 12px 0 8px;
    }
    .totals .block {
      border: 1px solid var(--border);
      border-radius: 8px;
      background: #f0f6ff;
      padding: 10px 12px;
    }
    .totals .row {
      display: grid;
      grid-template-columns: 160px 1fr;
      gap: 10px;
      margin: 6px 0;
      font-size: 14px;
      align-items: center;
    }
    .totals .label { color: var(--muted); }
    .totals .value input {
      width: 100%;
      border: none;
      outline: none;
      background: #ffffff;
      border-radius: 6px;
      padding: 6px 8px;
      font-weight: 700;
      color: var(--accent);
    }
    .signature {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 12px;
      align-items: end;
      margin-top: 6px;
    }
    .signature .note {
      font-size: 12.5px;
      color: var(--muted);
      line-height: 1.5;
      background: #f4f8ff;
      border: 1px dashed var(--border);
      border-radius: 8px;
      padding: 10px 12px;
    }
    .footer {
      border-top: 2px dashed var(--border);
      margin-top: 10px;
      padding-top: 10px;
      display: flex;
      justify-content: space-between;
      font-size: 12.5px;
      color: var(--muted);
    }
    .actions {
      display: flex;
      gap: 8px;
      margin: 0 auto 16px;
      width: 820px;
    }
    .btn {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: #ffffff;
      color: var(--accent);
      font-weight: 600;
      cursor: pointer;
    }
    .btn:hover { background: #f0f6ff; }
    @media print {
      .actions { display: none !important; }
      body { background: #ffffff; }
      .receipt {
        box-shadow: none; margin: 0;
        border-radius: 0; border-width: 1px; width: 100%;
      }
      .contact-card, .bank-block, .field, .block, .note {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      input { border: none !important; }
    }
  </style>
</head>
<body>

@php $s = App\Models\BusinessSetting::first(); @endphp

<!-- Actions -->
<div class="actions">
    <button class="btn" onclick="window.print()">🖨️ Print Invoice</button>
</div>

<section class="receipt">

  <!-- Header -->
  <div class="header">
    <div class="brand-block">
      <h1>{{ $s->business_name ?? 'STAR COMPUTER' }}</h1>
      <div class="subtitle">
        <strong>{{ $s->tagline ?? 'Deal in All Computer Products' }}</strong>
      </div>

      <div class="contact-grid">
        @if($s && $s->contact1_name)
        <div class="contact-card">
          <div class="name">{{ $s->contact1_name }}</div>
          <div class="label">Contact</div>
          <div>{{ $s->contact1_phone }}</div>
        </div>
        @endif
        @if($s && $s->contact2_name)
        <div class="contact-card">
          <div class="name">{{ $s->contact2_name }}</div>
          <div class="label">Contact</div>
          <div>{{ $s->contact2_phone }}</div>
        </div>
        @endif
        @if($s && $s->contact3_name)
        <div class="contact-card">
          <div class="name">{{ $s->contact3_name }}</div>
          <div class="label">Contact</div>
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
    <div class="field">
      <label>Memo no.</label>
      <input type="text" value="{{ $sale->memo_no }}" readonly/>
    </div>
    <div class="field">
      <label>Date</label>
      <input type="text"
             value="{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}"
             readonly/>
    </div>
    <div class="field">
      <label>Customer name</label>
      <input type="text" value="{{ $sale->customer->name ?? '' }}" readonly/>
    </div>
    <div class="field">
      <label>Mobile no.</label>
      <input type="text" value="{{ $sale->customer->contact1 ?? '' }}" readonly/>
    </div>
    <div class="field">
      <label>CNIC </label>
      <input type="text" value="{{ $sale->customer->cnic ?? '' }}" readonly/>
    </div>
    <div class="field">
      <label>Salesperson</label>
      <input type="text" value="{{ $sale->salesperson->name ?? '' }}" readonly/>
    </div>
  </div>

  <!-- Items Table -->
  <table id="items">
    <thead>
    <tr>
        <th style="width:45px">S/N</th>
        <th>Item</th>
        <th style="width:120px">Description</th>
        <th style="width:100px">Stock Code</th>
        <th style="width:80px">Qty</th>
        <th style="width:100px">Rate</th>
        <th style="width:110px">Amount</th>
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
    <td>{{ number_format($item->rate) }}</td>
    <td>{{ number_format($item->total) }}</td>
</tr>
@endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5">Total</td>
        <td id="totalCell">{{ number_format($sale->subtotal, 2) }}</td>
      </tr>
    </tfoot>
  </table>

  <!-- Totals -->
  <div class="totals">
    <div class="block">
      <div class="row">
        <div class="label">Total amount</div>
        <div class="value">
          <input type="number" id="totalAmount"
                 value="{{ $sale->subtotal }}" readonly/>
        </div>
      </div>
      <div class="row">
        <div class="label">Discount</div>
        <div class="value">
          <input type="number" id="discountAmount"
                 value="{{ $sale->discount }}" oninput="recalc()"/>
        </div>
      </div>
      <div class="row">
        <div class="label">Net total</div>
        <div class="value">
          <input type="number" id="netTotal"
                 value="{{ $sale->total }}" readonly/>
        </div>
      </div>
      <div class="row">
        <div class="label">Received amount</div>
        <div class="value">
          <input type="number" id="receivedAmount"
                 value="{{ $sale->paid }}" oninput="recalcBalance()"/>
        </div>
      </div>
      <div class="row">
        <div class="label">Balance</div>
        <div class="value">
          <input type="number" id="balanceAmount"
                 value="{{ $sale->balance }}" readonly/>
        </div>
      </div>
    </div>

    <div class="block">
      <div class="row">
        <div class="label">Payment method</div>
        <div class="value">
          <input type="text"
                 value="{{ ucfirst($sale->payment_type) }}" readonly/>
        </div>
      </div>
      @if($sale->description)
      <div class="row">
        <div class="label">Reference / Note</div>
        <div class="value">
          <input type="text" value="{{ $sale->description }}" readonly/>
        </div>
      </div>
      @endif
    </div>
  </div>

  <!-- Notes + Footer -->
  <div class="signature">
    @if($s && $s->notes)
    <div class="note">
      <strong>Note:</strong><br/>
      @foreach(explode("\n", $s->notes) as $note)
        @if(trim($note))
          • {{ trim($note) }}<br/>
        @endif
      @endforeach
    </div>
    @endif
  </div>

  <div class="footer">
    <div>{{ $s->thank_you_message ?? 'Thank you for your business.' }}</div>
    <div>Powered by STAR COMPUTER POS</div>
  </div>

</section>

<script>
// Recalculate totals
function recalc() {
  const tbody = document.querySelector("#items tbody");
  let total = 0;

  [...tbody.rows].forEach(row => {
    const qty  = parseFloat(row.cells[3].querySelector("input").value || "0");
    const rate = parseFloat(row.cells[4].querySelector("input").value || "0");
    const amt  = qty * rate || 0;
    row.cells[5].querySelector("input").value = amt.toFixed(2);
    total += amt;
  });

  const discount = parseFloat(
    document.getElementById("discountAmount")?.value || "0"
  );
  const net = Math.max(total - discount, 0);

  document.getElementById("totalCell").textContent  = total.toFixed(2);
  document.getElementById("totalAmount").value      = total.toFixed(2);
  document.getElementById("netTotal").value         = net.toFixed(2);
  recalcBalance();
}

function recalcBalance() {
  const net      = parseFloat(document.getElementById("netTotal").value || "0");
  const received = parseFloat(document.getElementById("receivedAmount").value || "0");
  const bal      = Math.max(net - received, 0);
  document.getElementById("balanceAmount").value = bal.toFixed(2);
}

// Add new row
function addRow() {
  const tbody = document.querySelector("#items tbody");
  const idx   = tbody.rows.length + 1;
  const tr    = document.createElement("tr");
  tr.innerHTML = `
    <td><input type="text" value="${idx}" readonly/></td>
    <td><input type="text" placeholder="Item description"/></td>
    <td><input type="text" placeholder="Stock code"/></td>
    <td><input type="number" min="0" step="1" value="1" oninput="recalc()"/></td>
    <td><input type="number" min="0" step="0.01" value="0" oninput="recalc()"/></td>
    <td><input type="number" min="0" step="0.01" value="0" readonly/></td>
  `;
  tbody.appendChild(tr);
  recalc();
  // Focus description of new row
  tr.cells[1].querySelector("input").focus();
}

// Clear empty rows
function clearItems() {
  const tbody = document.querySelector("#items tbody");
  [...tbody.rows].forEach(row => {
    const desc = row.cells[1].querySelector("input").value.trim();
    const rate = parseFloat(row.cells[4].querySelector("input").value || "0");
    if (desc === "" && rate === 0 && tbody.rows.length > 1) {
      row.remove();
    }
  });
  // Re-number
  [...tbody.rows].forEach((row, i) => {
    row.cells[0].querySelector("input").value = i + 1;
  });
  recalc();
}

// ✅ TAB on last cell → new row
document.querySelector("#items tbody").addEventListener("keydown", function(e) {
  if (e.key !== "Tab" || e.shiftKey) return;

  const tbody   = this;
  const lastRow = tbody.rows[tbody.rows.length - 1];
  const lastInput = lastRow.cells[5].querySelector("input");

  if (e.target === lastInput) {
    e.preventDefault();
    addRow();
  }
});
</script>
<script>
// Agar iframe mein khula hai to auto print
if (window.self !== window.top) {
    window.onload = function() {
        window.print();
    };
}
</script>
</body>
</html>