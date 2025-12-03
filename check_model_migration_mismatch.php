<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking Model vs Migration Mismatches ===\n\n";

// Get all migrated tables
$migrations = DB::table('migrations')->pluck('migration')->toArray();
$migratedTables = [];

foreach ($migrations as $migration) {
    // Extract table name from migration filename
    if (preg_match('/create_(.+)_table/', $migration, $matches)) {
        $migratedTables[] = $matches[1];
    }
}

echo "Found " . count($migratedTables) . " migrated tables\n\n";

// Get all models
$modelPath = app_path('Models');
$modelFiles = glob($modelPath . '/*.php');

$mismatches = [];

foreach ($modelFiles as $modelFile) {
    $className = 'App\\Models\\' . basename($modelFile, '.php');
    
    if (!class_exists($className)) {
        continue;
    }

    try {
        $model = new $className;
        
        // Skip if model doesn't have table property or fillable
        if (!property_exists($model, 'table') && !method_exists($model, 'getTable')) {
            continue;
        }

        $tableName = $model->getTable();
        
        // Check if table exists in database
        if (!Schema::hasTable($tableName)) {
            echo "⚠️  Table '$tableName' for model '$className' does not exist in database\n";
            continue;
        }

        // Get fillable fields from model
        $fillable = $model->getFillable();
        
        if (empty($fillable)) {
            continue;
        }

        // Get actual columns from database
        $columns = Schema::getColumnListing($tableName);
        
        // Find missing columns
        $missingColumns = array_diff($fillable, $columns);
        
        if (!empty($missingColumns)) {
            $mismatches[] = [
                'model' => $className,
                'table' => $tableName,
                'missing_columns' => $missingColumns
            ];
            
            echo "❌ MISMATCH: $className (table: $tableName)\n";
            echo "   Missing columns: " . implode(', ', $missingColumns) . "\n\n";
        }
        
    } catch (\Exception $e) {
        echo "Error checking $className: " . $e->getMessage() . "\n";
    }
}

if (empty($mismatches)) {
    echo "\n✅ All models match their migrations!\n";
} else {
    echo "\n=== Summary ===\n";
    echo "Found " . count($mismatches) . " model(s) with missing columns\n\n";
    
    echo "=== Generating Fix Migrations ===\n\n";
    
    foreach ($mismatches as $mismatch) {
        $tableName = $mismatch['table'];
        $missingColumns = $mismatch['missing_columns'];
        
        $timestamp = date('Y_m_d_His', time() + array_search($mismatch, $mismatches));
        $migrationName = "add_missing_columns_to_{$tableName}_table";
        $migrationFile = "database/migrations/{$timestamp}_{$migrationName}.php";
        
        // Generate migration content
        $migrationContent = generateMigration($tableName, $missingColumns, $migrationName);
        
        file_put_contents($migrationFile, $migrationContent);
        echo "✅ Created: $migrationFile\n";
    }
}

function generateMigration($tableName, $missingColumns, $migrationName) {
    $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $migrationName)));
    
    $columnsCode = '';
    foreach ($missingColumns as $column) {
        // Try to guess column type based on name
        $type = guessColumnType($column);
        $columnsCode .= "            \$table->$type('$column')->nullable();\n";
    }
    
    return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('$tableName', function (Blueprint \$table) {
$columnsCode        });
    }

    public function down(): void
    {
        Schema::table('$tableName', function (Blueprint \$table) {

        });
    }
};

PHP;
}

function guessColumnType($columnName) {
    // ID columns
    if (preg_match('/^id_/', $columnName) || $columnName === 'id') {
        return 'unsignedBigInteger';
    }
    
    // Boolean columns
    if (preg_match('/^(is_|has_|can_|should_)/', $columnName)) {
        return 'boolean';
    }
    
    // Date/Time columns
    if (preg_match('/(date|tanggal)$/', $columnName)) {
        return 'date';
    }
    if (preg_match('/(time|waktu)$/', $columnName)) {
        return 'time';
    }
    if (preg_match('/(datetime|timestamp|_at)$/', $columnName)) {
        return 'timestamp';
    }
    
    // Numeric columns
    if (preg_match('/(harga|price|amount|total|jumlah|qty|quantity|stok|stock|nominal)/', $columnName)) {
        return 'decimal:15,2';
    }
    if (preg_match('/(count|number|no_|nomor)/', $columnName)) {
        return 'integer';
    }
    
    // Text columns
    if (preg_match('/(description|deskripsi|keterangan|catatan|alamat|address|notes)/', $columnName)) {
        return 'text';
    }
    
    // Default to string
    return 'string';
}

echo "\n\nDone!\n";
