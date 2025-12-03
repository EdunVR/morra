<?php

/**
 * Script untuk test semua route yang digunakan di sidebar
 * Jalankan dengan: php test_sidebar_routes.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Daftar route yang digunakan di sidebar
$routesToTest = [
    'admin.inventaris.index',
    'admin.pelanggan',
    'admin.penjualan.dashboard.index',
    'pembelian.purchase-order.index',
    'admin.produksi',
    'admin.rantai-pasok',
    'finance.accounting.index',
    'admin.sdm',
    'admin.service',
    'admin.investor',
    'admin.analisis',
    'admin.sistem',
    
    // Submenu Inventaris
    'admin.inventaris.outlet.index',
    'admin.inventaris.kategori.index',
    'admin.inventaris.satuan.index',
    'admin.inventaris.produk.index',
    'admin.inventaris.bahan.index',
    'admin.inventaris.inventori.index',
    'admin.inventaris.transfer-gudang.index',
    
    // Submenu CRM
    'admin.crm.tipe.index',
    'admin.crm.pelanggan.index',
    
    // Submenu Finance
    'admin.finance.rab.index',
    'finance.biaya.index',
    'finance.hutang.index',
    'finance.piutang.index',
    'finance.rekonsiliasi.index',
    'finance.akun.index',
    'finance.buku.index',
    'finance.saldo-awal.index',
    'finance.jurnal.index',
    'finance.aktiva.index',
    'finance.buku-besar.index',
    'finance.neraca.index',
    'finance.neraca-saldo.index',
    'finance.profit-loss.index',
    'finance.cashflow.index',
    
    // Submenu Penjualan
    'admin.penjualan.pos.index',
    'admin.penjualan.invoice.index',
    'admin.penjualan.laporan.index',
    'admin.penjualan.margin.index',
    'admin.penjualan.agen_gerobak.index',
    'admin.penjualan.agen.index',
    
    // Submenu Pembelian
    'pembelian.purchase-order.index',
    
    // Submenu Produksi
    'admin.produksi.produksi.index',
    
    // Submenu Investor
    'admin.investor.profil.index',
    
    // Submenu Sistem
    'admin.users.index',
    'admin.roles.index',
];

echo "===========================================\n";
echo "Testing Sidebar Routes\n";
echo "===========================================\n\n";

$passed = 0;
$failed = 0;
$failedRoutes = [];

foreach ($routesToTest as $routeName) {
    try {
        $url = route($routeName);
        echo "✅ {$routeName}\n";
        $passed++;
    } catch (\Exception $e) {
        echo "❌ {$routeName} - NOT DEFINED\n";
        $failed++;
        $failedRoutes[] = $routeName;
    }
}

echo "\n===========================================\n";
echo "Summary\n";
echo "===========================================\n";
echo "Total Routes: " . count($routesToTest) . "\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n";

if ($failed > 0) {
    echo "\n===========================================\n";
    echo "Failed Routes:\n";
    echo "===========================================\n";
    foreach ($failedRoutes as $route) {
        echo "- {$route}\n";
    }
    exit(1);
} else {
    echo "\n✅ All routes are defined correctly!\n";
    exit(0);
}
