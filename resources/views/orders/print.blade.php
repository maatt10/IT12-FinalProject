<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt {{ $order->reference_code }} — Lara's Flowershop</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            margin-bottom: 6px;
        }

        .store-info {
            font-size: 10px;
            color: #64748B;
            line-height: 1.6;
        }

        .reference-badge {
            text-align: center;
            margin: 14px 0;
            padding: 10px;
            background: #E8F5E9;
            border-radius: 6px;
        }

        .ref-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            color: #2E5A3B;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .ref-value {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #2E5A3B;
            letter-spacing: 1px;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        .section-label {
            font-size: 9px;
            font-weight: 700;
            color: #94A3B8;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 12px 0 6px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #F0E6DD;
        }

        .meta {
            font-size: 10px;
            margin-bottom: 8px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .meta-label {
            color: #94A3B8;
        }

        .meta-value {
            color: #212121;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }

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

        .items-table thead th.right {
            text-align: right;
        }

        .items-table tbody td {
            padding: 5px 2px;
            border-bottom: 1px solid #F5EEE4;
            vertical-align: top;
        }

        .items-table tbody td.right {
            text-align: right;
        }

        .items-table tbody td.qty {
            text-align: center;
        }

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

        .total-row .label {
            color: #64748B;
        }

        .total-row .value {
            color: #212121;
            font-weight: 600;
        }

        .total-row.discount-line .label {
            color: #B8860B;
            font-weight: 600;
        }

        .total-row.discount-line .value {
            color: #B8860B;
            font-weight: 700;
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

        .discount-info {
            margin-top: 14px;
            padding: 10px 12px;
            background: #FFF8E1;
            border-left: 3px solid #D4AF37;
            border-radius: 4px;
            font-size: 10px;
        }

        .discount-info-title {
            font-size: 9px;
            font-weight: 700;
            color: #B8860B;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .discount-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .discount-info-row span:first-child {
            color: #94A3B8;
        }

        .discount-info-row span:last-child {
            color: #212121;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }

        .footer {
            margin-top: 16px;
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

        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                border-radius: 0;
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }
        }

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
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        .print-button:hover {
            background: #D14A62;
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

        .back-link:hover {
            color: #E85D75;
        }
    </style>
</head>

<body>

    <div class="receipt">

        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Lara's Flowershop" class="logo">
            <div class="store-name">Lara's Flowershop</div>
            <div class="store-sub">Est. 2021</div>
            <div class="store-info">Terminal POS-03</div>
        </div>

        <div class="reference-badge">
            <span class="ref-label">Order No.</span>
            <span class="ref-value">{{ $order->reference_code }}</span>
        </div>

        {{-- ORDER INFO --}}
        <div class="section-label">Order Information</div>
        <div class="meta">
            <div class="meta-row">
                <span class="meta-label">Date:</span>
                <span class="meta-value">{{ $order->order_date->format('M d, Y h:i A') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Customer:</span>
                <span class="meta-value">{{ $order->customer->full_name ?? $order->customer_name ?? 'Unregistered' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Order Type:</span>
                <span class="meta-value">{{ $order->order_type === 'ready_made' ? 'Ready-Made' : 'Customized' }}</span>
            </div>
        </div>

        {{-- RECEIVER --}}
        <div class="section-label">Receiver</div>
        <div class="meta">
            <div class="meta-row">
                <span class="meta-label">Name:</span>
                <span class="meta-value">{{ $order->receiver_full_name }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Mobile:</span>
                <span class="meta-value">{{ $order->receiver_contact }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Fulfillment:</span>
                <span class="meta-value">{{ ucfirst($order->fulfillment_type) }}</span>
            </div>
            @if($order->fulfillment_type === 'delivery' && $order->delivery_address)
            <div class="meta-row">
                <span class="meta-label">Address:</span>
                <span class="meta-value">{{ $order->delivery_address }}</span>
            </div>
            @endif
            <div class="meta-row">
                <span class="meta-label">Scheduled:</span>
                <span class="meta-value">{{ $order->delivery_datetime->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        {{-- ITEMS --}}
        <div class="section-label">Bouquet(s)</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 48%;">Item</th>
                    <th style="width: 12%;" class="qty">Qty</th>
                    <th style="width: 20%;" class="right">Price</th>
                    <th style="width: 20%;" class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-name">
                            {{ $item->product->display_name ?? 'Custom Bouquet' }}
                        </div>
                        @if($item->customization_details)
                        <span class="item-custom">{{ $item->customization_details }}</span>
                        @endif
                    </td>
                    <td class="qty">{{ (float) $item->quantity }}</td>
                    <td class="right">₱{{ number_format($item->unit_price, 2) }}</td>
                    <td class="right" style="font-weight: 600;">₱{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach

                @for($i = 0; $i < max(0, 6 - $order->items->count()); $i++)
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
                <span class="value">₱{{ number_format($order->subtotal, 2) }}</span>
            </div>

            @if($order->discount_amount > 0)
            <div class="total-row discount-line">
                <span class="label">
                    Discount ({{ $order->discount_type === 'pwd' ? 'PWD 20%' : 'Senior 20%' }})
                </span>
                <span class="value">− ₱{{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @endif

            @if($order->fulfillment_type === 'delivery' && $order->delivery_fee > 0)
            <div class="total-row">
                <span class="label">Delivery Fee</span>
                <span class="value">₱{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            @endif

            <div class="total-row grand">
                <span class="label">TOTAL</span>
                <span class="value">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>

            @if($order->payment_proof_reference)
            <div class="total-row" style="margin-top: 6px;">
                <span class="label">GCash Ref. No.</span>
                <span class="value">{{ $order->payment_proof_reference }}</span>
            </div>
            @endif
        </div>

        {{-- DISCOUNT DETAILS --}}
        @if($order->discount_type && $order->discount_type !== 'none')
        <div class="discount-info">
            <div class="discount-info-title">
                {{ $order->discount_type === 'pwd' ? 'PWD Discount Applied' : 'Senior Citizen Discount Applied' }}
            </div>
            <div class="discount-info-row">
                <span>Name:</span>
                <span>{{ $order->discount_name ?: '—' }}</span>
            </div>
            <div class="discount-info-row">
                <span>ID No:</span>
                <span>{{ $order->discount_id_number ?: '—' }}</span>
            </div>
        </div>
        @endif

        <div class="footer">
            <div class="thank-you">Thank you! Come again!</div>
            <div class="powered-by">
                Lara's Flowershop · Sales &amp; Inventory System
            </div>
        </div>

    </div>

    <button onclick="window.print()" class="print-button no-print">
        Print Receipt
    </button>
    @if(request('from') === 'records')
    <a href="{{ route('records.index', ['tab' => 'sales', 'sub' => 'online']) }}" class="back-link no-print">
        ← Back to Order Records
    </a>
    @else
    <a href="{{ route('orders.show', $order) }}" class="back-link no-print">
        ← Back to Order
    </a>
    @endif

</body>

</html>