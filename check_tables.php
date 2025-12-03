<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $tables = DB::select('SHOW TABLES');
    
    echo "Tabel yang ada di database:\n";
    echo "============================\n";
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- $tableName\n";
    }
    
    echo "\nTotal: " . count($tables) . " tabel\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
