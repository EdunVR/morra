<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header .company-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header .period {
            font-size: 10pt;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 15px;
            font-size: 9pt;
        }
        
        .info-section table {
            width: 100%;
        }
        
        .info-section td {
            padding: 2px 0;
        }
        
        .info-section td:first-child {
            width: 120px;
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 6px 8px;
            margin-bottom: 5px;
            border-left: 4px solid #2563eb;
        }
        
        .cashflow-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .cashflow-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .cashflow-table .item-name {
            width: 70%;
        }
        
        .cashflow-table .amount {
            width: 30%;
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .cashflow-table .subtotal {
            font-weight: bold;
            background-color: #f9fafb;
            border-top: 1px solid #333;
        }
        
        .cashflow-table .total {
            font-weight: bold;
            font-size: 11pt;
            background-color: #e5e7eb;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        
        .positive {
            color: #059669;
        }
        
        .negative {
            color: #dc2626;
        }
        
        .summary-box {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f9ff;
            border: 1px solid #2563eb;
            border-radius: 4px;
        }
        
        .summary-box table {
            width: 100%;
        }
        
        .summary-box td {
            padding: 4px 8px;
        }
        
        .summary-box .label {
            font-weight: bold;
            width: 70%;
        }
        
        .summary-box .value {
            text-align: right;
            font-family: 'Courier New', monospace;
            width: 30%;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 8pt;
            color: #666;
            text-align: center;
        }
        
        @media print {
            body {
                margin: 0;
            }
            
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $companyName ?? config('app.name') }}</div>
        <h1>LAPORAN ARUS KAS</h1>
        <div class="period">
            Periode: {{ $startDate }} s/d {{ $endDate }}<br>
            Metode: {{ $method === 'direct' ? 'Langsung (Direct)' : 'Tidak Langsung (Indirect)' }}
        </div>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td>Outlet</td>
                <td>: {{ $outletName ?? 'Semua Outlet' }}</td>
            </tr>
            <tr>
                <td>Buku Akuntansi</td>
                <td>: {{ $bookName ?? 'Semua Buku' }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    {{-- Operating Activities --}}
    <div class="section">
        <div class="section-title">AKTIVITAS OPERASI</div>
        <table class="cashflow-table">
            @foreach($operatingActivities as $item)
            <tr>
                <td class="item-name">{{ $item['name'] }}</td>
                <td class="amount {{ $item['amount'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format(abs($item['amount']), 0, ',', '.') }}
                    @if($item['amount'] < 0) ({{ number_format(abs($item['amount']), 0, ',', '.') }}) @endif
                </td>
            </tr>
            @endforeach
            <tr class="subtotal">
                <td class="item-name">Kas Bersih dari Aktivitas Operasi</td>
                <td class="amount {{ $netOperating >= 0 ? 'positive' : 'negative' }}">
                    @if($netOperating < 0) ( @endif
                    {{ number_format(abs($netOperating), 0, ',', '.') }}
                    @if($netOperating < 0) ) @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Investing Activities --}}
    <div class="section">
        <div class="section-title">AKTIVITAS INVESTASI</div>
        <table class="cashflow-table">
            @foreach($investingActivities as $item)
            <tr>
                <td class="item-name">{{ $item['name'] }}</td>
                <td class="amount {{ $item['amount'] >= 0 ? 'positive' : 'negative' }}">
                    @if($item['amount'] < 0) ( @endif
                    {{ number_format(abs($item['amount']), 0, ',', '.') }}
                    @if($item['amount'] < 0) ) @endif
                </td>
            </tr>
            @endforeach
            <tr class="subtotal">
                <td class="item-name">Kas Bersih dari Aktivitas Investasi</td>
                <td class="amount {{ $netInvesting >= 0 ? 'positive' : 'negative' }}">
                    @if($netInvesting < 0) ( @endif
                    {{ number_format(abs($netInvesting), 0, ',', '.') }}
                    @if($netInvesting < 0) ) @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Financing Activities --}}
    <div class="section">
        <div class="section-title">AKTIVITAS PENDANAAN</div>
        <table class="cashflow-table">
            @foreach($financingActivities as $item)
            <tr>
                <td class="item-name">{{ $item['name'] }}</td>
                <td class="amount {{ $item['amount'] >= 0 ? 'positive' : 'negative' }}">
                    @if($item['amount'] < 0) ( @endif
                    {{ number_format(abs($item['amount']), 0, ',', '.') }}
                    @if($item['amount'] < 0) ) @endif
                </td>
            </tr>
            @endforeach
            <tr class="subtotal">
                <td class="item-name">Kas Bersih dari Aktivitas Pendanaan</td>
                <td class="amount {{ $netFinancing >= 0 ? 'positive' : 'negative' }}">
                    @if($netFinancing < 0) ( @endif
                    {{ number_format(abs($netFinancing), 0, ',', '.') }}
                    @if($netFinancing < 0) ) @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Summary --}}
    <div class="summary-box">
        <table>
            <tr>
                <td class="label">Kenaikan (Penurunan) Kas Bersih</td>
                <td class="value {{ $netCashFlow >= 0 ? 'positive' : 'negative' }}">
                    @if($netCashFlow < 0) ( @endif
                    Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}
                    @if($netCashFlow < 0) ) @endif
                </td>
            </tr>
            <tr>
                <td class="label">Kas Awal Periode</td>
                <td class="value">Rp {{ number_format($beginningCash, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 2px solid #2563eb;">
                <td class="label" style="font-size: 11pt;">Kas Akhir Periode</td>
                <td class="value" style="font-size: 11pt; font-weight: bold;">
                    Rp {{ number_format($endingCash, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name ?? 'System' }} | 
        {{ config('app.name') }} - Laporan Arus Kas
    </div>
</body>
</html>
