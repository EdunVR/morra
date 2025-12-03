<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get all models
$modelFiles = glob('app/Models/*.php');
$models = [];
foreach ($modelFiles as $file) {
    $basename = basename($file, '.php');
    if ($basename !== 'Model ProspekFieldSetting') { // Skip invalid filename
        $models[] = $basename;
    }
}

// Get all tables from database
$tables = DB::select('SHOW TABLES');
$existingTables = [];
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    $existingTables[] = $tableName;
}

// Model to table name mapping (common patterns)
function modelToTableName($modelName) {
    // Special cases
    $specialCases = [
        'User' => 'users',
        'Outlet' => 'outlets',
        'Kategori' => 'kategori',
        'Satuan' => 'satuan',
        'Produk' => 'produk',
        'Supplier' => 'supplier',
        'Bahan' => 'bahan',
        'Member' => 'member',
        'Penjualan' => 'penjualan',
        'PenjualanDetail' => 'penjualan_detail',
        'Pembelian' => 'pembelian',
        'PembelianDetail' => 'pembelian_detail',
        'Inventori' => 'inventori',
        'InventoriDetail' => 'inventori_detail',
        'Piutang' => 'piutang',
        'Hutang' => 'hutang',
        'Gerobak' => 'gerobak',
        'Pengeluaran' => 'pengeluaran',
        'Produksi' => 'produksi',
        'ProduksiDetail' => 'produksi_detail',
        'Prospek' => 'prospek',
        'ProspekTimeline' => 'prospek_timeline',
    ];
    
    if (isset($specialCases[$modelName])) {
        return $specialCases[$modelName];
    }
    
    // Convert PascalCase to snake_case and pluralize
    $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));
    
    // Simple pluralization
    if (!str_ends_with($tableName, 's')) {
        $tableName .= 's';
    }
    
    return $tableName;
}

echo "Checking for missing tables...\n\n";
echo "Models found: " . count($models) . "\n";
echo "Tables in database: " . count($existingTables) . "\n\n";

$missingTables = [];
foreach ($models as $model) {
    $expectedTable = modelToTableName($model);
    if (!in_array($expectedTable, $existingTables)) {
        $missingTables[$model] = $expectedTable;
    }
}

if (empty($missingTables)) {
    echo "✓ All models have corresponding tables!\n";
} else {
    echo "Missing tables for these models:\n";
    echo "=================================\n";
    foreach ($missingTables as $model => $table) {
        echo "- {$model} => {$table}\n";
    }
    echo "\nTotal missing: " . count($missingTables) . "\n";
}
