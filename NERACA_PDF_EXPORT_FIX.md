# Fix Neraca PDF Export - Missing `is_balanced` Key

## Error yang Terjadi

```json
{
    "success": false,
    "message": "Gagal mengekspor neraca: Undefined array key \"is_balanced\" (View: C:\\xampp\\htdocs\\MORRA\\resources\\views\\admin\\finance\\neraca\\pdf.blade.php)"
}
```

## Penyebab

Method `exportNeracaPDF()` tidak mengirim key `is_balanced` dan `difference` ke view PDF, padahal view PDF menggunakan key tersebut untuk menampilkan status balance.

**Code di View PDF:**

```php
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
```

## Solusi

Tambahkan perhitungan `is_balanced` dan `difference` di method `exportNeracaPDF()` sebelum data dikirim ke view.

### Sebelum:

```php
public function exportNeracaPDF(Request $request)
{
    // ... get data ...

    $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

    $data = [
        'assets' => $assets,
        'liabilities' => $liabilities,
        'equity' => $equity,
        'retained_earnings' => $retainedEarnings,
        'totals' => [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_and_equity' => $totalLiabilitiesAndEquity
            // ❌ Missing: is_balanced & difference
        ],
        'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
        'end_date' => $endDate,
        'company_name' => config('app.name', 'PT. NAMA PERUSAHAAN'),
        'print_date' => now()->format('d/m/Y H:i')
    ];

    $pdf = Pdf::loadView('admin.finance.neraca.pdf', $data)
        ->setPaper('a4', 'portrait');

    return $pdf->download($filename);
}
```

### Sesudah:

```php
public function exportNeracaPDF(Request $request)
{
    // ... get data ...

    $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

    // ✅ Calculate balance check
    $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;
    $difference = $totalAssets - $totalLiabilitiesAndEquity;

    $data = [
        'assets' => $assets,
        'liabilities' => $liabilities,
        'equity' => $equity,
        'retained_earnings' => $retainedEarnings,
        'totals' => [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
            'is_balanced' => $isBalanced,      // ✅ Added
            'difference' => $difference         // ✅ Added
        ],
        'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
        'end_date' => $endDate,
        'company_name' => config('app.name', 'PT. NAMA PERUSAHAAN'),
        'print_date' => now()->format('d/m/Y H:i')
    ];

    $pdf = Pdf::loadView('admin.finance.neraca.pdf', $data)
        ->setPaper('a4', 'portrait');

    return $pdf->download($filename);
}
```

## Penjelasan

### 1. **Balance Check Logic**

```php
$isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;
```

-   Menggunakan `abs()` untuk mendapatkan nilai absolut
-   Toleransi 0.01 untuk menghindari floating point precision issues
-   Return `true` jika selisih kurang dari 0.01 (praktis balance)

### 2. **Difference Calculation**

```php
$difference = $totalAssets - $totalLiabilitiesAndEquity;
```

-   Menghitung selisih antara Aset dan (Kewajiban + Ekuitas)
-   Nilai positif: Aset lebih besar
-   Nilai negatif: Kewajiban + Ekuitas lebih besar

### 3. **Display di PDF**

```php
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
```

## Konsistensi dengan Method Lain

Method `neracaData()` (untuk API) sudah memiliki logic yang sama:

```php
public function neracaData(Request $request): JsonResponse
{
    // ... get data ...

    // Check if balanced
    $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;
    $difference = $totalAssets - $totalLiabilitiesAndEquity;

    return response()->json([
        'success' => true,
        'data' => [
            // ...
            'totals' => [
                'total_assets' => $totalAssets,
                'total_liabilities' => $totalLiabilities,
                'total_equity' => $totalEquity,
                'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
                'is_balanced' => $isBalanced,
                'difference' => $difference
            ]
        ]
    ]);
}
```

Sekarang `exportNeracaPDF()` konsisten dengan `neracaData()`.

## Testing

### Manual Test

1. Buka halaman Neraca
2. Pilih outlet dan tanggal
3. Klik "Export" → "Export ke PDF"
4. PDF harus ter-download tanpa error
5. PDF harus menampilkan status balance:
    - ✓ Jika balance: "Neraca Balance"
    - ⚠ Jika tidak balance: "Neraca Tidak Balance" + selisih

### Test Cases

#### Case 1: Neraca Balance

```
Total Aset: Rp 10,000,000
Total Kewajiban: Rp 6,000,000
Total Ekuitas: Rp 4,000,000
Total Kewajiban + Ekuitas: Rp 10,000,000

Result: ✓ Neraca Balance
```

#### Case 2: Neraca Tidak Balance

```
Total Aset: Rp 10,000,000
Total Kewajiban: Rp 6,000,000
Total Ekuitas: Rp 3,500,000
Total Kewajiban + Ekuitas: Rp 9,500,000

Result: ⚠ Neraca Tidak Balance
Selisih: Rp 500,000
```

## Kesimpulan

✅ **Error fixed** - Key `is_balanced` dan `difference` sudah ditambahkan
✅ **Konsistensi** - Logic sama dengan method `neracaData()`
✅ **PDF Export** - Sekarang berfungsi dengan baik
✅ **Balance Check** - Status balance ditampilkan dengan benar di PDF

Export PDF Neraca sekarang berfungsi sempurna! 🎉
