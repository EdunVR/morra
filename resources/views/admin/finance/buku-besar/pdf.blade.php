<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Besar</title>
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
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
            padding: 5mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
            color: #1a1a1a;
        }
        
        .header h2 {
            font-size: 14pt;
            margin-bottom: 10px;
            color: #4a4a4a;
        }
        
        .filter-info {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 3px solid #4F46E5;
        }
        
        .filter-info p {
            margin: 3px 0;
            font-size: 9pt;
        }
        
        .account-header {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 10pt;
        }
        
        .account-code {
            color: #4F46E5;
            font-family: 'Courier New', monospace;
        }
        
        .account-type {
            font-size: 8pt;
            color: #64748b;
            font-weight: normal;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        table thead {
            background-color: #f1f5f9;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        
        table th {
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
            border-right: 1px solid #e2e8f0;
        }
        
        table td {
            padding: 5px 4px;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            font-size: 8pt;
        }
        
        .opening-balance-row {
            background-color: #eff6ff;
            font-weight: bold;
        }
        
        .account-total-row {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 2px solid #94a3b8;
        }
        
        .grand-total-row {
            background-color: #e2e8f0;
            font-weight: bold;
            border-top: 3px solid #64748b;
            font-size: 10pt;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .amount {
            font-family: 'Courier New', monospace;
        }
        
        .debit {
            color: #059669;
        }
        
        .credit {
            color: #DC2626;
        }
        
        .balance-positive {
            color: #2563eb;
        }
        
        .balance-negative {
            color: #ea580c;
        }
        
        .reference {
            font-family: 'Courier New', monospace;
            font-size: 7pt;
            color: #4F46E5;
        }
        
        .book-name {
            font-size: 7pt;
            color: #64748b;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
        
        .summary-box {
            margin-top: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-weight: bold;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .spacer {
            height: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $filters['company_name'] ?? 'Nama Perusahaan' }}</h1>
        <h2>Buku Besar (General Ledger)</h2>
        @if(isset($filters['outlet_name']))
            <p style="font-size: 10pt; color: #666;">{{ $filters['outlet_name'] }}</p>
        @endif
    </div>

    @if(!empty($filters))
    <div class="filter-info">
        <p><strong>Informasi Laporan:</strong></p>
        @if(isset($filters['start_date']) && isset($filters['end_date']))
            <p>Periode: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</p>
        @endif
        @if(isset($filters['account_name']))
            <p>Akun: {{ $filters['account_name'] }}</p>
        @endif
    </div>
    @endif

    @php
        $grandTotalDebit = 0;
        $grandTotalCredit = 0;
        $grandBalance = 0;
    @endphp

    @if(isset($data['ledger_entries']) && count($data['ledger_entries']) > 0)
        @foreach($data['ledger_entries'] as $accountEntry)
            {{-- Account Header --}}
            <div class="account-header">
                <span class="account-code">{{ $accountEntry['account_code'] }}</span>
                <span>{{ $accountEntry['account_name'] }}</span>
                <span class="account-type">
                    ({{ ucfirst($accountEntry['account_type']) }} - {{ $accountEntry['transaction_count'] }} transaksi)
                </span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">#</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 12%;">Referensi</th>
                        <th style="width: 28%;">Keterangan</th>
                        <th style="width: 15%;" class="text-right">Debit</th>
                        <th style="width: 15%;" class="text-right">Kredit</th>
                        <th style="width: 15%;" class="text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Opening Balance --}}
                    <tr class="opening-balance-row">
                        <td class="text-center">-</td>
                        <td>{{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}</td>
                        <td><span class="reference">SALDO-AWAL</span></td>
                        <td>Saldo Awal Periode</td>
                        <td class="text-right amount">
                            @if($accountEntry['opening_balance'] > 0)
                                <span class="debit">{{ number_format($accountEntry['opening_balance'], 0, ',', '.') }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td class="text-right amount">
                            @if($accountEntry['opening_balance'] < 0)
                                <span class="credit">{{ number_format(abs($accountEntry['opening_balance']), 0, ',', '.') }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td class="text-right amount {{ $accountEntry['opening_balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                            {{ number_format($accountEntry['opening_balance'], 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Transactions --}}
                    @foreach($accountEntry['transactions'] as $index => $transaction)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}</td>
                            <td>
                                <span class="reference">{{ $transaction['reference'] }}</span>
                                @if(isset($transaction['book_name']))
                                    <br><span class="book-name">{{ $transaction['book_name'] }}</span>
                                @endif
                            </td>
                            <td style="font-size: 8pt;">{{ $transaction['description'] }}</td>
                            <td class="text-right amount">
                                @if($transaction['debit'] > 0)
                                    <span class="debit">{{ number_format($transaction['debit'], 0, ',', '.') }}</span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td class="text-right amount">
                                @if($transaction['credit'] > 0)
                                    <span class="credit">{{ number_format($transaction['credit'], 0, ',', '.') }}</span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td class="text-right amount {{ $transaction['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                {{ number_format($transaction['balance'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- Account Total --}}
                    <tr class="account-total-row">
                        <td colspan="4" class="text-right">
                            Total <span class="account-code">{{ $accountEntry['account_code'] }}</span>
                        </td>
                        <td class="text-right amount debit">
                            {{ number_format($accountEntry['total_debit'], 0, ',', '.') }}
                        </td>
                        <td class="text-right amount credit">
                            {{ number_format($accountEntry['total_credit'], 0, ',', '.') }}
                        </td>
                        <td class="text-right amount {{ $accountEntry['ending_balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                            {{ number_format($accountEntry['ending_balance'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="spacer"></div>

            @php
                $grandTotalDebit += $accountEntry['total_debit'];
                $grandTotalCredit += $accountEntry['total_credit'];
            @endphp
        @endforeach

        {{-- Grand Total --}}
        <table>
            <tbody>
                <tr class="grand-total-row">
                    <td colspan="4" class="text-right" style="padding: 8px;">TOTAL BUKU BESAR</td>
                    <td class="text-right amount debit" style="padding: 8px;">
                        {{ number_format($data['summary']['total_debit'], 0, ',', '.') }}
                    </td>
                    <td class="text-right amount credit" style="padding: 8px;">
                        {{ number_format($data['summary']['total_credit'], 0, ',', '.') }}
                    </td>
                    <td class="text-right amount {{ $data['summary']['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}" style="padding: 8px;">
                        {{ number_format($data['summary']['balance'], 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Summary Box --}}
        <div class="summary-box">
            <div class="summary-row">
                <span>Total Debit:</span>
                <span class="amount debit">Rp {{ number_format($data['summary']['total_debit'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Total Kredit:</span>
                <span class="amount credit">Rp {{ number_format($data['summary']['total_credit'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-row" style="border-top: 1px solid #999; padding-top: 5px; margin-top: 5px;">
                <span>Saldo:</span>
                <span class="amount {{ $data['summary']['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                    Rp {{ number_format($data['summary']['balance'], 0, ',', '.') }}
                </span>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #999;">
            <p>Tidak ada data transaksi untuk periode yang dipilih</p>
        </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
