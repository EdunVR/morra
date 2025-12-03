<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Neraca - {{ $outlet_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
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
            font-size: 18px;
            margin-bottom: 5px;
            color: #1e40af;
        }
        
        .header h2 {
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .info-box {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f3f4f6;
            border-radius: 4px;
        }
        
        .info-box table {
            width: 100%;
        }
        
        .info-box td {
            padding: 2px 5px;
            font-size: 10px;
        }
        
        .info-box td:first-child {
            width: 120px;
            font-weight: bold;
        }
        
        .content {
            margin-top: 20px;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #1e40af;
            color: white;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .section-title.liability {
            background-color: #7c3aed;
        }
        
        .account-list {
            margin-left: 10px;
        }
        
        .account-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .account-item.parent {
            font-weight: bold;
            background-color: #f9fafb;
        }
        
        .account-item.child {
            margin-left: 20px;
            font-size: 10px;
        }
        
        .account-code {
            color: #6b7280;
            margin-right: 10px;
            font-size: 9px;
        }
        
        .account-balance {
            text-align: right;
            white-space: nowrap;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            margin-top: 10px;
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid #333;
        }
        
        .grand-total {
            background-color: #1e40af;
            color: white;
        }
        
        .grand-total.liability {
            background-color: #7c3aed;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        
        .balance-check {
            margin-top: 20px;
            padding: 10px;
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 4px;
            text-align: center;
        }
        
        .balance-check.balanced {
            background-color: #d1fae5;
            border-color: #10b981;
        }
        
        .two-column {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 0 1%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company_name }}</h1>
        <h2>NERACA (BALANCE SHEET)</h2>
        <p>Per Tanggal: {{ \Carbon\Carbon::parse($end_date)->format('d F Y') }}</p>
        <p>{{ $outlet_name }}</p>
    </div>
    
    <div class="info-box">
        <table>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ $print_date }}</td>
            </tr>
            <tr>
                <td>Outlet</td>
                <td>: {{ $outlet_name }}</td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>: Per {{ \Carbon\Carbon::parse($end_date)->format('d F Y') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="two-column">
        {{-- ASET Column --}}
        <div class="column">
            <div class="section">
                <div class="section-title">ASET</div>
                <div class="account-list">
                    @foreach($assets as $asset)
                        <div class="account-item parent">
                            <div>
                                <span class="account-code">{{ $asset['code'] }}</span>
                                <span>{{ $asset['name'] }}</span>
                            </div>
                            <div class="account-balance">
                                Rp {{ number_format($asset['balance'], 0, ',', '.') }}
                            </div>
                        </div>
                        
                        @if(!empty($asset['children']))
                            @foreach($asset['children'] as $child)
                                <div class="account-item child">
                                    <div>
                                        <span class="account-code">{{ $child['code'] }}</span>
                                        <span>{{ $child['name'] }}</span>
                                    </div>
                                    <div class="account-balance">
                                        Rp {{ number_format($child['balance'], 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                </div>
                
                <div class="total-row grand-total">
                    <span>TOTAL ASET</span>
                    <span>Rp {{ number_format($totals['total_assets'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        {{-- KEWAJIBAN & EKUITAS Column --}}
        <div class="column">
            {{-- KEWAJIBAN --}}
            <div class="section">
                <div class="section-title liability">KEWAJIBAN</div>
                <div class="account-list">
                    @foreach($liabilities as $liability)
                        <div class="account-item parent">
                            <div>
                                <span class="account-code">{{ $liability['code'] }}</span>
                                <span>{{ $liability['name'] }}</span>
                            </div>
                            <div class="account-balance">
                                Rp {{ number_format($liability['balance'], 0, ',', '.') }}
                            </div>
                        </div>
                        
                        @if(!empty($liability['children']))
                            @foreach($liability['children'] as $child)
                                <div class="account-item child">
                                    <div>
                                        <span class="account-code">{{ $child['code'] }}</span>
                                        <span>{{ $child['name'] }}</span>
                                    </div>
                                    <div class="account-balance">
                                        Rp {{ number_format($child['balance'], 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                </div>
                
                <div class="total-row">
                    <span>Total Kewajiban</span>
                    <span>Rp {{ number_format($totals['total_liabilities'], 0, ',', '.') }}</span>
                </div>
            </div>
            
            {{-- EKUITAS --}}
            <div class="section">
                <div class="section-title liability">EKUITAS</div>
                <div class="account-list">
                    @foreach($equity as $eq)
                        <div class="account-item parent">
                            <div>
                                <span class="account-code">{{ $eq['code'] }}</span>
                                <span>{{ $eq['name'] }}</span>
                            </div>
                            <div class="account-balance">
                                Rp {{ number_format($eq['balance'], 0, ',', '.') }}
                            </div>
                        </div>
                        
                        @if(!empty($eq['children']))
                            @foreach($eq['children'] as $child)
                                <div class="account-item child">
                                    <div>
                                        <span class="account-code">{{ $child['code'] }}</span>
                                        <span>{{ $child['name'] }}</span>
                                    </div>
                                    <div class="account-balance">
                                        Rp {{ number_format($child['balance'], 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                    
                    {{-- Laba Ditahan --}}
                    <div class="account-item parent">
                        <div>
                            <span class="account-code">-</span>
                            <span>Laba Ditahan</span>
                        </div>
                        <div class="account-balance">
                            Rp {{ number_format($retained_earnings, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                
                <div class="total-row">
                    <span>Total Ekuitas</span>
                    <span>Rp {{ number_format($totals['total_equity'], 0, ',', '.') }}</span>
                </div>
                
                <div class="total-row grand-total liability">
                    <span>TOTAL KEWAJIBAN & EKUITAS</span>
                    <span>Rp {{ number_format($totals['total_liabilities_and_equity'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    @if($totals['is_balanced'])
        <div class="balance-check balanced">
            <strong>✓ Neraca Balance</strong>
        </div>
    @else
        <div class="balance-check">
            <strong>⚠ Neraca Tidak Balance</strong><br>
            Selisih: Rp {{ number_format(abs($totals['difference']), 0, ',', '.') }}
        </div>
    @endif
    
    <div class="footer">
        <p>Dicetak pada: {{ $print_date }}</p>
        <p>{{ $company_name }} - {{ $outlet_name }}</p>
    </div>
</body>
</html>
