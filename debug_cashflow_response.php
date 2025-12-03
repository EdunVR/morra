<?php
// Debug script to check cash flow API response
// Run this in browser: http://localhost/debug_cashflow_response.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate request
$request = Illuminate\Http\Request::create(
    '/finance/cashflow/data',
    'GET',
    [
        'outlet_id' => 1, // Change to your outlet ID
        'start_date' => date('Y-m-01'),
        'end_date' => date('Y-m-d'),
        'method' => 'direct'
    ]
);

$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);

echo "<h1>Cash Flow API Response Debug</h1>";
echo "<h2>Request Parameters:</h2>";
echo "<pre>";
print_r($request->all());
echo "</pre>";

echo "<h2>Response Status:</h2>";
echo "<p>HTTP " . $response->getStatusCode() . "</p>";

echo "<h2>Response Data:</h2>";
echo "<pre>";
print_r($data);
echo "</pre>";

if (isset($data['data']['operating']['items'])) {
    echo "<h2>Operating Items Detail:</h2>";
    foreach ($data['data']['operating']['items'] as $item) {
        echo "<h3>" . ($item['name'] ?? 'No name') . "</h3>";
        echo "<ul>";
        echo "<li>ID: " . ($item['id'] ?? 'N/A') . "</li>";
        echo "<li>Account ID: " . ($item['account_id'] ?? 'MISSING') . "</li>";
        echo "<li>Code: " . ($item['code'] ?? 'N/A') . "</li>";
        echo "<li>Amount: " . ($item['amount'] ?? 0) . "</li>";
        echo "<li>Has Children: " . (isset($item['children']) && count($item['children']) > 0 ? 'YES' : 'NO') . "</li>";
        echo "</ul>";
        
        if (isset($item['children']) && count($item['children']) > 0) {
            echo "<h4>Children:</h4>";
            foreach ($item['children'] as $child) {
                echo "<ul style='margin-left: 20px;'>";
                echo "<li>Name: " . ($child['name'] ?? 'No name') . "</li>";
                echo "<li>Account ID: " . ($child['account_id'] ?? 'MISSING') . "</li>";
                echo "<li>Code: " . ($child['code'] ?? 'N/A') . "</li>";
                echo "<li>Amount: " . ($child['amount'] ?? 0) . "</li>";
                echo "</ul>";
            }
        }
    }
}

if (isset($data['data']['investing']['items'])) {
    echo "<h2>Investing Items Detail:</h2>";
    foreach ($data['data']['investing']['items'] as $item) {
        echo "<h3>" . ($item['name'] ?? 'No name') . "</h3>";
        echo "<ul>";
        echo "<li>ID: " . ($item['id'] ?? 'N/A') . "</li>";
        echo "<li>Account ID: " . ($item['account_id'] ?? 'MISSING') . "</li>";
        echo "<li>Code: " . ($item['code'] ?? 'N/A') . "</li>";
        echo "<li>Amount: " . ($item['amount'] ?? 0) . "</li>";
        echo "</ul>";
    }
}

if (isset($data['data']['financing']['items'])) {
    echo "<h2>Financing Items Detail:</h2>";
    foreach ($data['data']['financing']['items'] as $item) {
        echo "<h3>" . ($item['name'] ?? 'No name') . "</h3>";
        echo "<ul>";
        echo "<li>ID: " . ($item['id'] ?? 'N/A') . "</li>";
        echo "<li>Account ID: " . ($item['account_id'] ?? 'MISSING') . "</li>";
        echo "<li>Code: " . ($item['code'] ?? 'N/A') . "</li>";
        echo "<li>Amount: " . ($item['amount'] ?? 0) . "</li>";
        echo "</ul>";
    }
}

$kernel->terminate($request, $response);
