<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChartOfAccount;

$acc = ChartOfAccount::where('code', '4000')->first();
echo "Parent Account:\n";
echo "ID: {$acc->id}\n";
echo "Code: {$acc->code}\n";
echo "Name: {$acc->name}\n";
echo "Children count: " . $acc->children->count() . "\n\n";

echo "Children:\n";
foreach($acc->children as $child) {
    echo "  - ID: {$child->id} | Code: {$child->code} | Name: {$child->name} | Parent ID: {$child->parent_id}\n";
}
