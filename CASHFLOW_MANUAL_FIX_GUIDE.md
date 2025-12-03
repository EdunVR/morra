# Cash Flow Manual Fix Guide

## ⚠️ IMPORTANT: Autofix Issue

Kiro IDE autofix terus menghapus perubahan yang saya buat. Oleh karena itu, saya berikan panduan manual untuk Anda implementasikan sendiri.

## 🔧 Manual Fixes Required

### Fix 1: Operating Activities Section (Line ~324)

**Find this code:**

```html
<template x-for="item in directCashFlow.operating" :key="item.id">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-2">
            <span class="text-slate-600" x-text="item.description"></span>
            <span
                x-show="item.note"
                class="text-xs text-slate-400"
                x-text="'(' + item.note + ')'"
            ></span>
        </div>
        <div
            class="font-semibold"
            :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'"
            x-text="formatCurrency(item.amount)"
        ></div>
    </div>
</template>
```

**Replace with:**

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
                <span
                    class="font-medium text-slate-700"
                    x-text="item.name"
                ></span>
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

**Also update the subtotal line below it:**

```html
<!-- FROM: -->
<span
    :class="directCashFlow.netOperating >= 0 ? 'text-green-600' : 'text-red-600'"
    x-text="formatCurrency(directCashFlow.netOperating)"
></span>

<!-- TO: -->
<span
    :class="(cashFlowData.operating?.total || 0) >= 0 ? 'text-green-600' : 'text-red-600'"
    x-text="formatCurrency(cashFlowData.operating?.total || 0)"
></span>
```

### Fix 2: Investing Activities Section (Line ~350)

**Find:**

```html
<template x-for="item in directCashFlow.investing" :key="item.id"></template>
```

**Replace with:**

```html
<template
    x-for="(item, index) in (cashFlowData.investing?.items || [])"
    :key="'inv-' + index"
>
    <div class="space-y-2">
        <!-- Parent Item -->
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="bx bx-trending-down text-red-500"></i>
                <span
                    class="font-medium text-slate-700"
                    x-text="item.name"
                ></span>
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
                    :key="'inv-acc-' + index + '-' + accIndex"
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

**Update subtotal:**

```html
<!-- FROM: -->
<span
    :class="directCashFlow.netInvesting >= 0 ? 'text-green-600' : 'text-red-600'"
    x-text="formatCurrency(directCashFlow.netInvesting)"
></span>

<!-- TO: -->
<span
    :class="(cashFlowData.investing?.total || 0) >= 0 ? 'text-green-600' : 'text-red-600'"
    x-text="formatCurrency(cashFlowData.investing?.total || 0)"
></span>
```

### Fix 3: Financing Activities Section (Line ~376)

**Find:**

```html
<template x-for="item in directCashFlow.financing" :key="item.id"></template>
```

**Replace with same pattern as above, using:**

-   Key: `'fin-' + index`
-   Icon: `bx-dollar-circle text-blue-500`
-   Child key: `'fin-acc-' + index + '-' + accIndex`

**Update subtotal:**

```html
<!-- FROM: -->
<span
    :class="directCashFlow.netFinancing >= 0 ? 'text-green-600' : 'text-red-600'"
    x-text="formatCurrency(directCashFlow.netFinancing)"
></span>

<!-- TO: -->
<span
    :class="(cashFlowData.financing?.total || 0) >= 0 ? 'text-green-600' : 'text-red-600'"
    x-text="formatCurrency(cashFlowData.financing?.total || 0)"
></span>
```

### Fix 4: JavaScript Data Structure (Line ~680)

**Find:**

```javascript
directCashFlow: {
  operating: [],
  investing: [],
  financing: [],
  netOperating: 0,
  netInvesting: 0,
  netFinancing: 0
},
indirectCashFlow: {
  netIncome: 0,
  adjustments: [],
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

### Fix 5: loadCashFlowData() Method (Line ~753)

**Find the entire method and replace with:**

```javascript
async loadCashFlowData() {
  if (!this.filters.outlet_id) {
    this.error = 'Pilih outlet terlebih dahulu';
    return;
  }

  if (!this.filters.start_date || !this.filters.end_date) {
    this.error = 'Pilih periode tanggal terlebih dahulu';
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

      // Update charts if needed
      this.$nextTick(() => {
        if (typeof this.initCharts === 'function') {
          this.initCharts();
        }
      });
    } else {
      this.error = result.message || 'Gagal memuat data arus kas';
    }
  } catch (error) {
    console.error('Error loading cash flow:', error);
    this.error = 'Terjadi kesalahan saat memuat data';
  } finally {
    this.isLoading = false;
  }
},
```

### Fix 6: Remove Dummy Data (Line ~815)

**Find and DELETE all dummy data assignments in viewAccountDetails() method:**

```javascript
// DELETE these lines:
this.directCashFlow = { ... };
this.indirectCashFlow = { ... };
this.cashFlowStats = { ... };
this.cashFlowForecast = [ ... ];
```

**Keep only the actual API call logic.**

## 📝 Step-by-Step Instructions:

1. **Backup first:**

    ```bash
    cp resources/views/admin/finance/cashflow/index.blade.php resources/views/admin/finance/cashflow/index.blade.php.backup2
    ```

2. **Open file in editor:**

    ```
    resources/views/admin/finance/cashflow/index.blade.php
    ```

3. **Make changes one by one** (use Find & Replace in your editor)

4. **Save after each change**

5. **Test in browser** after all changes

6. **Clear cache:**
    ```bash
    php artisan view:clear
    ```

## ✅ Verification:

After making all changes, verify:

-   [ ] No syntax errors in browser console
-   [ ] No Alpine.js errors
-   [ ] Data loads from API
-   [ ] Child accounts are indented
-   [ ] Click on child accounts works

## 🚨 If Autofix Runs Again:

If Kiro IDE autofix runs and breaks things:

1. Restore from backup
2. Disable autofix temporarily
3. Make changes manually
4. Test thoroughly before re-enabling autofix

---

**Sorry for the inconvenience caused by autofix!**
**Please make these changes manually and let me know the result.**
