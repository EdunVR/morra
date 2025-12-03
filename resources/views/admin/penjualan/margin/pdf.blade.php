<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Margin & Profit</title>
    <style>
        @page {
            margin: 15mm 10mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #1a1a1a;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 3px solid #2563eb;
            background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);
            padding-top: 10px;
        }
        
        .header h1 {
            font-size: 18pt;
            margin-bottom: 4px;
            color: #1e40af;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .header .subtitle {
            font-size: 10pt;
            color: #475569;
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .header .period {
            font-size: 9pt;
            color: #64748b;
            font-style: italic;
        }
        
        .info-section {
            margin-bottom: 12px;
            background: #f1f5f9;
            padding: 8px 12px;
            border-radius: 4px;
            border-left: 4px solid #3b82f6;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: 600;
            color: #334155;
            padding: 3px 0;
            width: 120px;
        }
        
        .info-value {
            display: table-cell;
            color: #475569;
            padding: 3px 0;
        }
        
        .summary-section {
            margin-bottom: 15px;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
        }
        
        .summary-title {
            font-size: 10pt;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        
        .summary-item {
            display: table-cell;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
            width: 25%;
        }
        
        .summary-item.profit {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-color: #bbf7d0;
        }
        
        .summary-item-label {
            font-size: 7pt;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .summary-item-value {
            font-size: 11pt;
            font-weight: 700;
            color: #1e40af;
        }
        
        .summary-item.profit .summary-item-value {
            color: #16a34a;
        }
        
        .table-container {
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
        }
        
        thead {
            background: linear-gradient(to bottom, #1e40af 0%, #1e3a8a 100%);
            color: white;
        }
        
        th {
            padding: 7px 4px;
            text-align: left;
            font-size: 7pt;
            font-weight: 700;
            border-right: 1px solid rgba(255,255,255,0.2);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        th:last-child {
            border-right: none;
        }
        
        th.text-right {
            text-align: right;
        }
        
        th.text-center {
            text-align: center;
        }
        
        tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        tbody tr:nth-child(odd) {
            background: #ffffff;
        }
        
        td {
            padding: 5px 4px;
            font-size: 7pt;
            color: #334155;
            border-right: 1px solid #f1f5f9;
        }
        
        td:last-child {
            border-right: none;
        }
        
        td.text-right {
            text-align: right;
        }
        
        td.text-center {
            text-align: center;
        }
        
        td.font-medium {
            font-weight: 600;
            color: #1e293b;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .badge-invoice {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        
        .badge-pos {
            background: #cffafe;
            color: #0e7490;
            border: 1px solid #67e8f9;
        }
        
        .badge-cash {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .badge-qris {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        
        .badge-bon {
            background: #fed7aa;
            color: #9a3412;
            border: 1px solid #fdba74;
        }
        
        .margin-high {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .margin-medium {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        
        .margin-low {
            background: #fed7aa;
            color: #9a3412;
            border: 1px solid #fdba74;
        }
        
        .margin-negative {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .profit-positive {
            color: #16a34a;
            font-weight: 700;
        }
        
        .profit-negative {
            color: #dc2626;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 7pt;
            color: #64748b;
        }
        
        .footer-row {
            margin: 3px 0;
        }
        
        .footer-brand {
            font-weight: 700;
            color: #1e40af;
            margin-top: 5px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* Zebra striping enhancement */
        tbody tr:hover {
            background: #f1f5f9 !important;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Laporan Margin & Profit</h1>
        <div class="subtitle">{{ $outletName }}</div>
        <div class="period">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
    </div>

    {{-- Info Section --}}
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Tanggal Cetak</div>
                <div class="info-value">: {{ $generatedAt }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Transaksi</div>
                <div class="info-value">: {{ $summary['total_items'] }} item produk</div>
            </div>
            <div class="info-row">
                <div class="info-label">Sumber Data</div>
                <div class="info-value">: Invoice & Point of Sales (POS)</div>
            </div>
        </div>
    </div>

    {{-- Summary Section --}}
    <div class="summary-section">
        <div class="summary-title">Ringkasan Keuangan</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-item-label">Total HPP</div>
                <div class="summary-item-value">Rp {{ number_format($summary['total_hpp'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-item-label">Total Penjualan</div>
                <div class="summary-item-value">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item profit">
                <div class="summary-item-label">Total Profit</div>
                <div class="summary-item-value">Rp {{ number_format($summary['total_profit'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item profit">
                <div class="summary-item-label">Avg Margin</div>
                <div class="summary-item-value">{{ number_format($summary['avg_margin'], 2) }}%</div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 2.5%;">No</th>
                    <th style="width: 6%;">Source</th>
                    <th style="width: 7%;">Tanggal</th>
                    <th style="width: 9%;">Outlet</th>
                    <th style="width: 20%;">Produk</th>
                    <th class="text-right" style="width: 4%;">Qty</th>
                    <th class="text-right" style="width: 9%;">HPP</th>
                    <th class="text-right" style="width: 9%;">Harga Jual</th>
                    <th class="text-right" style="width: 10%;">Subtotal</th>
                    <th class="text-right" style="width: 10%;">Profit</th>
                    <th class="text-center" style="width: 6.5%;">Margin</th>
                    <th class="text-center" style="width: 7%;">Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marginData as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        @if($item['source'] === 'invoice')
                            <span class="badge badge-invoice">INV</span>
                        @else
                            <span class="badge badge-pos">POS</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/y') }}</td>
                    <td>{{ $item['outlet'] }}</td>
                    <td class="font-medium">{{ $item['produk'] }}</td>
                    <td class="text-right">{{ number_format($item['qty'], 0) }}</td>
                    <td class="text-right">{{ number_format($item['hpp'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item['harga_jual'], 0, ',', '.') }}</td>
                    <td class="text-right font-medium">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    <td class="text-right {{ $item['profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ number_format($item['profit'], 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @php
                            $marginClass = 'margin-negative';
                            if ($item['margin_pct'] >= 30) {
                                $marginClass = 'margin-high';
                            } elseif ($item['margin_pct'] >= 15) {
                                $marginClass = 'margin-medium';
                            } elseif ($item['margin_pct'] >= 5) {
                                $marginClass = 'margin-low';
                            }
                        @endphp
                        <span class="badge {{ $marginClass }}">{{ number_format($item['margin_pct'], 1) }}%</span>
                    </td>
                    <td class="text-center">
                        @php
                            $paymentClass = 'badge-cash';
                            $paymentLabel = $item['payment_type'];
                            if (strtolower($item['payment_type']) === 'qris') {
                                $paymentClass = 'badge-qris';
                            } elseif (strtolower($item['payment_type']) === 'bon') {
                                $paymentClass = 'badge-bon';
                            }
                        @endphp
                        <span class="badge {{ $paymentClass }}">{{ strtoupper($paymentLabel) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-row">
            <strong>Catatan:</strong> Margin dihitung berdasarkan (Profit / Subtotal) × 100%
        </div>
        <div class="footer-row">
            Laporan ini digenerate otomatis oleh sistem ERP pada {{ $generatedAt }}
        </div>
        <div class="footer-brand">
            © {{ date('Y') }} - Sistem ERP Terintegrasi
        </div>
    </div>
</body>
</html>
