<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->sale_id }} — Lara's Flowershop</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'SF Mono', 'Courier New', Consolas, monospace;
            font-size: 12px;
            color: #212121;
            background: #F9F6F0;
            padding: 20px;
            line-height: 1.5;
        }

        .receipt {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 20px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(46, 90, 59, 0.08);
        }

        /* HEADER */
        .header {
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 2px dashed #F0E6DD;
            margin-bottom: 12px;
        }
        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 6px;
        }
        .store-name {
            font-size: 18px;
            font-weight: 700;
            color: #2E5A3B;
            letter-spacing: 0.5px;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }
        .store-sub {
            font-size: 9px;
            font-weight: 600;
            color: #D4AF37;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 2px;
            margin-bottom: 8px;
        }
        .store-info {
            font-size: 10px;
            color: #64748B;
            line-height: 1.6;
        }

        /* META */
        .meta {
            font-size: 10px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #F0E6DD;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .meta-label { color: #94A3B8; }
        .meta-value {
            color: #212121;
            font-weight: 600;
            text-align: right;
        }

        .invoice-badge { text-align: center; margin: 10px 0; }
        .invoice-number {
            display: inline-block;
            padding: 4px 12px;
            background: #FCE4EC;
            color: #E85D75;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            border-radius: 4px;
        }

        /* ITEMS TABLE */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .items-table thead th {
            text-align: left;
            padding: 6px 2px;
            border-bottom: 2px solid #F8BBD0;
            color: #E85D75;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-size: 9px;
        }
        .items-table thead th.right { text-align: right; }
        .items-table tbody td {
            padding: 5px 2px;
            border-bottom: 1px solid #F5EEE4;
            vertical-align: top;
        }
        .items-table tbody td.right { text-align: right; }
        .items-table tbody td.qty { text-align: center; }
        .item-name {
            font-weight: 600;
            color: #212121;
            line-height: 1.3;
        }
        .item-custom {
            display: block;
            font-size: 9px;
            color: #94A3B8;
            font-style: italic;
            margin-top: 1px;
        }
        .discount-text { color: #B8860B; }

        /* TOTALS */
        .totals {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px dashed #F0E6DD;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 11px;
        }
        .total-row .label { color: #64748B; }
        .total-row .value {
            color: #212121;
            font-weight: 600;
        }
        .total-row.grand {
            margin-top: 6px;
            padding-top: 8px;
            border-top: 1px solid #F0E6DD;
        }
        .total-row.grand .label {
            font-size: 12px;
            font-weight: 700;
            color: #212121;
        }
        .total-row.grand .value {
            font-size: 16px;
            font-weight: 700;
            color: #E85D75;
        }
        .total-row.change .value { color: #2E5A3B; }

        /* SIGNATURE */
        .signature {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #F0E6DD;
            text-align: center;
            font-size: 10px;
            color: #64748B;
        }
        .signature-line {
            margin-top: 25px;
            padding-top: 4px;
            border-top: 1px solid #212121;
            display: inline-block;
            min-width: 140px;
            font-size: 9px;
            color: #212121;
        }
        .signature-label {
            font-size: 9px;
            color: #94A3B8;
            margin-top: 4px;
        }

        /* TERMS */
        .terms {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px dashed #F0E6DD;
            font-size: 9px;
            color: #94A3B8;
            text-align: center;
            line-height: 1.6;
        }

        /* FOOTER */
        .footer {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 2px dashed #F0E6DD;
            text-align: center;
        }
        .thank-you {
            font-size: 12px;
            font-weight: 700;
            color: #2E5A3B;
            margin-bottom: 4px;
        }
        .powered-by {
            font-size: 9px;
            color: #94A3B8;
        }

        /* PRINT */
        @media print {
            body { background: #FFFFFF; padding: 0; }
            .receipt {
                box-shadow: none;
                border-radius: 0;
                padding: 10px;
            }
            .no-print { display: none !important; }
        }

        /* SCREEN BUTTONS */
        .print-button {
            display: block;
            width: 80mm;
            max-width: 100%;
            margin: 16px auto 0;
            padding: 12px;
            background: #E85D75;
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-family: 'Instrument Sans', Arial, sans-serif;
            transition: all 0.2s ease;
        }
        .print-button:hover {
            background: #D14A62;
            box-shadow: 0 4px 12px rgba(232, 93, 117, 0.4);
        }
        .back-link {
            display: block;
            width: 80mm;
            max-width: 100%;
            margin: 10px auto 0;
            text-align: center;
            font-size: 12px;
            color: #64748B;
            text-decoration: none;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }
        .back-link:hover { color: #E85D75; }
    </style>
</head>

<body>

    <div class="receipt">

        {{-- HEADER --}}
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Lara's Flowershop" class="logo">
            <div class="store-name">Lara's Flowershop</div>
            <div class="store-sub">Est. 2021</div>
            <div class="store-info">
                Store #127 · Manila Central<br>
                Terminal POS-03
            </div>
        </div>

        {{-- META --}}
        <div class="meta">
            <div class="meta-row">
                <span class="meta-label">Date:</span>
                <span class="meta-value">{{ $sale->sale_date->format('M d, Y') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Time:</span>
                <span class="meta-value">{{ $sale->sale_date->format('h:i A') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Cashier:</span>
                <span class="meta-value">{{ $sale->user->full_name ?? 'Cashier' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Customer:</span>
                <span class="meta-value">{{ $sale->customer->full_name ?? 'Walk-in Customer' }}</span>
            </div>
        </div>

        {{-- INVOICE NUMBER --}}
        <div class="invoice-badge">
            <span class="invoice-number">RECEIPT #{{ str_pad($sale->sale_id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>

        {{-- ITEMS --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 42%;">Item</th>
                    <th style="width: 12%;" class="qty">Qty</th>
                    <th style="width: 20%;" class="right">Price</th>
                    <th style="width: 26%;" class="right">Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach($sale->items as $item)
                    @php
                        $lineTotal = $item->quantity * $item->unit_price;
                    @endphp

                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product->display_name }}</div>
                            @if($item->customization_details ?? false)
                                <span class="item-custom">{{ $item->customization_details }}</span>
                            @endif
                        </td>
                        <td class="qty">{{ (float) $item->quantity }}</td>
                        <td class="right">₱{{ number_format($item->unit_price, 2) }}</td>
                        <td class="right" style="font-weight: 600;">
                            ₱{{ number_format($lineTotal, 2) }}
                        </td>
                    </tr>
                @endforeach

                @for($i = 0; $i < max(0, 8 - $sale->items->count()); $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td class="qty">&nbsp;</td>
                        <td class="right">&nbsp;</td>
                        <td class="right">&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        {{-- TOTALS --}}
        <div class="totals">

            <div class="total-row">
                <span class="label">Subtotal</span>
                <span class="value">₱{{ number_format($sale->subtotal, 2) }}</span>
            </div>

            @if($sale->discount_amount > 0)
                <div class="total-row">
                    <span class="label">Discount</span>
                    <span class="value discount-text">− ₱{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
            @endif

            <div class="total-row grand">
                <span class="label">Total</span>
                <span class="value">₱{{ number_format($sale->total_amount, 2) }}</span>
            </div>

            @if($sale->payment_method === 'cash')
                <div class="total-row">
                    <span class="label">Cash Received</span>
                    <span class="value">₱{{ number_format($sale->cash_received ?? $sale->total_amount, 2) }}</span>
                </div>
                <div class="total-row change">
                    <span class="label">Change</span>
                    <span class="value">₱{{ number_format($sale->change ?? 0, 2) }}</span>
                </div>
            @else
                <div class="total-row">
                    <span class="label">Payment Method</span>
                    <span class="value">{{ strtoupper(str_replace('_', ' ', $sale->payment_method)) }}</span>
                </div>
            @endif

        </div>

        {{-- SIGNATURE --}}
        <div class="signature">
            <div>Received by:</div>
            <div class="signature-line">&nbsp;</div>
            <div class="signature-label">Customer Signature</div>
        </div>

        {{-- TERMS --}}
        <div class="terms">
            Thank you for your purchase!<br>
            Items are final sale. Please verify all items<br>
            before leaving the counter.
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <div class="thank-you">Thank you! Come again! 🌸</div>
            <div class="powered-by">
                Lara's Flowershop · Sales &amp; Inventory System
            </div>
        </div>

    </div>

    <button onclick="window.print()" class="print-button no-print">
        Print Receipt
    </button>
    <a href="{{ route('sales.create') }}" class="back-link no-print">
        ← Start New Sale
    </a>

</body>
</html>