# Cash Flow Auto-Fix Script - Simple Version
Write-Host "Starting Cash Flow Auto-Fix..."

$file = "resources/views/admin/finance/cashflow/index.blade.php"

# Check if file exists
if (-not (Test-Path $file)) {
    Write-Host "Error: File not found"
    exit 1
}

# Create backup
Write-Host "Creating backup..."
Copy-Item $file "$file.pre-fix-backup" -Force

# Read file content
Write-Host "Reading file..."
$content = Get-Content $file -Raw

# Fix 1: Operating activities
Write-Host "Fix 1: Operating activities..."
$content = $content.Replace('x-for="item in directCashFlow.operating" :key="item.id"', 'x-for="(item, index) in (cashFlowData.operating?.items || [])" :key="`''op-`'' + index"')

# Fix 2: Investing activities
Write-Host "Fix 2: Investing activities..."
$content = $content.Replace('x-for="item in directCashFlow.investing" :key="item.id"', 'x-for="(item, index) in (cashFlowData.investing?.items || [])" :key="`''inv-`'' + index"')

# Fix 3: Financing activities
Write-Host "Fix 3: Financing activities..."
$content = $content.Replace('x-for="item in directCashFlow.financing" :key="item.id"', 'x-for="(item, index) in (cashFlowData.financing?.items || [])" :key="`''fin-`'' + index"')

# Fix 4: Change item.description to item.name
Write-Host "Fix 4: Changing descriptions..."
$content = $content.Replace('x-text="item.description"', 'x-text="item.name"')

# Fix 5: Update subtotals
Write-Host "Fix 5: Updating subtotals..."
$content = $content.Replace('directCashFlow.netOperating', '(cashFlowData.operating?.total || 0)')
$content = $content.Replace('directCashFlow.netInvesting', '(cashFlowData.investing?.total || 0)')
$content = $content.Replace('directCashFlow.netFinancing', '(cashFlowData.financing?.total || 0)')

# Save file
Write-Host "Saving changes..."
Set-Content $file $content -NoNewline

# Clear cache
Write-Host "Clearing cache..."
php artisan view:clear

Write-Host ""
Write-Host "SUCCESS! Cash Flow fixes applied!"
Write-Host "Backup saved to: $file.pre-fix-backup"
Write-Host ""
Write-Host "Test in browser: http://localhost/finance/cashflow"
Write-Host ""
