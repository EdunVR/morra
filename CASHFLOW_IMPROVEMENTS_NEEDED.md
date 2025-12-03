# Cash Flow Improvements - Action Items

## 🐛 Issues to Fix:

### 1. Alpine.js Errors

**Problem:**

```
- Duplicate key on x-for: :key="item.id"
- Cannot read properties of undefined (reading 'operating/investing/financing')
- directCashFlow is undefined
```

**Root Cause:**

-   Data structure mismatch between API response and frontend
-   Using `item.id` as key when items don't have id property
-   Using `directCashFlow` instead of actual API response data

**Solution:**

```javascript
// Change from:
x-for="item in directCashFlow.operating" :key="item.id"

// To:
x-for="(item, index) in cashFlowData.operating.items" :key="'op-' + index"
```

### 2. Data Structure Fix

**Current (Wrong):**

```javascript
directCashFlow: {
  operating: [],
  investing: [],
  financing: []
}
```

**Should be (From API):**

```javascript
cashFlowData: {
  operating: { items: [], total: 0 },
  investing: { items: [], total: 0 },
  financing: { items: [], total: 0 },
  net_cash_flow: 0,
  beginning_cash: 0,
  ending_cash: 0
}
```

### 3. Hierarchy/Indentation Issues

**Problem:** Child accounts tidak menjorok

**Solution:** Add proper indentation classes:

```html
<!-- Parent item -->
<div class="flex justify-between items-center">
    <div class="flex items-center gap-2">
        <span x-text="item.name"></span>
    </div>
</div>

<!-- Child items (should be indented) -->
<template x-if="item.accounts && item.accounts.length > 0">
    <div class="ml-8 mt-2 space-y-2">
        <!-- Add ml-8 for indentation -->
        <template
            x-for="(account, accIndex) in item.accounts"
            :key="'acc-' + accIndex"
        >
            <div class="flex justify-between items-center text-sm">
                <div class="flex items-center gap-2 text-slate-600">
                    <i class="bx bx-subdirectory-right text-slate-400"></i>
                    <span x-text="account.account_code"></span> -
                    <span x-text="account.account_name"></span>
                </div>
                <span
                    class="text-slate-600"
                    x-text="formatCurrency(account.amount)"
                ></span>
            </div>
        </template>
    </div>
</template>
```

## 📊 Charts to Add/Fix:

### 1. Trend Arus Kas (Line Chart)

**Location:** After summary cards
**Data:** Last 6 months cash flow trends
**Chart Type:** Line chart with 3 lines (Operating, Investing, Financing)

```javascript
// Add to controller:
public function getCashFlowTrends(Request $request)
{
    $outletId = $request->get('outlet_id');
    $months = 6;

    $trends = [];
    for ($i = $months - 1; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $startDate = $date->startOfMonth()->format('Y-m-d');
        $endDate = $date->endOfMonth()->format('Y-m-d');

        $operating = $this->calculateOperatingCashFlowDirect($outletId, null, $startDate, $endDate);
        $investing = $this->calculateInvestingCashFlow($outletId, null, $startDate, $endDate);
        $financing = $this->calculateFinancingCashFlow($outletId, null, $startDate, $endDate);

        $trends[] = [
            'month' => $date->translatedFormat('M Y'),
            'operating' => $operating['total'],
            'investing' => $investing['total'],
            'financing' => $financing['total'],
            'net' => $operating['total'] + $investing['total'] + $financing['total']
        ];
    }

    return response()->json(['success' => true, 'data' => $trends]);
}
```

### 2. Komposisi Arus Kas (Pie/Doughnut Chart)

**Location:** Below trend chart
**Data:** Current period breakdown
**Chart Type:** Doughnut chart showing Operating/Investing/Financing percentages

```javascript
// Frontend calculation:
const total =
    Math.abs(cashFlowStats.operatingCash) +
    Math.abs(cashFlowStats.investingCash) +
    Math.abs(cashFlowStats.financingCash);

const composition = {
    operating: ((Math.abs(cashFlowStats.operatingCash) / total) * 100).toFixed(
        1
    ),
    investing: ((Math.abs(cashFlowStats.investingCash) / total) * 100).toFixed(
        1
    ),
    financing: ((Math.abs(cashFlowStats.financingCash) / total) * 100).toFixed(
        1
    ),
};
```

## 📈 Rasio Arus Kas (Real Data)

**Add section after charts:**

```html
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
    <h3 class="text-lg font-semibold text-slate-800 mb-4">Rasio Arus Kas</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Operating Cash Flow Ratio -->
        <div class="bg-blue-50 rounded-xl p-4">
            <div class="text-sm text-blue-600 mb-1">Rasio Arus Kas Operasi</div>
            <div
                class="text-2xl font-bold text-blue-700"
                x-text="cashFlowRatios.operatingRatio + '%'"
            ></div>
            <div class="text-xs text-slate-500 mt-1">
                Arus Kas Operasi / Total Arus Kas
            </div>
        </div>

        <!-- Cash Flow Adequacy Ratio -->
        <div class="bg-green-50 rounded-xl p-4">
            <div class="text-sm text-green-600 mb-1">Rasio Kecukupan Kas</div>
            <div
                class="text-2xl font-bold text-green-700"
                x-text="cashFlowRatios.adequacyRatio.toFixed(2)"
            ></div>
            <div class="text-xs text-slate-500 mt-1">
                Arus Kas Operasi / (Investasi + Pendanaan)
            </div>
        </div>

        <!-- Cash Flow Coverage Ratio -->
        <div class="bg-purple-50 rounded-xl p-4">
            <div class="text-sm text-purple-600 mb-1">Rasio Cakupan Kas</div>
            <div
                class="text-2xl font-bold text-purple-700"
                x-text="cashFlowRatios.coverageRatio.toFixed(2)"
            ></div>
            <div class="text-xs text-slate-500 mt-1">
                Arus Kas Operasi / Kewajiban Lancar
            </div>
        </div>
    </div>
</div>
```

**Calculate in JavaScript:**

```javascript
calculateCashFlowRatios() {
    const operating = this.cashFlowStats.operatingCash;
    const investing = Math.abs(this.cashFlowStats.investingCash);
    const financing = Math.abs(this.cashFlowStats.financingCash);
    const total = Math.abs(operating) + investing + financing;

    this.cashFlowRatios = {
        operatingRatio: total > 0 ? ((Math.abs(operating) / total) * 100).toFixed(1) : 0,
        adequacyRatio: (investing + financing) > 0 ? operating / (investing + financing) : 0,
        coverageRatio: 0 // Need current liabilities data
    };
}
```

## 🔮 Proyeksi Arus Kas (Real Data)

**Add section after ratios:**

```html
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
    <h3 class="text-lg font-semibold text-slate-800 mb-4">
        Proyeksi Arus Kas (3 Bulan Ke Depan)
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th
                        class="px-4 py-3 text-left font-semibold text-slate-700"
                    >
                        Bulan
                    </th>
                    <th
                        class="px-4 py-3 text-right font-semibold text-slate-700"
                    >
                        Proyeksi Operasi
                    </th>
                    <th
                        class="px-4 py-3 text-right font-semibold text-slate-700"
                    >
                        Proyeksi Investasi
                    </th>
                    <th
                        class="px-4 py-3 text-right font-semibold text-slate-700"
                    >
                        Proyeksi Pendanaan
                    </th>
                    <th
                        class="px-4 py-3 text-right font-semibold text-slate-700"
                    >
                        Proyeksi Total
                    </th>
                </tr>
            </thead>
            <tbody>
                <template
                    x-for="(projection, index) in cashFlowProjections"
                    :key="'proj-' + index"
                >
                    <tr class="border-b border-slate-100">
                        <td
                            class="px-4 py-3 text-slate-700"
                            x-text="projection.month"
                        ></td>
                        <td
                            class="px-4 py-3 text-right text-green-600"
                            x-text="formatCurrency(projection.operating)"
                        ></td>
                        <td
                            class="px-4 py-3 text-right text-red-600"
                            x-text="formatCurrency(projection.investing)"
                        ></td>
                        <td
                            class="px-4 py-3 text-right text-blue-600"
                            x-text="formatCurrency(projection.financing)"
                        ></td>
                        <td
                            class="px-4 py-3 text-right font-semibold"
                            x-text="formatCurrency(projection.total)"
                        ></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-xs text-slate-500">
        * Proyeksi berdasarkan rata-rata 6 bulan terakhir dengan trend analysis
    </div>
</div>
```

**Calculate projections:**

```javascript
async calculateProjections() {
    // Get historical data (last 6 months)
    const response = await fetch(`/finance/cashflow/trends?outlet_id=${this.filters.outlet_id}`);
    const { data: trends } = await response.json();

    // Calculate averages
    const avgOperating = trends.reduce((sum, t) => sum + t.operating, 0) / trends.length;
    const avgInvesting = trends.reduce((sum, t) => sum + t.investing, 0) / trends.length;
    const avgFinancing = trends.reduce((sum, t) => sum + t.financing, 0) / trends.length;

    // Calculate trend (simple linear regression)
    const operatingTrend = this.calculateTrend(trends.map(t => t.operating));
    const investingTrend = this.calculateTrend(trends.map(t => t.investing));
    const financingTrend = this.calculateTrend(trends.map(t => t.financing));

    // Project next 3 months
    this.cashFlowProjections = [];
    for (let i = 1; i <= 3; i++) {
        const month = moment().add(i, 'months').format('MMM YYYY');
        const operating = avgOperating + (operatingTrend * i);
        const investing = avgInvesting + (investingTrend * i);
        const financing = avgFinancing + (financingTrend * i);

        this.cashFlowProjections.push({
            month,
            operating,
            investing,
            financing,
            total: operating + investing + financing
        });
    }
}

calculateTrend(values) {
    const n = values.length;
    const sumX = (n * (n + 1)) / 2;
    const sumY = values.reduce((a, b) => a + b, 0);
    const sumXY = values.reduce((sum, y, x) => sum + (x + 1) * y, 0);
    const sumX2 = (n * (n + 1) * (2 * n + 1)) / 6;

    return (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);
}
```

## 🔧 Implementation Steps:

1. **Fix Alpine.js errors** (Priority: HIGH)

    - Change all `:key="item.id"` to `:key="'prefix-' + index"`
    - Fix data structure to match API response
    - Add proper null checks

2. **Fix hierarchy/indentation** (Priority: HIGH)

    - Add `ml-8` class for child items
    - Add subdirectory icon for visual hierarchy
    - Adjust text size for children (text-sm)

3. **Add trend chart** (Priority: MEDIUM)

    - Create `/finance/cashflow/trends` endpoint
    - Add Chart.js line chart
    - Show last 6 months data

4. **Add composition chart** (Priority: MEDIUM)

    - Calculate percentages from current data
    - Add Chart.js doughnut chart
    - Show breakdown of activities

5. **Add cash flow ratios** (Priority: MEDIUM)

    - Calculate ratios from current data
    - Display in cards
    - Add explanations

6. **Add projections** (Priority: LOW)
    - Calculate based on historical trends
    - Display in table
    - Add disclaimer

## 📝 Files to Modify:

1. `resources/views/admin/finance/cashflow/index.blade.php` - Fix errors, add charts, ratios, projections
2. `app/Http/Controllers/CashFlowController.php` - Add trends endpoint
3. `routes/web.php` - Add trends route

## ⚠️ Important Notes:

-   Test each change incrementally
-   Clear browser cache after changes
-   Verify data structure matches API response
-   Add proper error handling
-   Test with real data

---

**Status:** Ready for implementation
**Estimated Time:** 2-3 hours
**Priority:** HIGH (Alpine errors), MEDIUM (charts/ratios), LOW (projections)
