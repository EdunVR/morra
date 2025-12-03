<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
    <style>
        @page {
            margin: 10mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 5mm;
        }

        .container {
            width: 100%;
        }

        /* Header Styles */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header .company-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header .outlet-name {
            font-size: 12pt;
            margin-bottom: 3px;
        }

        .header .period {
            font-size: 11pt;
            color: #666;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
        }

        table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            font-size: 9pt;
        }

        table td.amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        table td.account-code {
            width: 100px;
        }

        table td.account-name {
            width: auto;
        }

        /* Section Headers */
        .section-header {
            background-color: #e8e8e8 !important;
            font-weight: bold;
            font-size: 11pt;
        }

        /* Total Rows */
        .total-row {
            font-weight: bold;
            background-color: #f8f8f8;
            border-top: 2px solid #333 !important;
        }

        .grand-total-row {
            font-weight: bold;
            background-color: #e0e0e0;
            border-top: 3px double #333 !important;
            border-bottom: 3px double #333 !important;
            font-size: 11pt;
        }

        /* Child Account Indentation */
        .child-account {
            padding-left: 20px;
        }

        .grandchild-account {
            padding-left: 40px;
        }

        /* Ratios Section */
        .ratios-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .ratios-section h3 {
            font-size: 12pt;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .ratio-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ddd;
        }

        .ratio-label {
            font-weight: bold;
        }

        .ratio-value {
            font-family: 'Courier New', monospace;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        /* Comparison Columns */
        .comparison-table th,
        .comparison-table td {
            width: 16.66%;
        }

        .comparison-table td.account-name {
            width: auto;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Positive/Negative Indicators */
        .positive {
            color: #28a745;
        }

        .negative {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ $filters['company_name'] ?? config('app.name', 'Nama Perusahaan') }}</div>
            <h1>Laporan Laba Rugi</h1>
            <div class="outlet-name">{{ $filters['outlet_name'] ?? '-' }}</div>
            <div class="period">
                Periode: {{ \Carbon\Carbon::parse($filters['start_date'])->translatedFormat('d F Y') }} 
                s/d {{ \Carbon\Carbon::parse($filters['end_date'])->translatedFormat('d F Y') }}
            </div>
            @if($filters['comparison_enabled'] ?? false)
            <div class="period" style="margin-top: 5px;">
                Pembanding: {{ \Carbon\Carbon::parse($filters['comparison_start_date'])->translatedFormat('d F Y') }} 
                s/d {{ \Carbon\Carbon::parse($filters['comparison_end_date'])->translatedFormat('d F Y') }}
            </div>
            @endif
        </div>

        <!-- Profit & Loss Statement Table -->
        <table class="{{ ($filters['comparison_enabled'] ?? false) ? 'comparison-table' : '' }}">
            <thead>
                <tr>
                    <th class="account-code">Kode Akun</th>
                    <th class="account-name">Nama Akun</th>
                    <th style="text-align: right;">Jumlah</th>
                    @if($filters['comparison_enabled'] ?? false)
                    <th style="text-align: right;">Pembanding</th>
                    <th style="text-align: right;">Selisih</th>
                    <th style="text-align: right;">%</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <!-- PENDAPATAN -->
                <tr class="section-header">
                    <td colspan="{{ ($filters['comparison_enabled'] ?? false) ? 6 : 3 }}">PENDAPATAN</td>
                </tr>
                @foreach($data['revenue']['accounts'] ?? [] as $account)
                    <tr>
                        <td class="account-code">{{ $account['code'] }}</td>
                        <td class="account-name">{{ $account['name'] }}</td>
                        <td class="amount">{{ number_format($account['amount'], 2, ',', '.') }}</td>
                        @if($filters['comparison_enabled'] ?? false)
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        @endif
                    </tr>
                    @if(!empty($account['children']))
                        @foreach($account['children'] as $child)
                        <tr>
                            <td class="account-code">{{ $child['code'] }}</td>
                            <td class="account-name child-account">{{ $child['name'] }}</td>
                            <td class="amount">{{ number_format($child['amount'], 2, ',', '.') }}</td>
                            @if($filters['comparison_enabled'] ?? false)
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                @endforeach
                <tr class="total-row">
                    <td></td>
                    <td><strong>Total Pendapatan</strong></td>
                    <td class="amount"><strong>{{ number_format($data['revenue']['total'] ?? 0, 2, ',', '.') }}</strong></td>
                    @if($filters['comparison_enabled'] ?? false)
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    @endif
                </tr>

                <!-- PENDAPATAN LAIN-LAIN -->
                @if(!empty($data['other_revenue']['accounts']))
                <tr class="section-header">
                    <td colspan="{{ ($filters['comparison_enabled'] ?? false) ? 6 : 3 }}">PENDAPATAN LAIN-LAIN</td>
                </tr>
                @foreach($data['other_revenue']['accounts'] ?? [] as $account)
                    <tr>
                        <td class="account-code">{{ $account['code'] }}</td>
                        <td class="account-name">{{ $account['name'] }}</td>
                        <td class="amount">{{ number_format($account['amount'], 2, ',', '.') }}</td>
                        @if($filters['comparison_enabled'] ?? false)
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        @endif
                    </tr>
                    @if(!empty($account['children']))
                        @foreach($account['children'] as $child)
                        <tr>
                            <td class="account-code">{{ $child['code'] }}</td>
                            <td class="account-name child-account">{{ $child['name'] }}</td>
                            <td class="amount">{{ number_format($child['amount'], 2, ',', '.') }}</td>
                            @if($filters['comparison_enabled'] ?? false)
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                @endforeach
                <tr class="total-row">
                    <td></td>
                    <td><strong>Total Pendapatan Lain-Lain</strong></td>
                    <td class="amount"><strong>{{ number_format($data['other_revenue']['total'] ?? 0, 2, ',', '.') }}</strong></td>
                    @if($filters['comparison_enabled'] ?? false)
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    @endif
                </tr>
                @endif

                <!-- TOTAL PENDAPATAN -->
                <tr class="grand-total-row">
                    <td></td>
                    <td><strong>TOTAL PENDAPATAN</strong></td>
                    <td class="amount"><strong>{{ number_format($data['summary']['total_revenue'] ?? 0, 2, ',', '.') }}</strong></td>
                    @if($filters['comparison_enabled'] ?? false)
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    @endif
                </tr>

                <!-- Empty Row -->
                <tr>
                    <td colspan="{{ ($filters['comparison_enabled'] ?? false) ? 6 : 3 }}" style="border: none; height: 10px;"></td>
                </tr>

                <!-- BEBAN OPERASIONAL -->
                <tr class="section-header">
                    <td colspan="{{ ($filters['comparison_enabled'] ?? false) ? 6 : 3 }}">BEBAN OPERASIONAL</td>
                </tr>
                @foreach($data['expense']['accounts'] ?? [] as $account)
                    <tr>
                        <td class="account-code">{{ $account['code'] }}</td>
                        <td class="account-name">{{ $account['name'] }}</td>
                        <td class="amount">{{ number_format($account['amount'], 2, ',', '.') }}</td>
                        @if($filters['comparison_enabled'] ?? false)
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        @endif
                    </tr>
                    @if(!empty($account['children']))
                        @foreach($account['children'] as $child)
                        <tr>
                            <td class="account-code">{{ $child['code'] }}</td>
                            <td class="account-name child-account">{{ $child['name'] }}</td>
                            <td class="amount">{{ number_format($child['amount'], 2, ',', '.') }}</td>
                            @if($filters['comparison_enabled'] ?? false)
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                @endforeach
                <tr class="total-row">
                    <td></td>
                    <td><strong>Total Beban Operasional</strong></td>
                    <td class="amount"><strong>{{ number_format($data['expense']['total'] ?? 0, 2, ',', '.') }}</strong></td>
                    @if($filters['comparison_enabled'] ?? false)
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    @endif
                </tr>

                <!-- BEBAN LAIN-LAIN -->
                @if(!empty($data['other_expense']['accounts']))
                <tr class="section-header">
                    <td colspan="{{ ($filters['comparison_enabled'] ?? false) ? 6 : 3 }}">BEBAN LAIN-LAIN</td>
                </tr>
                @foreach($data['other_expense']['accounts'] ?? [] as $account)
                    <tr>
                        <td class="account-code">{{ $account['code'] }}</td>
                        <td class="account-name">{{ $account['name'] }}</td>
                        <td class="amount">{{ number_format($account['amount'], 2, ',', '.') }}</td>
                        @if($filters['comparison_enabled'] ?? false)
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        @endif
                    </tr>
                    @if(!empty($account['children']))
                        @foreach($account['children'] as $child)
                        <tr>
                            <td class="account-code">{{ $child['code'] }}</td>
                            <td class="account-name child-account">{{ $child['name'] }}</td>
                            <td class="amount">{{ number_format($child['amount'], 2, ',', '.') }}</td>
                            @if($filters['comparison_enabled'] ?? false)
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                @endforeach
                <tr class="total-row">
                    <td></td>
                    <td><strong>Total Beban Lain-Lain</strong></td>
                    <td class="amount"><strong>{{ number_format($data['other_expense']['total'] ?? 0, 2, ',', '.') }}</strong></td>
                    @if($filters['comparison_enabled'] ?? false)
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    @endif
                </tr>
                @endif

                <!-- TOTAL BEBAN -->
                <tr class="grand-total-row">
                    <td></td>
                    <td><strong>TOTAL BEBAN</strong></td>
                    <td class="amount"><strong>{{ number_format($data['summary']['total_expense'] ?? 0, 2, ',', '.') }}</strong></td>
                    @if($filters['comparison_enabled'] ?? false)
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    @endif
                </tr>

                <!-- Empty Row -->
                <tr>
                    <td colspan="{{ ($filters['comparison_enabled'] ?? false) ? 6 : 3 }}" style="border: none; height: 10px;"></td>
                </tr>

                <!-- LABA/RUGI BERSIH -->
                <tr class="grand-total-row" style="background-color: {{ ($data['summary']['net_income'] ?? 0) >= 0 ? '#d4edda' : '#f8d7da' }};">
                    <td></td>
                    <td><strong>LABA/RUGI BERSIH</strong></td>
                    <td class="amount"><strong>{{ number_format($data['summary']['net_income'] ?? 0, 2, ',', '.') }}</strong></td>
                    @if($filters['comparison_enabled'] ?? false)
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    @endif
                </tr>
            </tbody>
        </table>

        <!-- Financial Ratios Section -->
        <div class="ratios-section">
            <h3>RASIO KEUANGAN</h3>
            <div class="ratio-row">
                <span class="ratio-label">Gross Profit Margin:</span>
                <span class="ratio-value">{{ $data['summary']['gross_profit_margin'] ?? 'N/A' }}{{ is_numeric($data['summary']['gross_profit_margin'] ?? null) ? '%' : '' }}</span>
            </div>
            <div class="ratio-row">
                <span class="ratio-label">Net Profit Margin:</span>
                <span class="ratio-value">{{ $data['summary']['net_profit_margin'] ?? 'N/A' }}{{ is_numeric($data['summary']['net_profit_margin'] ?? null) ? '%' : '' }}</span>
            </div>
            <div class="ratio-row">
                <span class="ratio-label">Operating Expense Ratio:</span>
                <span class="ratio-value">{{ $data['summary']['operating_expense_ratio'] ?? 'N/A' }}{{ is_numeric($data['summary']['operating_expense_ratio'] ?? null) ? '%' : '' }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i:s') }}</p>
            <p>{{ $filters['company_name'] ?? config('app.name', 'Nama Perusahaan') }}</p>
        </div>
    </div>
</body>
</html>
