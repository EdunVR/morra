# Cash Flow - Quick Fix Guide

## 🚨 CRITICAL FIXES (Do These First!)

### 1. Fix Alpine.js Duplicate Key Error

**Find and Replace in `resources/views/admin/finance/cashflow/index.blade.php`:**

```html
<!-- BEFORE (Line ~324) -->
<template x-for="item in directCashFlow.operating" :key="item.id">
    <!-- AFTER -->
    <template
        x-for="(item, index) in (cashFlowData.operating?.items || [])"
        :key="'op-' + index"
    ></template
></template>
```

```html
<!-- BEFORE (Line ~350) -->
<template x-for="item in directCashFlow.investing" :key="item.id">
    <!-- AFTER -->
    <template
        x-for="(item, index) in (cashFlowData.investing?.items || [])"
        :key="'inv-' + index"
    ></template
></template>
```

```html
<!-- BEFORE (Line ~376) -->
<template x-for="item in directCashFlow.financing" :key="item.id">
    <!-- AFTER -->
    <template
        x-for="(item, index) in (cashFlowData.financing?.items || [])"
        :key="'fin-' + index"
    ></template
></template>
```

### 2. Fix Data Structure

**Find (around line 626-634):**

```javascript
directCashFlow: {
  operating: [],
  investing: [],
  financing: [],
  netOperating: 0,
  netInvesting: 0,
  netFinancing: 0
},
```

**Replace with:**

```javascript
cashFlowData: {
  operating: { items: [], total: 0 },
  investing: { items: [], total: 0 },
  financing: { items: [], total: 0 },
  net_cash_flow: 0,
  beginning_cash: 0,
  ending_cash: 0
},
```

### 3. Fix loadCashFlowData() Method

**Find the method (around line 550-580) and update:**

```javascript
async loadCashFlowData() {
  if (!this.filters.start_date || !this.filters.end_date) {
    return;
  }

  this.isLoading = true;
  this.error = null;

  try {
    const params = new URLSearchParams({
      outlet_id: this.filters.outlet_id,
      start_date: this.filters.start_date,
      end_date: this.filters.end_date,
      method: this.filters.method
    });

    if (this.filters.book_id) {
      params.append('book_id', this.filters.book_id);
    }

    const response = await fetch(`{{ route('finance.cashflow.data') }}?${params}`);
    const result = await response.json();

    if (result.success) {
      // Store the complete data structure from API
      this.cashFlowData = result.data;

      // Update stats
      this.cashFlowStats = result.data.stats || {
        netCashFlow: result.data.net_cash_flow || 0,
        operatingCash: result.data.operating?.total || 0,
        investingCash: result.data.investing?.total || 0,
        financingCash: result.data.financing?.total || 0
      };
    } else {
      this.error = result.message || 'Gagal memuat data arus kas';
    }
  } catch (error) {
    console.error('Error loading cash flow data:', error);
    this.error = 'Terjadi kesalahan saat memuat data';
  } finally {
    this.isLoading = false;
  }
},
```

### 4. Fix Child Account Display (Add Indentation)

**Find the operating activities section (around line 324-348) and update:**

```html
<template
    x-for="(item, index) in (cashFlowData.operating?.items || [])"
    :key="'op-' + index"
>
    <div class="space-y-2">
        <!-- Parent Item -->
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="bx bx-trending-up text-green-500"></i>
                <span class="font-medium" x-text="item.name"></span>
            </div>
            <span
                :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'"
                class="font-semibold"
                x-text="formatCurrency(item.amount)"
            ></span>
        </div>

        <!-- Child Accounts (Indented) -->
        <template x-if="item.accounts && item.accounts.length > 0">
            <div class="ml-8 space-y-1">
                <template
                    x-for="(account, accIndex) in item.accounts"
                    :key="'op-acc-' + index + '-' + accIndex"
                >
                    <div
                        class="flex justify-between items-center text-sm py-1 hover:bg-slate-50 rounded px-2 -mx-2 transition-colors cursor-pointer"
                        @click="viewAccountDetails(account.account_id)"
                    >
                        <div class="flex items-center gap-2 text-slate-600">
                            <i
                                class="bx bx-subdirectory-right text-slate-400"
                            ></i>
                            <span
                                class="font-mono text-xs"
                                x-text="account.account_code"
                            ></span>
                            <span x-text="account.account_name"></span>
                        </div>
                        <span
                            class="text-slate-700"
                            x-text="formatCurrency(account.amount)"
                        ></span>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>
```

**Do the same for investing (line ~350) and financing (line ~376) sections!**

### 5. Fix Summary Section

**Find the summary section (around line 400-420) and update:**

```html
<div
    class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border-2 border-blue-200"
>
    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-slate-700">Kenaikan (Penurunan) Kas Bersih</span>
            <span
                class="text-xl font-bold"
                :class="cashFlowData.net_cash_flow >= 0 ? 'text-green-600' : 'text-red-600'"
                x-text="formatCurrency(cashFlowData.net_cash_flow)"
            ></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-slate-700">Kas Awal Periode</span>
            <span
                class="text-lg font-semibold text-slate-800"
                x-text="formatCurrency(cashFlowData.beginning_cash)"
            ></span>
        </div>
        <div class="h-px bg-blue-300"></div>
        <div class="flex justify-between items-center">
            <span class="text-lg font-semibold text-slate-800"
                >Kas Akhir Periode</span
            >
            <span
                class="text-2xl font-bold text-blue-700"
                x-text="formatCurrency(cashFlowData.ending_cash)"
            ></span>
        </div>
    </div>
</div>
```

## ✅ Testing Checklist

After making these changes:

1. Clear browser cache (Ctrl+Shift+Del)
2. Refresh page (Ctrl+F5)
3. Check browser console - should have NO Alpine errors
4. Select outlet and date range
5. Verify data loads correctly
6. Check that child accounts are indented
7. Click on child accounts to view details
8. Verify all amounts display correctly

## 🎯 Expected Results

-   ✅ No Alpine.js errors in console
-   ✅ Data loads from API correctly
-   ✅ Child accounts are indented (ml-8)
-   ✅ All amounts display properly
-   ✅ Click on accounts works
-   ✅ Summary shows correct totals

## ⏱️ Time Required

-   **5-10 minutes** for critical fixes
-   Test immediately after each change
-   If errors persist, check browser console for specific line numbers

---

**Priority:** 🔴 CRITICAL - Do these fixes NOW before adding charts/ratios!
