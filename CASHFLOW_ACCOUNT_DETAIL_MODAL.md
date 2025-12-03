# Laporan Arus Kas - Account Detail Modal Implementation

## Fitur yang Diminta

Setiap akun yang terlibat dalam laporan arus kas dapat diklik untuk menampilkan modal detail transaksi.

## Status Implementasi

### ✅ Yang Sudah Ada:

1. Modal template sudah ada di file (line 1-130)
2. Method `viewAccountDetails(accountId)` sudah ada (line ~1070)
3. Method `closeAccountModal()` sudah ada
4. Backend endpoint sudah ada: `getAccountDetails($accountId)`

### ⏳ Yang Perlu Ditambahkan:

1. Click handler pada nama akun
2. Visual feedback (hover effect)
3. Cursor pointer untuk akun yang bisa diklik

## Implementasi

### 1. Update Template - Direct Method

**Location**: Operating Activities section (~line 350)

**Before**:

```html
<span
    :class="item.is_header ? 'font-semibold text-slate-700' : 'text-slate-600'"
    x-text="item.name"
>
</span>
```

**After**:

```html
<!-- For clickable accounts (not headers) -->
<button
    x-show="!item.is_header && item.id"
    @click="viewAccountDetails(item.id)"
    class="text-slate-600 hover:text-blue-600 hover:underline cursor-pointer text-left"
    x-text="item.name"
></button>

<!-- For headers (not clickable) -->
<span
    x-show="item.is_header"
    class="font-semibold text-slate-700"
    x-text="item.name"
>
</span>
```

### 2. Add Hover Effect on Row

**Before**:

```html
<div class="flex justify-between items-center py-1" ...></div>
```

**After**:

```html
<div
    class="flex justify-between items-center py-1 hover:bg-slate-50 rounded transition-colors"
    ...
></div>
```

### 3. Update for Investing & Financing Activities

Apply same pattern to:

-   Investing Activities section (~line 380)
-   Financing Activities section (~line 410)

### 4. Update for Indirect Method

Apply same pattern to:

-   Indirect Method adjustments (if they have account_id)

## Backend - Already Working ✅

**Endpoint**: `/finance/cashflow/account-details/{id}`

**Method**: `CashFlowController::getAccountDetails()`

**Response**:

```json
{
    "success": true,
    "data": {
        "account": {
            "id": 1,
            "code": "1-1-001",
            "name": "Kas",
            "type": "asset"
        },
        "transactions": [
            {
                "id": 1,
                "transaction_date": "2025-11-01",
                "transaction_number": "JU-001",
                "description": "Penerimaan kas",
                "debit": 1000000,
                "credit": 0,
                "book_name": "Buku Umum"
            }
        ],
        "summary": {
            "total_transactions": 10,
            "total_debit": 5000000,
            "total_credit": 3000000,
            "net_cash_flow": 2000000
        }
    }
}
```

## Frontend - Already Working ✅

**Method**: `viewAccountDetails(accountId)`

```javascript
async viewAccountDetails(accountId) {
  this.showAccountModal = true;
  this.isLoadingAccountDetails = true;

  try {
    const params = new URLSearchParams({
      start_date: this.filters.start_date,
      end_date: this.filters.end_date
    });

    if (this.filters.book_id) {
      params.append('book_id', this.filters.book_id);
    }

    const response = await fetch(`/finance/cashflow/account-details/${accountId}?${params}`);
    const result = await response.json();

    if (result.success) {
      this.accountDetails = result.data;
    }
  } catch (error) {
    this.accountDetailsError = 'Terjadi kesalahan';
  } finally {
    this.isLoadingAccountDetails = false;
  }
}
```

## Modal Template - Already Exists ✅

Modal sudah ada dengan fitur:

-   Header dengan nama akun
-   Summary cards (Total Transaksi, Debit, Kredit, Net Cash Flow)
-   Table transaksi dengan detail
-   Loading state
-   Error state
-   Close button

## Implementation Steps

### Step 1: Update Direct Method Template

```blade
{{-- Operating Activities - Direct --}}
<div class="px-6 py-4 space-y-2">
  <template x-for="item in directCashFlow.operating" :key="item.id">
    <div>
      <div class="flex justify-between items-center py-1 hover:bg-slate-50 rounded px-2 transition-colors"
           :style="'padding-left: ' + (item.level * 20) + 'px'">
        <div class="flex items-center gap-2">
          <!-- Expand/Collapse button -->
          <button x-show="item.children && item.children.length > 0"
                  @click="toggleItem(item.id)"
                  class="text-slate-400 hover:text-slate-600">
            <i :class="isExpanded(item.id) ? 'bx bx-chevron-down' : 'bx bx-chevron-right'"></i>
          </button>

          <!-- Clickable account name -->
          <button x-show="!item.is_header && item.id"
                  @click="viewAccountDetails(item.id)"
                  class="text-slate-600 hover:text-blue-600 hover:underline cursor-pointer text-left"
                  x-text="item.name">
          </button>

          <!-- Non-clickable header -->
          <span x-show="item.is_header"
                class="font-semibold text-slate-700"
                x-text="item.name">
          </span>

          <!-- Account code -->
          <span x-show="item.code"
                class="text-xs text-slate-400"
                x-text="'(' + item.code + ')'">
          </span>
        </div>

        <!-- Amount -->
        <div :class="[item.is_header ? 'font-semibold' : '', item.amount >= 0 ? 'text-green-600' : 'text-red-600']"
             x-text="formatCurrency(item.amount)">
        </div>
      </div>

      <!-- Children (if expanded) -->
      <template x-if="item.children && item.children.length > 0 && isExpanded(item.id)">
        <div x-html="renderChildren(item.children)"></div>
      </template>
    </div>
  </template>
</div>
```

### Step 2: Update renderChildren() Method

Add click handler in HTML generation:

```javascript
renderChildren(children) {
  let html = '';
  children.forEach(child => {
    const paddingLeft = child.level * 20;
    const amountClass = child.amount >= 0 ? 'text-green-600' : 'text-red-600';
    const nameClass = child.is_header ? 'font-semibold text-slate-700' : 'text-slate-600';

    html += `<div class="flex justify-between items-center py-1 hover:bg-slate-50 rounded px-2" style="padding-left: ${paddingLeft}px">`;
    html += `<div class="flex items-center gap-2">`;

    // Clickable account name
    if (!child.is_header && child.id) {
      html += `<button onclick="Alpine.store('cashflow').viewAccountDetails(${child.id})"
                       class="text-slate-600 hover:text-blue-600 hover:underline cursor-pointer text-left">
                 ${child.name}
               </button>`;
    } else {
      html += `<span class="${nameClass}">${child.name}</span>`;
    }

    if (child.code) {
      html += `<span class="text-xs text-slate-400">(${child.code})</span>`;
    }
    html += `</div>`;
    html += `<div class="${amountClass}">${this.formatCurrency(child.amount)}</div>`;
    html += `</div>`;

    if (child.children && child.children.length > 0) {
      html += this.renderChildren(child.children);
    }
  });
  return html;
}
```

## Visual Indicators

### Clickable Accounts:

-   Text color: slate-600
-   Hover: blue-600 + underline
-   Cursor: pointer
-   Transition: smooth color change

### Non-Clickable Headers:

-   Text color: slate-700
-   Font weight: semibold
-   No hover effect
-   Normal cursor

### Row Hover:

-   Background: slate-50
-   Border radius: rounded
-   Transition: smooth

## Testing Checklist

-   [ ] Click pada akun child → Modal muncul
-   [ ] Modal menampilkan nama akun yang benar
-   [ ] Transaksi ditampilkan dengan lengkap
-   [ ] Summary cards menampilkan total yang benar
-   [ ] Loading state muncul saat fetch data
-   [ ] Error state muncul jika gagal
-   [ ] Close button berfungsi
-   [ ] Click away menutup modal
-   [ ] Hover effect pada akun berfungsi
-   [ ] Header tidak bisa diklik
-   [ ] Child accounts bisa diklik

## Status

⏳ **READY TO IMPLEMENT** - Semua komponen sudah ada, tinggal update template

## Estimated Time

-   Template update: 15 minutes
-   Testing: 10 minutes
-   Total: ~25 minutes

## Notes

-   Modal template sudah lengkap dan bagus
-   Backend endpoint sudah berfungsi
-   Frontend method sudah ada
-   Hanya perlu update template untuk add click handler
-   Implementasi straightforward karena infrastruktur sudah ada
