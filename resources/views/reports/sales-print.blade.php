<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report — {{ $from }} to {{ $to }}</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Instrument Sans', Arial, sans-serif;
            font-size: 12px;
            color: #212121;
            background: #F9F6F0;
            padding: 30px 20px;
            line-height: 1.5;
        }

        .report {
            max-width: 900px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 40px 36px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(46, 90, 59, 0.08);
        }

        /* ===============================
           HEADER
           =============================== */
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px dashed #F0E6DD;
            margin-bottom: 20px;
        }
        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 6px;
        }
        .store-name {
            font-size: 22px;
            font-weight: 700;
            color: #2E5A3B;
            letter-spacing: 0.5px;
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
            font-size: 11px;
            color: #64748B;
            line-height: 1.6;
        }

        /* ===============================
           REPORT TITLE
           =============================== */
        .report-title {
            text-align: center;
            margin-bottom: 24px;
        }
        .report-title h1 {
            font-size: 20px;
            font-weight: 700;
            color: #212121;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .report-period {
            display: inline-block;
            padding: 4px 14px;
            background: #FCE4EC;
            color: #E85D75;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            border-radius: 4px;
        }

        /* ===============================
           SUMMARY CARDS
           =============================== */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }
        .summary-card {
            background: #FEFCF9;
            border: 1px solid #F0E6DD;
            border-left: 4px solid #E85D75;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .summary-card.green { border-left-color: #2E5A3B; }
        .summary-card.gold { border-left-color: #D4AF37; }

        .summary-label {
            font-size: 10px;
            font-weight: 600;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 20px;
            font-weight: 700;
            color: #212121;
        }
        .summary-value.rose { color: #E85D75; }
        .summary-value.green { color: #2E5A3B; }
        .summary-value.gold { color: #B8860B; }

        /* ===============================
           SECTION TITLES
           =============================== */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #212121;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding-bottom: 8px;
            border-bottom: 2px solid #F8BBD0;
            margin-bottom: 14px;
            color: #E85D75;
        }

        /* ===============================
           PAYMENT TABLE
           =============================== */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 12px;
        }
        .payment-table td {
            padding: 8px 12px;
            border-bottom: 1px dashed #F0E6DD;
        }
        .payment-table tr:last-child td { border-bottom: none; }
        .payment-table td:first-child {
            color: #64748B;
            font-weight: 500;
        }
        .payment-table td:last-child {
            text-align: right;
            font-weight: 700;
            color: #2E5A3B;
        }

        /* ===============================
           TRANSACTION TABLE
           =============================== */
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .transactions-table thead th {
            text-align: left;
            padding: 10px 8px;
            background: #FDF2F4;
            color: #E85D75;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.8px;
            border-bottom: 2px solid #F8BBD0;
        }
        .transactions-table thead th.right { text-align: right; }

        .transactions-table tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #F5EEE4;
            vertical-align: top;
        }
        .transactions-table tbody td.right {
            text-align: right;
            white-space: nowrap;
        }

        .transactions-table tbody tr:nth-child(even) {
            background: #FEFCF9;
        }

        .pay-label {
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pay-label.cash { color: #E85D75; }
        .pay-label.gcash { color: #2E5A3B; }
        .pay-label.bank { color: #B8860B; }

        .discount-value {
            color: #B8860B;
            font-weight: 600;
        }
        .total-value {
            color: #2E5A3B;
            font-weight: 700;
        }

        /* ===============================
           FOOTER
           =============================== */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px dashed #F0E6DD;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            font-size: 10px;
            color: #64748B;
        }
        .footer-block strong {
            display: block;
            color: #212121;
            font-size: 11px;
            margin-bottom: 4px;
        }
        .footer-signature {
            text-align: right;
        }
        .signature-line {
            margin-top: 30px;
            padding-top: 4px;
            border-top: 1px solid #212121;
            min-width: 180px;
            text-align: center;
            color: #212121;
            font-size: 10px;
        }

        /* ===============================
           EMPTY STATE
           =============================== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94A3B8;
            font-size: 13px;
        }

        /* ===============================
           SCREEN BUTTONS
           =============================== */
        .screen-actions {
            max-width: 900px;
            margin: 20px auto 0;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .screen-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
            transition: all 0.2s ease;
        }
        .screen-btn.primary {
            background: #E85D75;
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(232, 93, 117, 0.3);
        }
        .screen-btn.primary:hover {
            background: #D14A62;
            box-shadow: 0 6px 16px rgba(232, 93, 117, 0.4);
        }
        .screen-btn.secondary {
            background: #FFFFFF;
            color: #64748B;
            border: 1.5px solid #F0E6DD;
        }
        .screen-btn.secondary:hover {
            background: #FEFCF9;
            border-color: #E85D75;
            color: #E85D75;
        }

        /* ===============================
           PRINT STYLES
           =============================== */
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
                font-size: 10px;
            }
            .report {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                max-width: 100%;
            }
            .screen-actions {
                display: none !important;
            }
            .summary-card {
                border: 1px solid #E5E5E5;
                background: #FFFFFF;
            }
            .transactions-table tbody tr:nth-child(even) {
                background: #FAFAFA;
            }
        }
    </style>
</head>

<body>

    <div class="report">

        {{-- HEADER --}}
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Lara's Flowershop" class="logo">
            <div class="store-name">Lara's Flowershop</div>
            <div class="store-sub">Est. 2021</div>
            <div class="store-info">
                Store #127 · Manila Central · Terminal POS-03
            </div>
        </div>

        {{-- REPORT TITLE --}}
        <div class="report-title">
            <h1>Sales Report</h1>
            <span class="report-period">
                {{ \Carbon\Carbon::parse($from)->format('F d, Y') }}
                &nbsp;—&nbsp;
                {{ \Carbon\Carbon::parse($to)->format('F d, Y') }}
            </span>
        </div>

        {{-- SUMMARY --}}
        <div class="summary-grid">

            <div class="summary-card">
                <div class="summary-label">Total Sales</div>
                <div class="summary-value rose">₱{{ number_format($totalSales, 2) }}</div>
            </div>

            <div class="summary-card green">
                <div class="summary-label">Transactions</div>
                <div class="summary-value green">{{ $transactionCount }}</div>
            </div>

            <div class="summary-card gold">
                <div class="summary-label">Total Discounts</div>
                <div class="summary-value gold">₱{{ number_format($totalDiscounts, 2) }}</div>
            </div>

        </div>

        {{-- PAYMENT BREAKDOWN --}}
        <div class="section-title">Payment Methods Breakdown</div>

        <table class="payment-table">
            <tr>
                <td>Cash</td>
                <td>₱{{ number_format($paymentTotals['cash'], 2) }}</td>
            </tr>
            <tr>
                <td>GCash</td>
                <td>₱{{ number_format($paymentTotals['gcash'], 2) }}</td>
            </tr>
            <tr>
                <td>Bank Transfer</td>
                <td>₱{{ number_format($paymentTotals['bank_transfer'], 2) }}</td>
            </tr>
        </table>

        {{-- TRANSACTIONS --}}
        <div class="section-title" style="margin-top: 30px;">Transactions</div>

        @if($sales->isEmpty())

            <div class="empty-state">
                No sales were recorded during the selected period.
            </div>

        @else

            <table class="transactions-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Sale #</th>
                        <th style="width: 20%;">Date &amp; Time</th>
                        <th style="width: 20%;">Customer</th>
                        <th style="width: 12%;">Payment</th>
                        <th class="right" style="width: 12%;">Subtotal</th>
                        <th class="right" style="width: 10%;">Discount</th>
                        <th class="right" style="width: 12%;">Total</th>
                        <th style="width: 12%;">Recorded By</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td style="font-weight: 600; color: #E85D75;">
                                #{{ $sale->sale_id }}
                            </td>

                            <td style="color: #64748B; white-space: nowrap;">
                                {{ $sale->sale_date->format('M d, Y') }}<br>
                                <span style="font-size: 10px;">{{ $sale->sale_date->format('h:i A') }}</span>
                            </td>

                            <td>{{ $sale->customer->full_name ?? 'Walk-in' }}</td>

                            <td>
                                <span class="pay-label {{ $sale->payment_method === 'bank_transfer' ? 'bank' : $sale->payment_method }}">
                                    {{ str_replace('_', ' ', $sale->payment_method) }}
                                </span>
                            </td>

                            <td class="right" style="color: #64748B;">
                                ₱{{ number_format($sale->subtotal, 2) }}
                            </td>

                            <td class="right">
                                @if($sale->discount_amount > 0)
                                    <span class="discount-value">− ₱{{ number_format($sale->discount_amount, 2) }}</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>

                            <td class="right total-value">
                                ₱{{ number_format($sale->total_amount, 2) }}
                            </td>

                            <td style="color: #64748B; font-size: 10px;">
                                {{ $sale->user->full_name ?? 'Unknown' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endif

        {{-- FOOTER --}}
        <div class="footer">
            <div class="footer-block">
                <strong>Report Generated By</strong>
                {{ auth()->user()->full_name ?? 'System' }}<br>
                {{ now()->format('F d, Y · h:i A') }}
            </div>

            <div class="footer-signature">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>

    </div>

    {{-- SCREEN-ONLY BUTTONS --}}
    <div class="screen-actions">
        <button onclick="window.print()" class="screen-btn primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print This Report
        </button>

        <a href="{{ route('reports.sales', ['from' => $from, 'to' => $to]) }}" class="screen-btn secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Report
        </a>

        <a href="{{ route('reports.sales.export', ['from' => $from, 'to' => $to]) }}" class="screen-btn secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export CSV
        </a>
    </div>

</body>
</html>