<?php

/**
 * Script untuk cek dan seed bank accounts jika belum ada
 * Run: php check_and_seed_bank_accounts.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Company Bank Accounts ===\n\n";

// Check if table exists
$tableExists = DB::select("SHOW TABLES LIKE 'company_bank_accounts'");

if (empty($tableExists)) {
    echo "❌ Table 'company_bank_accounts' does not exist!\n";
    echo "Please run migration first.\n";
    exit(1);
}

echo "✅ Table 'company_bank_accounts' exists\n\n";

// Check existing data
$existingAccounts = DB::table('company_bank_accounts')->get();

echo "Current bank accounts: " . $existingAccounts->count() . "\n";

if ($existingAccounts->count() > 0) {
    echo "\nExisting accounts:\n";
    foreach ($existingAccounts as $account) {
        echo "  - ID: {$account->id_company_bank_account}\n";
        echo "    Outlet: {$account->id_outlet}\n";
        echo "    Bank: {$account->bank_name}\n";
        echo "    Account: {$account->account_number}\n";
        echo "    Holder: {$account->account_holder_name}\n";
        echo "    Active: " . ($account->is_active ? 'Yes' : 'No') . "\n\n";
    }
} else {
    echo "\n⚠️  No bank accounts found!\n\n";
    echo "Would you like to seed sample data? (y/n): ";
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    
    if(trim($line) == 'y' || trim($line) == 'Y'){
        echo "\nSeeding sample bank accounts...\n";
        
        // Get outlets
        $outlets = DB::table('outlets')->get();
        
        if ($outlets->count() == 0) {
            echo "❌ No outlets found! Please create outlets first.\n";
            exit(1);
        }
        
        echo "Found " . $outlets->count() . " outlet(s)\n";
        
        // Get company name from settings
        $setting = DB::table('setting')->first();
        $companyName = $setting->nama_perusahaan ?? 'PT Contoh Perusahaan';
        
        // Sample banks for each outlet
        $sampleBanks = [
            [
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'branch_name' => 'KCP Sudirman',
                'sort_order' => 1
            ],
            [
                'bank_name' => 'Mandiri',
                'account_number' => '9876543210',
                'branch_name' => null,
                'sort_order' => 2
            ],
            [
                'bank_name' => 'BNI',
                'account_number' => '5555666677',
                'branch_name' => 'Cabang Utama',
                'sort_order' => 3
            ]
        ];
        
        $inserted = 0;
        foreach ($outlets as $outlet) {
            echo "\nAdding banks for outlet: {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
            
            foreach ($sampleBanks as $bank) {
                DB::table('company_bank_accounts')->insert([
                    'id_outlet' => $outlet->id_outlet,
                    'bank_name' => $bank['bank_name'],
                    'account_number' => $bank['account_number'],
                    'account_holder_name' => $companyName,
                    'branch_name' => $bank['branch_name'],
                    'currency' => 'IDR',
                    'is_active' => true,
                    'sort_order' => $bank['sort_order'],
                    'notes' => 'Sample data - Please update with real account information',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                echo "  ✅ Added: {$bank['bank_name']} - {$bank['account_number']}\n";
                $inserted++;
            }
        }
        
        echo "\n✅ Successfully inserted {$inserted} bank account(s)!\n";
        echo "\n⚠️  IMPORTANT: Please update these accounts with real information!\n";
        echo "You can manage bank accounts in the admin panel.\n";
        
    } else {
        echo "Skipped seeding.\n";
    }
}

echo "\n=== Done ===\n";
