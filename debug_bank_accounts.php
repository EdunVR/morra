<?php

/**
 * Debug script untuk cek bank accounts
 * Run: php debug_bank_accounts.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Debugging Company Bank Accounts ===\n\n";

// 1. Check table structure
echo "1. Table Structure:\n";
$columns = DB::select("DESCRIBE company_bank_accounts");
foreach ($columns as $col) {
    echo "   - {$col->Field} ({$col->Type}) " . ($col->Null == 'NO' ? 'NOT NULL' : 'NULL') . "\n";
}

// 2. Check all data
echo "\n2. All Records:\n";
$all = DB::table('company_bank_accounts')->get();
echo "   Total: {$all->count()} records\n\n";

if ($all->count() > 0) {
    foreach ($all as $record) {
        echo "   Record ID: {$record->id_company_bank_account}\n";
        
        // Check all possible outlet column names
        $outletValue = null;
        if (isset($record->id_outlet)) {
            $outletValue = $record->id_outlet;
            echo "   - id_outlet: {$outletValue} (type: " . gettype($outletValue) . ")\n";
        }
        if (isset($record->outlet_id)) {
            $outletValue = $record->outlet_id;
            echo "   - outlet_id: {$outletValue} (type: " . gettype($outletValue) . ")\n";
        }
        
        echo "   - bank_name: {$record->bank_name}\n";
        echo "   - account_number: {$record->account_number}\n";
        echo "   - is_active: {$record->is_active} (type: " . gettype($record->is_active) . ")\n";
        echo "\n";
    }
}

// 3. Test queries
echo "3. Test Queries:\n";

echo "\n   Query 1: WHERE id_outlet = 1 AND is_active = true\n";
$test1 = DB::table('company_bank_accounts')
    ->where('id_outlet', 1)
    ->where('is_active', true)
    ->get();
echo "   Result: {$test1->count()} records\n";

echo "\n   Query 2: WHERE id_outlet = 1 AND is_active = 1\n";
$test2 = DB::table('company_bank_accounts')
    ->where('id_outlet', 1)
    ->where('is_active', 1)
    ->get();
echo "   Result: {$test2->count()} records\n";

echo "\n   Query 3: WHERE id_outlet = '1' AND is_active = 1\n";
$test3 = DB::table('company_bank_accounts')
    ->where('id_outlet', '1')
    ->where('is_active', 1)
    ->get();
echo "   Result: {$test3->count()} records\n";

echo "\n   Query 4: WHERE id_outlet = 1 (no is_active filter)\n";
$test4 = DB::table('company_bank_accounts')
    ->where('id_outlet', 1)
    ->get();
echo "   Result: {$test4->count()} records\n";

// 4. Check using Model
echo "\n4. Using Eloquent Model:\n";
try {
    $modelTest = \App\Models\CompanyBankAccount::where('id_outlet', 1)->get();
    echo "   Model query result: {$modelTest->count()} records\n";
    
    if ($modelTest->count() > 0) {
        $first = $modelTest->first();
        echo "   First record:\n";
        echo "   - ID: {$first->id_company_bank_account}\n";
        echo "   - Outlet: {$first->id_outlet}\n";
        echo "   - Bank: {$first->bank_name}\n";
        echo "   - Active: {$first->is_active}\n";
    }
} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Done ===\n";
