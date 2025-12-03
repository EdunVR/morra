<?php
/**
 * Test Script untuk Piutang Routes
 * 
 * Jalankan dengan: php artisan tinker
 * Kemudian copy-paste kode ini
 */

// Test 1: Check if all routes exist
echo "=== Testing Piutang Routes ===\n\n";

$routes = [
    'finance.outlets.data',
    'finance.piutang.index',
    'finance.piutang.data',
    'finance.piutang.detail',
    'finance.piutang.mark-paid',
    'finance.piutang.get-sales-invoice-id',
    'penjualan.invoice.index',
    'penjualan.invoice.print',
];

foreach ($routes as $routeName) {
    try {
        if (strpos($routeName, ':id') !== false || strpos($routeName, 'detail') !== false || strpos($routeName, 'mark-paid') !== false || strpos($routeName, 'get-sales-invoice-id') !== false || strpos($routeName, 'print') !== false) {
            // Routes with parameters
            $url = route($routeName, ['id' => 1]);
        } else {
            $url = route($routeName);
        }
        echo "✅ {$routeName}: {$url}\n";
    } catch (\Exception $e) {
        echo "❌ {$routeName}: Route not found!\n";
    }
}

echo "\n=== Test Mapping Penjualan ID to Sales Invoice ID ===\n\n";

// Test 2: Check if getSalesInvoiceId method exists
try {
    $controller = new \App\Http\Controllers\FinanceAccountantController();
    echo "✅ FinanceAccountantController exists\n";
    
    if (method_exists($controller, 'getSalesInvoiceId')) {
        echo "✅ getSalesInvoiceId method exists\n";
    } else {
        echo "❌ getSalesInvoiceId method not found\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Database Connection ===\n\n";

// Test 3: Check if tables exist
try {
    $piutangExists = \Schema::hasTable('piutang');
    $salesInvoiceExists = \Schema::hasTable('sales_invoice');
    $penjualanExists = \Schema::hasTable('penjualan');
    
    echo "✅ Table 'piutang' exists: " . ($piutangExists ? 'Yes' : 'No') . "\n";
    echo "✅ Table 'sales_invoice' exists: " . ($salesInvoiceExists ? 'Yes' : 'No') . "\n";
    echo "✅ Table 'penjualan' exists: " . ($penjualanExists ? 'Yes' : 'No') . "\n";
    
    if ($piutangExists) {
        $piutangCount = \DB::table('piutang')->count();
        echo "   - Total piutang records: {$piutangCount}\n";
    }
    
    if ($salesInvoiceExists) {
        $invoiceCount = \DB::table('sales_invoice')->count();
        echo "   - Total sales_invoice records: {$invoiceCount}\n";
    }
} catch (\Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Sample Data ===\n\n";

// Test 4: Get sample piutang with penjualan mapping
try {
    $samplePiutang = \DB::table('piutang')
        ->join('penjualan', 'piutang.id_penjualan', '=', 'penjualan.id_penjualan')
        ->leftJoin('sales_invoice', 'penjualan.id_penjualan', '=', 'sales_invoice.id_penjualan')
        ->select(
            'piutang.id_piutang',
            'piutang.nama as invoice_number',
            'piutang.id_penjualan',
            'sales_invoice.id_sales_invoice',
            'piutang.jumlah_piutang',
            'piutang.status'
        )
        ->limit(5)
        ->get();
    
    if ($samplePiutang->count() > 0) {
        echo "✅ Sample piutang data found:\n";
        foreach ($samplePiutang as $item) {
            echo "   - Piutang ID: {$item->id_piutang}\n";
            echo "     Invoice: {$item->invoice_number}\n";
            echo "     Penjualan ID: {$item->id_penjualan}\n";
            echo "     Sales Invoice ID: " . ($item->id_sales_invoice ?? 'NULL') . "\n";
            echo "     Amount: Rp " . number_format($item->jumlah_piutang, 0, ',', '.') . "\n";
            echo "     Status: {$item->status}\n\n";
        }
    } else {
        echo "⚠️  No piutang data found\n";
    }
} catch (\Exception $e) {
    echo "❌ Error fetching sample data: " . $e->getMessage() . "\n";
}

echo "\n=== All Tests Completed ===\n";
