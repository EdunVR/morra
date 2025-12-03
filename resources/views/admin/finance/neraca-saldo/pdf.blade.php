<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Neraca Saldo - {{ $outlet_name }}</title>
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
            padding-bottom: 15px;
            border-bottom: 2px solid #2563eb;
        }
        
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .report-info {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 120px;
            padding: 3px 0;
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        thead {
            background-color: #f1f5f9;
        }
        
        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            color: #1e293b;
            border-bottom: 2px solid #cbd5e1;
        }
        
        th.text-right {
            text-align: right;
        }
        
        th.text-center {
            text-align: center;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9pt;
        }
        
        td.text-right {
            text-align: right;
        }
        
        td.text-center {
            text-align: center;
        }
        
        tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .account-code {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #475569;
        }
        
        .account-name {
            color: #334155;
        }
        
        .type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
        }
        
        .type-asset {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .type-liability {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .type-equity {
            background-color: #f3e8ff;
            color: #6b21a8;
        }
        
        .type-revenue {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .type-expense {
            background-color: #fed7aa;
            color: #9a3412;
        }
        
        .amount-debit {
            color: #059669;
            font-weight: 600;
        }
        
        .amount-credit {
            color: #dc2626;
            font-weight: 600;
        }
        
        .amount-balance {
            color: #1e293b;
            font-weight: 700;
        }
        
        .amount-negative {
            color: #dc2626;
        }
        
        tfoot {
            background-color: #f1f5f9;
            border-top: 2px solid #94a3b8;
        }
        
        tfoot td {
            padding: 10px 8px;
            font-weight: bold;
            font-size: 10pt;
            border-bottom: none;
        }
        
        .summary-box {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        
        .summary-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 10px;
            color: #1e293b;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-label {
            display: table-cell;
            padding: 5px 0;
            font-weight: 600;
            color: #475569;
        }
        
        .summary-value {
            display: table-cell;
            padding: 5px 0;
            text-align: right;
            font-weight: bold;
        }
        
        .balanced {
            color: #059669;
        }
        
        .unbalanced {
            color: #dc2626;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 8pt;
            color: #64748b;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company_name }}</div>
        <div class="report-title">NERACA SALDO (TRIAL BALANCE)</div>
        <div class="report-info">
            Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
        </div>
    </div>
    
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Outlet:</div>
            <div class="info-value">{{ $outlet_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Buku:</div>
            <div class="info-value">{{ $book_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Cetak:</div>
            <div class="info-value">{{ $print_date }}</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Kode</th>
                <th style="width: 30%;">Nama Akun</th>
                <th class="text-center" style="width: 10%;">Tipe</th>
                <th class="text-right" style="width: 12.5%;">Saldo Awal</th>
                <th class="text-right" style="width: 12.5%;">Debit</th>
                <th class="text-right" style="width: 12.5%;">Kredit</th>
                <th class="text-right" style="width: 12.5%;">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trialBalanceData as $account)
            <tr>
                <td class="account-code">{{ $account['code'] }}</td>
                <td class="account-name">{{ $account['name'] }}</td>
                <td class="text-center">
                    @php
                        $typeClass = 'type-asset';
                        $typeLabel = 'Aset';
                        
                        switch($account['type']) {
                            case 'liability':
                                $typeClass = 'type-liability';
                                $typeLabel = 'Kewajiban';
                                break;
                            case 'equity':
                                $typeClass = 'type-equity';
                                $typeLabel = 'Ekuitas';
                                break;
                            case 'revenue':
                            case 'otherrevenue':
                                $typeClass = 'type-revenue';
                                $typeLabel = 'Pendapatan';
                                break;
                            case 'expense':
                            case 'otherexpense':
                                $typeClass = 'type-expense';
                                $typeLabel = 'Beban';
                                break;
                        }
                    @endphp
                    <span class="type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
                </td>
                <td class="text-right {{ $account['opening_balance'] < 0 ? 'amount-negative' : '' }}">
                    {{ number_format($account['opening_balance'], 0, ',', '.') }}
                </td>
                <td class="text-right amount-debit">
                    {{ number_format($account['debit'], 0, ',', '.') }}
                </td>
                <td class="text-right amount-credit">
                    {{ number_format($account['credit'], 0, ',', '.') }}
                </td>
                <td class="text-right amount-balance {{ $account['ending_balance'] < 0 ? 'amount-negative' : '' }}">
                    {{ number_format($account['ending_balance'], 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">
                    Tidak ada data neraca saldo untuk periode yang dipilih
                </td>
            </tr>
            @endforelse
        </tbody>
        @if(count($trialBalanceData) > 0)
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;">TOTAL</td>
                <td class="text-right amount-debit">{{ number_format($summary['total_debit'], 0, ',', '.') }}</td>
                <td class="text-right amount-credit">{{ number_format($summary['total_credit'], 0, ',', '.') }}</td>
                <td class="text-right amount-balance">{{ number_format($summary['total_debit'] - $summary['total_credit'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    
    @if(count($trialBalanceData) > 0)
    <div class="summary-box">
        <div class="summary-title">Ringkasan</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-label">Total Debit:</div>
                <div class="summary-value amount-debit">Rp {{ number_format($summary['total_debit'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Kredit:</div>
                <div class="summary-value amount-credit">Rp {{ number_format($summary['total_credit'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Selisih:</div>
                <div class="summary-value {{ $summary['is_balanced'] ? 'balanced' : 'unbalanced' }}">
                    Rp {{ number_format($summary['difference'], 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Status:</div>
                <div class="summary-value {{ $summary['is_balanced'] ? 'balanced' : 'unbalanced' }}">
                    {{ $summary['is_balanced'] ? '✓ Seimbang' : '⚠ Tidak Seimbang' }}
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem pada {{ $print_date }}</p>
        <p>{{ $company_name }} - Neraca Saldo</p>
    </div>
</body>
</html>
