<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->no_invoice }}</title>
    <style>
        /* Tambahkan style untuk bank accounts */
        .bank-accounts {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .bank-account-item {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #ddd;
        }
        .bank-account-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .bank-name {
            font-weight: bold;
            color: #2c3e50;
        }
        .account-number {
            font-family: monospace;
            color: #34495e;
        }
        .account-holder {
            color: #7f8c8d;
            font-style: italic;
        }
        .bank-section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Perusahaan -->
        <div class="header" style="text-align: center; margin-bottom: 20px;">
            <h1 style="margin: 0; color: #2c3e50;">{{ $setting->nama_perusahaan ?? 'Nama Perusahaan' }}</h1>
            <p style="margin: 5px 0; color: #7f8c8d;">{{ $setting->alamat ?? 'Alamat Perusahaan' }}</p>
            <p style="margin: 5px 0; color: #7f8c8d;">Telp: {{ $setting->telepon ?? '-' }} | Email: {{ $setting->email ?? '-' }}</p>
        </div>

        <!-- Informasi Rekening Perusahaan -->
        @if(isset($bankAccounts) && $bankAccounts->count() > 0)
        <div class="bank-accounts">
            <div class="bank-section-title">Rekening Pembayaran</div>
            @foreach($bankAccounts as $bank)
            <div class="bank-account-item">
                <span class="bank-name">{{ $bank->bank_name }}</span>
                @if($bank->branch_name)
                <span style="color: #95a5a6;">({{ $bank->branch_name }})</span>
                @endif
                <br>
                <span class="account-number">No. Rek: {{ $bank->getFormattedAccountNumber() }}</span>
                <br>
                <span class="account-holder">a/n {{ $bank->account_holder }}</span>
            </div>
            @endforeach
        </div>
        @else
        <!-- Fallback jika tidak ada bank accounts -->
        <div class="bank-accounts" style="background: #fff3cd; border-color: #ffc107;">
            <div class="bank-section-title" style="color: #856404; border-color: #ffc107;">Informasi Pembayaran</div>
            <div style="color: #856404; font-size: 12px;">
                <em>Silakan hubungi kami untuk informasi rekening pembayaran.</em>
                <br>
                <small style="color: #6c757d;">
                    (Outlet ID: {{ $invoice->id_outlet ?? 'N/A' }} - 
                    @if(isset($bankAccounts))
                        {{ $bankAccounts->count() }} rekening ditemukan
                    @else
                        Data rekening tidak tersedia
                    @endif)
                </small>
            </div>
        </div>
        @endif

        <!-- Informasi Invoice -->
        <div class="invoice-info" style="margin-bottom: 20px;">
            <h2 style="text-align: center; color: #e74c3c; margin-bottom: 20px;">INVOICE</h2>
            
            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <strong>No. Invoice:</strong> {{ $invoice->no_invoice }}<br>
                        <strong>Tanggal:</strong> {{ $invoice->tanggal->format('d/m/Y') }}<br>
                        <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d/m/Y') }}
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <strong>Kepada:</strong><br>
                        @if($invoice->member)
                            {{ $invoice->member->nama }}<br>
                            {{ $invoice->member->alamat }}<br>
                            Telp: {{ $invoice->member->telepon }}
                        @elseif($invoice->prospek)
                            {{ $invoice->prospek->nama }}<br>
                            {{ $invoice->prospek->alamat }}<br>
                            Telp: {{ $invoice->prospek->telepon }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabel Items -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #34495e; color: white;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">No</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Deskripsi</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Qty</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Satuan</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Harga</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Diskon</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->deskripsi }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ number_format($item->kuantitas, 2) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $item->satuan }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        @if($item->diskon > 0)
                        {{ number_format($item->diskon * $item->kuantitas, 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total -->
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 5px;"><strong>Subtotal:</strong></td>
                            <td style="padding: 5px; text-align: right;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @if($invoice->total_diskon > 0)
                        <tr>
                            <td style="padding: 5px;"><strong>Total Diskon:</strong></td>
                            <td style="padding: 5px; text-align: right;">- Rp {{ number_format($invoice->total_diskon, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr style="background-color: #ecf0f1;">
                            <td style="padding: 8px;"><strong>TOTAL:</strong></td>
                            <td style="padding: 8px; text-align: right; font-weight: bold;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Informasi Pembayaran untuk Transfer -->
        @if($invoice->jenis_pembayaran === 'transfer' && $invoice->hasBuktiTransfer())
        <div style="margin-top: 20px; padding: 10px; border: 1px solid #27ae60; background: #d5f4e6; border-radius: 5px;">
            <strong>Pembayaran telah diterima via Transfer</strong><br>
            Bank: {{ $invoice->nama_bank }} | Pengirim: {{ $invoice->nama_pengirim }}<br>
            Jumlah: Rp {{ number_format($invoice->jumlah_transfer, 0, ',', '.') }} | Tanggal: {{ $invoice->tanggal_pembayaran->format('d/m/Y') }}
        </div>
        @endif

        <!-- Keterangan -->
        @if($invoice->keterangan)
        <div style="margin-top: 20px;">
            <strong>Keterangan:</strong><br>
            {{ $invoice->keterangan }}
        </div>
        @endif

        <!-- Footer -->
        <div style="margin-top: 40px; text-align: right;">
            <div style="margin-bottom: 60px;">
                <br><br>
                <strong>{{ $setting->kota ?? 'Kota' }}, {{ $invoice->tanggal->format('d F Y') }}</strong><br>
                <br><br><br>
                <strong>{{ auth()->user()->name ?? 'Admin' }}</strong><br>
                {{ $setting->jabatan ?? 'Finance Manager' }}
            </div>
        </div>

        <!-- Watermark untuk invoice lunas -->
        @if($invoice->status === 'lunas')
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(46, 204, 113, 0.1); font-weight: bold; z-index: -1;">
            LUNAS
        </div>
        @endif
    </div>
</body>
</html>
