# Cash Flow Auto-Fix Script
# This script automatically applies all necessary fixes to the cash flow index file

Write-Host "🔧 Starting Cash Flow Auto-Fix..." -ForegroundColor Cyan

$file = "resources/views/admin/finance/cashflow/index.blade.php"
$backup = "resources/views/admin/finance/cashflow/index.blade.php.backup"

# Check if file exists
if (-not (Test-Path $file)) {
    Write-Host "❌ Error: File not found: $file" -ForegroundColor Red
    exit 1
}

# Create backup
Write-Host "📦 Creating backup..." -ForegroundColor Yellow
Copy-Item $file "$file.pre-fix-backup" -Force

# Read file content
Write-Host "📖 Reading file..." -ForegroundColor Yellow
$content = Get-Content $file -Raw

# Fix 1: Operating activities x-for loop
Write-Host "🔧 Fix 1: Operating activities x-for..." -ForegroundColor Green
$content = $content -replace 'x-for="item in directCashFlow\.operating" :key="item\.id"', 'x-for="(item, index) in (cashFlowData.operating?.items || [])" :key="''op-'' + index"'

# Fix 2: Investing activities x-for loop
Write-Host "🔧 Fix 2: Investing activities x-for..." -ForegroundColor Green
$content = $content -replace 'x-for="item in directCashFlow\.investing" :key="item\.id"', 'x-for="(item, index) in (cashFlowData.investing?.items || [])" :key="''inv-'' + index"'

# Fix 3: Financing activities x-for loop
Write-Host "🔧 Fix 3: Financing activities x-for..." -ForegroundColor Green
$content = $content -replace 'x-for="item in directCashFlow\.financing" :key="item\.id"', 'x-for="(item, index) in (cashFlowData.financing?.items || [])" :key="''fin-'' + index"'

# Fix 4: Change item.description to item.name
Write-Host "🔧 Fix 4: Changing item.description to item.name..." -ForegroundColor Green
$content = $content -replace 'x-text="item\.description"', 'x-text="item.name"'

# Fix 5: Update subtotal references
Write-Host "🔧 Fix 5: Updating subtotal references..." -ForegroundColor Green
$content = $content -replace 'directCashFlow\.netOperating', '(cashFlowData.operating?.total || 0)'
$content = $content -replace 'directCashFlow\.netInvesting', '(cashFlowData.investing?.total || 0)'
$content = $content -replace 'directCashFlow\.netFinancing', '(cashFlowData.financing?.total || 0)'

# Fix 6: Update data structure in JavaScript
Write-Host "🔧 Fix 6: Updating data structure..." -ForegroundColor Green
$oldDataStructure = @'
        directCashFlow: \{
          operating: \[\],
          investing: \[\],
          financing: \[\],
          netOperating: 0,
          netInvesting: 0,
          netFinancing: 0
        \},
        indirectCashFlow: \{
          netIncome: 0,
          adjustments: \[\],
          operating: \[\],
          investing: \[\],
          financing: \[\],
          netOperating: 0,
          netInvesting: 0,
          netFinancing: 0
        \},
'@

$newDataStructure = @'
        cashFlowData: {
          operating: { items: [], total: 0 },
          investing: { items: [], total: 0 },
          financing: { items: [], total: 0 },
          net_cash_flow: 0,
          beginning_cash: 0,
          ending_cash: 0
        },
'@

$content = $content -replace $oldDataStructure, $newDataStructure

# Save file
Write-Host "💾 Saving changes..." -ForegroundColor Yellow
Set-Content $file $content -NoNewline

# Clear cache
Write-Host "🧹 Clearing view cache..." -ForegroundColor Yellow
php artisan view:clear

Write-Host ""
Write-Host "✅ Cash Flow fixes applied successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Summary of changes:" -ForegroundColor Cyan
Write-Host "  ✓ Fixed 3 x-for loops (operating, investing, financing)" -ForegroundColor White
Write-Host "  ✓ Updated data structure (directCashFlow → cashFlowData)" -ForegroundColor White
Write-Host "  ✓ Fixed subtotal references" -ForegroundColor White
Write-Host "  ✓ Changed item.description → item.name" -ForegroundColor White
Write-Host ""
Write-Host "📦 Backup saved to: $file.pre-fix-backup" -ForegroundColor Yellow
Write-Host ""
Write-Host "🧪 Next steps:" -ForegroundColor Cyan
Write-Host "  1. Test in browser: http://localhost/finance/cashflow" -ForegroundColor White
Write-Host "  2. Check browser console for errors (F12)" -ForegroundColor White
Write-Host "  3. Select outlet and date range" -ForegroundColor White
Write-Host "  4. Verify data loads correctly" -ForegroundColor White
Write-Host ""
Write-Host "🔄 If something goes wrong, restore backup:" -ForegroundColor Yellow
Write-Host "  Copy-Item '$file.pre-fix-backup' '$file' -Force" -ForegroundColor Gray
Write-Host ""
