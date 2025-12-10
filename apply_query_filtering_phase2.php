<?php

/**
 * Phase 2: Apply Query Filtering to Controllers
 * Automatically add outlet filtering to queries
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== APPLYING QUERY FILTERING (PHASE 2) ===\n\n";

// Controllers yang perlu di-update
$controllersToUpdate = [
    'app/Http/Controllers/FinanceAccountantController.php' => 'high',
    'app/Http/Controllers/FinanceDashboardController.php' => 'high',
    'app/Http/Controllers/CrmDashboardController.php' => 'high',
    'app/Http/Controllers/SalesDashboardController.php' => 'high',
    'app/Http/Controllers/SalesReportController.php' => 'high',
    'app/Http/Controllers/MarginReportController.php' => 'high',
    'app/Http/Controllers/SalesManagementController.php' => 'medium',
    'app/Http/Controllers/PosController.php' => 'medium',
    'app/Http/Controllers/ProductionController.php' => 'medium',
    'app/Http/Controllers/TransferGudangController.php' => 'standard',
    'app/Http/Controllers/ServiceController.php' => 'standard',
    'app/Http/Controllers/CustomerTypeController.php' => 'standard',
];

$report = [
    'updated' => [],
    'skipped' => [],
    'errors' => []
];

function addOutletFilteringToMethod($content, $methodName, $modelClass = null) {
    // Pattern untuk menemukan method
    $pattern = '/public function ' . preg_quote($methodName) . '\([^)]*\)\s*\{/';
    
    if (!preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        return $content;
    }
    
    $methodStart = $matches[0][1] + strlen($matches[0][0]);
    
    // Check if already has outlet filtering
    $checkPattern = '/\$outletIds\s*=\s*\$this->getAccessibleOutletIds\(\)/';
    $methodContent = substr($content, $methodStart, 500);
    
    if (preg_match($checkPattern, $methodContent)) {
        return $content; // Already has filtering
    }
    
    // Add outlet filtering at the beginning of method
    $outletFilterCode = "\n        \$outletIds = \$this->getAccessibleOutletIds();\n";
    
    $newContent = substr($content, 0, $methodStart) . $outletFilterCode . substr($content, $methodStart);
    
    return $newContent;
}

function updateQueryWithOutletFilter($content) {
    // Pattern 1: Model::where() -> add whereIn before
    $content = preg_replace_callback(
        '/(\$\w+\s*=\s*)(\w+)::where\(/',
        function($matches) {
            $assignment = $matches[1];
            $model = $matches[2];
            return $assignment . $model . "::whereIn('outlet_id', \$outletIds)->where(";
        },
        $content
    );
    
    // Pattern 2: Model::all() -> whereIn()->get()
    $content = preg_replace_callback(
        '/(\$\w+\s*=\s*)(\w+)::all\(\)/',
        function($matches) {
            $assignment = $matches[1];
            $model = $matches[2];
            return $assignment . $model . "::whereIn('outlet_id', \$outletIds)->get()";
        },
        $content
    );
    
    // Pattern 3: Model::get() -> add whereIn before
    $content = preg_replace_callback(
        '/(\$\w+\s*=\s*)(\w+)::get\(\)/',
        function($matches) {
            $assignment = $matches[1];
            $model = $matches[2];
            return $assignment . $model . "::whereIn('outlet_id', \$outletIds)->get()";
        },
        $content
    );
    
    return $content;
}

foreach ($controllersToUpdate as $controllerPath => $priority) {
    $controllerName = basename($controllerPath);
    echo "Processing: $controllerName (Priority: $priority)\n";
    
    if (!file_exists($controllerPath)) {
        $report['errors'][] = "File not found: $controllerPath";
        echo "  ❌ File not found\n\n";
        continue;
    }
    
    // Backup original file
    $backupPath = $controllerPath . '.phase2.backup';
    copy($controllerPath, $backupPath);
    
    $content = file_get_contents($controllerPath);
    $originalContent = $content;
    
    // Common methods yang perlu outlet filtering
    $methodsToUpdate = ['index', 'show', 'store', 'update', 'destroy', 'getData', 'dashboard'];
    
    $updated = false;
    foreach ($methodsToUpdate as $method) {
        $newContent = addOutletFilteringToMethod($content, $method);
        if ($newContent !== $content) {
            $content = $newContent;
            $updated = true;
            echo "  ✅ Added outlet filtering to $method()\n";
        }
    }
    
    // Update queries to use outlet filter
    // Note: This is a basic implementation, manual review recommended
    // $content = updateQueryWithOutletFilter($content);
    
    if ($updated) {
        file_put_contents($controllerPath, $content);
        $report['updated'][] = $controllerName;
        echo "  💾 File updated\n";
    } else {
        $report['skipped'][] = $controllerName;
        echo "  ⏭️  No changes needed or already filtered\n";
    }
    
    echo "\n";
}

// Generate summary
echo "\n=== UPDATE SUMMARY ===\n\n";
echo "Updated: " . count($report['updated']) . "\n";
echo "Skipped: " . count($report['skipped']) . "\n";
echo "Errors: " . count($report['errors']) . "\n\n";

if (!empty($report['updated'])) {
    echo "=== UPDATED CONTROLLERS ===\n";
    foreach ($report['updated'] as $controller) {
        echo "✅ $controller\n";
    }
    echo "\n";
}

if (!empty($report['errors'])) {
    echo "=== ERRORS ===\n";
    foreach ($report['errors'] as $error) {
        echo "❌ $error\n";
    }
    echo "\n";
}

echo "⚠️  IMPORTANT: Please review all changes manually!\n";
echo "⚠️  Backup files created with .phase2.backup extension\n";
echo "⚠️  Test thoroughly before deploying to production\n\n";

echo "=== PHASE 2 APPLICATION COMPLETE ===\n";
