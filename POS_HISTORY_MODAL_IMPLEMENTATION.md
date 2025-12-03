# ✅ POS History Modal Implementation

## 🎯 Objective

Menambahkan tombol dan modal History di halaman POS untuk melihat riwayat transaksi dengan fitur:

1. Filter by status (Lunas/BON)
2. Filter by date range
3. Search by nomor transaksi
4. Print nota dari history
5. Responsive table view

## 📝 Changes Made

### 1. **View Update - POS Index**

**File:** `resources/views/admin/penjualan/pos/index.blade.php`

#### A. Header - Added History Button

**Location:** Next to Setting COA button

```html
<button
    x-on:click="showHistoryModal = true; loadHistory()"
    class="text-xs px-3 py-1 rounded-full border border-slate-200 hover:bg-slate-50"
>
    📋 History
</button>
<button
    x-on:click="showCoaModal = true"
    class="text-xs px-3 py-1 rounded-full border border-slate-200 hover:bg-slate-50"
>
    ⚙️ Setting COA
</button>
```

#### B. Modal History

**Features:**

-   Full-screen modal with max-width 6xl
-   Filter section (Status, Date Range, Search)
-   Responsive table with sticky header
-   Loading state
-   Empty state
-   Print button for each transaction

**Structure:**

```html
<div
    x-show="showHistoryModal"
    x-transition
    class="fixed inset-0 bg-black/30 z-50"
>
    <div class="w-full max-w-6xl rounded-2xl bg-white">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b bg-slate-50">
            <h2>📋 History Transaksi POS</h2>
            <button @click="showHistoryModal=false">×</button>
        </div>

        <!-- Filter Section -->
        <div class="p-4 border-b bg-white">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <select x-model="historyFilter.status" @change="loadHistory()">
                    <option value="all">Semua Status</option>
                    <option value="lunas">Lunas</option>
                    <option value="menunggu">Menunggu (BON)</option>
                </select>
                <input
                    type="date"
                    x-model="historyFilter.start_date"
                    @change="loadHistory()"
                />
                <input
                    type="date"
                    x-model="historyFilter.end_date"
                    @change="loadHistory()"
                />
                <input
                    type="text"
                    x-model="historyFilter.search"
                    @input.debounce.500ms="loadHistory()"
                    placeholder="No transaksi..."
                />
            </div>
        </div>

        <!-- Loading State -->
        <div
            x-show="historyLoading"
            class="flex-1 flex items-center justify-center p-8"
        >
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"
            ></div>
            <p>Memuat history...</p>
        </div>

        <!-- Table -->
        <div x-show="!historyLoading" class="flex-1 overflow-auto p-4">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 sticky top-0">
                    <tr>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in historyData" :key="item.id">
                        <tr class="hover:bg-slate-50">
                            <td x-text="item.no_transaksi"></td>
                            <td x-text="formatDateTime(item.tanggal)"></td>
                            <td
                                x-text="item.member ? item.member.nama : 'Umum'"
                            ></td>
                            <td x-text="formatRupiah(item.total)"></td>
                            <td>
                                <span
                                    :class="paymentBadgeClass(item.jenis_pembayaran)"
                                >
                                    {{ payment type }}
                                </span>
                            </td>
                            <td>
                                <span :class="statusBadgeClass(item.status)">
                                    {{ status }}
                                </span>
                            </td>
                            <td>
                                <button @click="printHistoryItem(item.id)">
                                    <i class="bx bx-printer"></i> Print
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Empty State -->
            <div x-show="historyData.length === 0" class="text-center py-12">
                <i class="bx bx-receipt text-3xl text-slate-400"></i>
                <p>Tidak ada transaksi</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t bg-slate-50">
            <div>Total: <b x-text="historyData.length"></b> transaksi</div>
            <button @click="showHistoryModal=false">Tutup</button>
        </div>
    </div>
</div>
```

#### C. JavaScript Data Structure

**Added variables:**

```javascript
showHistoryModal: false,
historyLoading: false,
historyData: [],
historyFilter: {
  status: 'all',
  start_date: new Date(new Date().setDate(new Date().getDate() - 7)).toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  search: ''
}
```

**Default date range:** Last 7 days

#### D. JavaScript Functions

**loadHistory():**

```javascript
async loadHistory() {
  this.historyLoading = true;
  try {
    const params = new URLSearchParams({
      outlet_id: this.state.outlet,
      status: this.historyFilter.status,
      start_date: this.historyFilter.start_date,
      end_date: this.historyFilter.end_date,
      search: this.historyFilter.search
    });

    const response = await fetch(`{{ route('penjualan.pos.history.data') }}?${params}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = await response.json();

    if (data.success) {
      this.historyData = data.data || [];
    }
  } catch(e) {
    console.error('Error loading history:', e);
    this.historyData = [];
  } finally {
    this.historyLoading = false;
  }
}
```

**printHistoryItem(id):**

```javascript
printHistoryItem(id) {
  const url = `{{ route('penjualan.pos.print', ':id') }}`.replace(':id', id) + '?type=besar';
  window.open(url, '_blank');
}
```

**formatDateTime(dateStr):**

```javascript
formatDateTime(dateStr) {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}
```

### 2. **Controller Update - PosController**

**File:** `app/Http/Controllers/PosController.php`

#### Updated historyData() Method

**Before:** Only DataTables response

```php
public function historyData(Request $request)
{
    $query = PosSale::with(['outlet', 'member', 'user', 'items'])
        ->byOutlet($outletId)
        ->status($status)
        ->dateRange($startDate, $endDate)
        ->orderBy('tanggal', 'desc');

    return DataTables::of($query)
        // ... DataTables columns
        ->make(true);
}
```

**After:** Support both JSON and DataTables

```php
public function historyData(Request $request)
{
    $outletId = $request->get('outlet_id', 'all');
    $status = $request->get('status', 'all');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    $search = $request->get('search');

    $query = PosSale::with(['outlet', 'member', 'user', 'items'])
        ->byOutlet($outletId)
        ->status($status)
        ->dateRange($startDate, $endDate)
        ->when($search, function($q) use ($search) {
            $q->where('no_transaksi', 'like', "%{$search}%");
        })
        ->orderBy('tanggal', 'desc');

    // If AJAX request for modal, return JSON
    if ($request->wantsJson() || $request->ajax()) {
        $data = $query->limit(100)->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Otherwise return DataTables
    return DataTables::of($query)
        // ... DataTables columns
        ->make(true);
}
```

**Key Changes:**

-   ✅ Added `$search` parameter
-   ✅ Added search filter by `no_transaksi`
-   ✅ Check if AJAX/JSON request
-   ✅ Return JSON for modal (limit 100 records)
-   ✅ Return DataTables for history page

## ✨ Features

### 1. **Quick Access**

-   Button di header POS (next to Setting COA)
-   One-click to open history modal
-   Auto-load last 7 days transactions

### 2. **Powerful Filters**

-   **Status Filter:** All / Lunas / BON
-   **Date Range:** Start date & End date
-   **Search:** By nomor transaksi
-   **Auto-refresh:** On filter change

### 3. **Responsive Table**

-   Sticky header (stays visible when scrolling)
-   Hover effect on rows
-   Color-coded badges:
    -   Payment type (Cash/Transfer/QRIS)
    -   Status (Lunas/BON)

### 4. **Actions**

-   **Print Button:** Open nota in new tab
-   Quick access to print past transactions

### 5. **UX Enhancements**

-   Loading state with spinner
-   Empty state with icon
-   Transaction count in footer
-   Click-away to close modal

## 🧪 Testing Guide

### Test 1: Open History Modal

1. Buka halaman **POS** (`/penjualan/pos`)
2. Klik button **📋 History** di header
3. **Verify:** Modal terbuka
4. **Verify:** Loading spinner muncul
5. **Verify:** Data transaksi 7 hari terakhir muncul

### Test 2: Filter by Status

1. Di modal History, pilih filter **Status: Lunas**
2. **Verify:** Hanya transaksi lunas yang muncul
3. Pilih **Status: Menunggu (BON)**
4. **Verify:** Hanya transaksi BON yang muncul
5. Pilih **Status: Semua Status**
6. **Verify:** Semua transaksi muncul

### Test 3: Filter by Date Range

1. Set **Tanggal Mulai:** 1 bulan lalu
2. Set **Tanggal Akhir:** Hari ini
3. **Verify:** Transaksi dalam range muncul
4. Set range yang tidak ada transaksi
5. **Verify:** Empty state muncul

### Test 4: Search by No Transaksi

1. Ketik nomor transaksi di search box
2. **Verify:** Auto-search dengan debounce 500ms
3. **Verify:** Hanya transaksi yang match muncul
4. Clear search
5. **Verify:** Semua transaksi muncul kembali

### Test 5: Print from History

1. Klik button **Print** pada salah satu transaksi
2. **Verify:** Nota terbuka di tab baru
3. **Verify:** PDF nota tampil dengan benar
4. **Verify:** Data transaksi sesuai

### Test 6: Empty State

1. Set filter yang tidak ada datanya
2. **Verify:** Empty state muncul:
    - Icon receipt
    - Text "Tidak ada transaksi"
    - Helper text

### Test 7: Responsive Table

1. Scroll table ke bawah
2. **Verify:** Header tetap visible (sticky)
3. Hover pada row
4. **Verify:** Background berubah (hover effect)

### Test 8: Close Modal

1. Klik button **Tutup** di footer
2. **Verify:** Modal tertutup
3. Buka modal lagi
4. Klik area di luar modal (click-away)
5. **Verify:** Modal tertutup

### Test 9: Transaction Count

1. Buka modal dengan berbagai filter
2. **Verify:** Footer menampilkan jumlah transaksi yang benar
3. Example: "Total: **15** transaksi"

### Test 10: Badge Colors

1. Check payment type badges:
    - **Cash:** Green badge 💵
    - **Transfer:** Blue badge 🏦
    - **QRIS:** Purple badge 📱
2. Check status badges:
    - **Lunas:** Green badge ✅
    - **BON:** Orange badge ⏳

## 📊 Data Flow

```
User clicks "📋 History" button
    ↓
showHistoryModal = true
    ↓
loadHistory() called
    ↓
Fetch from /penjualan/pos/history-data
    ↓
PosController@historyData
    ↓
Check if AJAX request
    ↓
Return JSON with transactions (limit 100)
    ↓
historyData populated
    ↓
Table rendered with Alpine.js
    ↓
User can filter/search/print
```

## 🎨 UI Components

### Payment Type Badges

```
💵 Cash      → bg-green-100 text-green-800
🏦 Transfer  → bg-blue-100 text-blue-800
📱 QRIS      → bg-purple-100 text-purple-800
```

### Status Badges

```
✅ Lunas     → bg-green-100 text-green-800
⏳ BON       → bg-orange-100 text-orange-800
```

### Button Styles

```
📋 History   → text-xs px-3 py-1 rounded-full border
🖨️ Print     → bg-primary-50 text-primary-600 hover:bg-primary-100
```

## 🔄 Performance

-   **Limit:** 100 transactions per load
-   **Debounce:** 500ms on search input
-   **Lazy Load:** Data only loaded when modal opened
-   **Efficient:** Uses existing route and controller

## 📦 Files Modified

1. ✅ `resources/views/admin/penjualan/pos/index.blade.php`

    - Added History button in header
    - Added History modal
    - Added JavaScript functions
    - Added data structure

2. ✅ `app/Http/Controllers/PosController.php`
    - Updated `historyData()` method
    - Added JSON response support
    - Added search filter

## 🎯 Key Features Summary

1. ✅ History button next to Setting COA
2. ✅ Full-featured modal with filters
3. ✅ Status filter (All/Lunas/BON)
4. ✅ Date range filter
5. ✅ Search by transaction number
6. ✅ Print button for each transaction
7. ✅ Responsive table with sticky header
8. ✅ Loading & empty states
9. ✅ Color-coded badges
10. ✅ Transaction count display

---

**Status:** ✅ COMPLETE
**Date:** December 1, 2025
**Location:** POS Index Page - Header & Modal
**Impact:** Improved accessibility to transaction history without leaving POS page
