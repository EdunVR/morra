<?php

/**
 * Analyze queries in controllers to identify which need outlet filtering
 */

echo "=== ANALYZE QUERIES FOR OUTLET FILTER ===\n\n";

$controllers = [
    // Priority 1: Finance
    'app/Http/Controllers/FinanceAccountantController.php' => 'Finance',
    'app/Http/Controllers/FinanceDashboardController.php' => 'Finance',
    'app/Http/Controllers/BankReconciliationController.php' => 'Finance',
    'app/Http/Controllers/CompanyBankAccountController.php' => 'Finance',
    
    // Priority 2: Dashboard
    'app/Http/Controllers/AdminDashboardController.php' => 'Dashboard',
    'app/Http/Controllers/CrmDashboardController.php' => 'Dashboard',
    'app/Http/Controllers/SalesDashboardController.php' => 'Dashboard',
    
    // Priority 3: Reports
    'app/Http/Controllers/MarginReportController.php' => 'Reports',
    'app/Http/Controllers/SalesReportController.php' => 'Reports',
    
    // Priority 4: Sales & Purchase
    'app/Http/Controllers/PosController.php' => 'Sales',
    'app/Http/Controllers/SalesManagementController.php' => 'Sales',
    'app/Http/Controllers/PurchaseManagementController.php' => 'Purchase',
    
    // Priority 5: Service & CRM
    'app/Http/Controllers/ServiceController.php' => 'Service',
    'app/Http/Controllers/CustomerTypeController.php' => 'CRM',
    
    // Priority 6: SDM
    'app/Http/Controllers/AttendanceManagementController.php' => 'SDM',
    
    // Priority 7: Others
    'app/Http/Controllers/UserManagementController.php' => 'User',
    'app/Http/Controllers/ChatController.php' => 'Chat',
];

$results = [];

foreach ($controllers as $file => $module) {
    if (!file_exists($file)) {
        continue;
    }
    
    $content = file_get_contents($file);
    $name = basename($file, '.php');
    
    // Check for queries that need filtering
    $patterns = [
        'where.*outlet_id' => 'Has outlet_id filter',
        'whereIn.*outlet_id' => 'Has whereIn outlet_id',
        'byOutlet' => 'Uses byOutlet scope',
        'getAccessibleOutletIds' => 'Already uses accessible IDs',
        'isSuperAdmin' => 'Has superadmin check',
        'validateOutletAccess' => 'Has access validation',
    ];
    
    $findings = [];
    foreach ($patterns as $pattern => $description) {
        if (preg_match('/' . $pattern . '/i', $content)) {
            $findings[] = $description;
        }
    }
    
    // Count methods
    preg_match_all('/public function \w+\(/', $content, $methods);
    $methodCount = count($methods[0]);
    
    $results[$module][] = [
        'name' => $name,
        'file' => $file,
        'methods' => $methodCount,
        'findings' => $findings,
        'needs_update' => count($findings) < 2 // Needs update if less than 2 patterns found
    ];
}

// Display results by module
foreach ($results as $module => $controllers) {
    echo "=== $module Module ===\n";
    
    foreach ($controllers as $controller) {
        $status = $controller['needs_update'] ? '⚠️' : '✓';
        echo "$status {$controller['name']}\n";
        echo "   Methods: {$controller['methods']}\n";
        
        if (!empty($controller['findings'])) {
            echo "   Current: " . implode(', ', $controller['findings']) . "\n";
        } else {
            echo "   Current: No outlet filtering found\n";
        }
        
        if ($controller['needs_update']) {
            echo "   Action: NEEDS QUERY FILTERING UPDATE\n";
        } else {
            echo "   Action: Review and verify\n";
        }
        echo "\n";
    }
}

// Summary
$totalControllers = array_sum(array_map('count', $results));
$needsUpdate = 0;
foreach ($results as $controllers) {
    foreach ($controllers as $controller) {
        if ($controller['needs_update']) {
            $needsUpdate++;
        }
    }
}

echo "=== SUMMARY ===\n";
echo "Total controllers analyzed: $totalControllers\n";
echo "Need query filtering update: $needsUpdate\n";
echo "Already have filtering: " . ($totalControllers - $needsUpdate) . "\n";

echo "\n=== PRIORITY ORDER ===\n";
echo "1. Finance Module (4 controllers)\n";
echo "2. Dashboard Module (3 controllers)\n";
echo "3. Reports Module (2 controllers)\n";
echo "4. Sales & Purchase (3 controllers)\n";
echo "5. Service & CRM (2 controllers)\n";
echo "6. SDM (1 controller)\n";
echo "7. Others (2 controllers)\n";

echo "\n=== DONE ===\n";
