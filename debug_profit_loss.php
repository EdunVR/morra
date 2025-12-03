<?php
// Debug script untuk cek data profit loss
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Carbon\Carbon;

$outletId = 1;
$startDate = '2025-11-01';
$endDate = '2025-11-30';

echo "=== DEBUG PROFIT LOSS DATA ===\n\n";

// 1. Cek Chart of Accounts
echo "1. CHART OF ACCOUNTS (Revenue):\n";
$revenueAccounts = ChartOfAccount::where('outlet_id', $outletId)
    ->where('type', 'revenue')
    ->where('status', 'active')
    ->get();

echo "Total revenue accounts: " . $revenueAccounts->count() . "\n";
foreach ($revenueAccounts as $acc) {
    echo "  - {$acc->code} | {$acc->name} | Parent: " . ($acc->parent_id ?? 'NULL') . "\n";
}

echo "\n2. CHART OF ACCOUNTS (Expense):\n";
$expenseAccounts = ChartOfAccount::where('outlet_id', $outletId)
    ->where('type', 'expense')
    ->where('status', 'active')
    ->get();

echo "Total expense accounts: " . $expenseAccounts->count() . "\n";
foreach ($expenseAccounts as $acc) {
    echo "  - {$acc->code} | {$acc->name} | Parent: " . ($acc->parent_id ?? 'NULL') . "\n";
}

// 2. Cek Journal Entries
echo "\n3. JOURNAL ENTRIES (Posted):\n";
$journalEntries = JournalEntry::where('outlet_id', $outletId)
    ->where('status', 'posted')
    ->whereBetween('transaction_date', [$startDate, $endDate])
    ->get();

echo "Total journal entries: " . $journalEntries->count() . "\n";
foreach ($journalEntries->take(5) as $entry) {
    echo "  - {$entry->transaction_number} | {$entry->transaction_date} | {$entry->description}\n";
}

// 3. Cek Journal Entry Details untuk revenue accounts
if ($revenueAccounts->count() > 0) {
    echo "\n4. JOURNAL ENTRY DETAILS (Revenue Accounts):\n";
    $revenueAccountIds = $revenueAccounts->pluck('id')->toArray();
    
    $details = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId, $startDate, $endDate) {
            $query->where('outlet_id', $outletId)
                ->where('status', 'posted')
                ->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->whereIn('account_id', $revenueAccountIds)
        ->with(['account', 'journalEntry'])
        ->get();
    
    echo "Total details for revenue accounts: " . $details->count() . "\n";
    foreach ($details->take(10) as $detail) {
        $account = $detail->account;
        echo "  - {$account->code} | {$account->name} | Debit: {$detail->debit} | Credit: {$detail->credit}\n";
    }
    
    // Calculate total
    $totalCredit = $details->sum('credit');
    $totalDebit = $details->sum('debit');
    $netRevenue = $totalCredit - $totalDebit;
    
    echo "\n  Summary:\n";
    echo "  Total Credit: " . number_format($totalCredit, 2) . "\n";
    echo "  Total Debit: " . number_format($totalDebit, 2) . "\n";
    echo "  Net Revenue: " . number_format($netRevenue, 2) . "\n";
}

// 4. Test API endpoint
echo "\n5. TESTING API ENDPOINT:\n";
echo "URL: /finance/profit-loss/data\n";
echo "Params: outlet_id={$outletId}, start_date={$startDate}, end_date={$endDate}\n";
echo "\nSilakan test di browser atau Postman untuk melihat response lengkap.\n";

echo "\n=== END DEBUG ===\n";
