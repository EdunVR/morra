<?php
/**
 * ERP Performance Optimization Script
 * 
 * Script ini akan mencari dan mengoptimasi semua halaman admin
 * dengan mengubah sequential async calls menjadi parallel
 */

$viewsPath = __DIR__ . '/resources/views/admin';
$optimizedCount = 0;
$errors = [];

// Pattern yang akan dicari dan diganti
$patterns = [
    // Pattern 1: Sequential await dalam init()
    [
        'search' => '/async init\(\)\s*\{([^}]*?)(\s*await\s+this\.\w+\([^)]*\);\s*){2,}([^}]*?)\}/s',
        'description' => 'Sequential await calls in init()'
    ]
];

function scanDirectory($dir, &$files = []) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            scanDirectory($path, $files);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $files[] = $path;
        }
    }
    return $files;
}

function analyzeFile($filePath) {
    $content = file_get_contents($filePath);
    $issues = [];
    
    // Check for sequential await pattern
    if (preg_match('/async\s+init\s*\(\)\s*\{[^}]*await[^}]*await/s', $content)) {
        $issues[] = 'Sequential await calls detected in init()';
    }
    
    // Check for sequential fetch calls
    if (preg_match('/await\s+fetch[^;]*;\s*await\s+fetch/s', $content)) {
        $issues[] = 'Sequential fetch calls detected';
    }
    
    // Check for missing error handling
    if (preg_match('/Promise\.all\([^)]+\)(?!\s*\.catch|\s*}\s*catch)/s', $content)) {
        $issues[] = 'Promise.all without error handling';
    }
    
    return $issues;
}

function generateOptimizationReport($files) {
    $report = "# ERP Optimization Analysis Report\n\n";
    $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $report .= "## Summary\n\n";
    $report .= "Total files scanned: " . count($files) . "\n\n";
    
    $needsOptimization = [];
    
    foreach ($files as $file) {
        $issues = analyzeFile($file);
        if (!empty($issues)) {
            $needsOptimization[$file] = $issues;
        }
    }
    
    $report .= "Files needing optimization: " . count($needsOptimization) . "\n\n";
    
    if (!empty($needsOptimization)) {
        $report .= "## Files Needing Optimization\n\n";
        foreach ($needsOptimization as $file => $issues) {
            $relativePath = str_replace(__DIR__ . '/', '', $file);
            $report .= "### $relativePath\n\n";
            foreach ($issues as $issue) {
                $report .= "- ⚠️ $issue\n";
            }
            $report .= "\n";
        }
    }
    
    $report .= "## Optimization Recommendations\n\n";
    $report .= "1. Convert sequential await calls to Promise.all()\n";
    $report .= "2. Add try-catch blocks for error handling\n";
    $report .= "3. Implement caching for frequently accessed data\n";
    $report .= "4. Use debouncing for search/filter functions\n\n";
    
    return $report;
}

// Main execution
echo "🚀 ERP Performance Optimization Script\n";
echo "=====================================\n\n";

echo "📁 Scanning files in: $viewsPath\n";
$files = scanDirectory($viewsPath);
echo "✅ Found " . count($files) . " PHP files\n\n";

echo "🔍 Analyzing files for optimization opportunities...\n";
$report = generateOptimizationReport($files);

// Save report
$reportPath = __DIR__ . '/OPTIMIZATION_REPORT.md';
file_put_contents($reportPath, $report);
echo "✅ Report saved to: $reportPath\n\n";

echo "📊 Analysis complete!\n";
echo "Please review the report for optimization opportunities.\n";
