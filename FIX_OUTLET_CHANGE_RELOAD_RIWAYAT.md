# 🔧 Fix: Reload Riwayat Penyusutan Saat Outlet Berubah

## 🐛 Problem

Saat user mengganti outlet dari dropdown, riwayat penyusutan tidak ter-update untuk menampilkan data dari outlet yang baru dipilih. Data masih menampilkan riwayat dari outlet sebelumnya.

## 🔍 Root Cause

Dropdown outlet hanya memanggil `loadAssets()` dan `reloadCharts()`, tetapi tidak memanggil `loadDepreciationHistory()`. Akibatnya:

-   Tabel aset ter-update ✅
-   Chart ter-update ✅
-   Riwayat penyusutan TIDAK ter-update ❌

## ✅ Solution

### 1. Create `onOutletChange()` Method

Membuat method khusus untuk handle perubahan outlet yang akan:

1. Reset filter depreciation ke "all" (show all assets)
2. Reset filter month ke empty (show all months)
3. Reload semua data (assets, charts, depreciation history)

**File**: `resources/views/admin/finance/aktiva-tetap/index.blade.php`

```javascript
onOutletChange() {
  // Reset depreciation filters to show all assets when outlet changes
  this.depreciationFilters.asset_id = 'all';
  this.depreciationFilters.month = '';

  console.log('Outlet changed to:', this.filters.outlet_id);
  console.log('Reset depreciation filters to show all assets');

  // Reload all data for new outlet
  this.loadAssets();
  this.reloadCharts();
  this.loadDepreciationHistory(); // ✅ Added
}
```

### 2. Update Outlet Dropdown Event

**Before:**

```html
<select
    x-model="filters.outlet_id"
    @change="loadAssets(); reloadCharts();"
    class="..."
></select>
```

**After:**

```html
<select
    x-model="filters.outlet_id"
    @change="onOutletChange()"
    class="..."
></select>
```

### 3. Update `resetFilters()` Method

Juga reset depreciation filters saat user klik "Reset Filter":

```javascript
resetFilters() {
  // Reset to default outlet
  @if(auth()->user() && auth()->user()->id_outlet)
    const defaultOutlet = {{ auth()->user()->id_outlet }};
  @else
    const defaultOutlet = this.outlets.length > 0 ? this.outlets[0].id_outlet : null;
  @endif

  this.filters = {
    outlet_id: defaultOutlet,
    status: 'all',
    category: 'all'
  };

  // ✅ ADD: Also reset depreciation filters
  this.depreciationFilters.asset_id = 'all';
  this.depreciationFilters.month = '';

  this.loadAssets();
  this.loadDepreciationHistory(); // ✅ Added
}
```

## 🎯 Expected Behavior

### Before Fix

```
1. User di Outlet A
2. Riwayat penyusutan shows data from Outlet A
3. User ganti ke Outlet B
4. Tabel aset updates ✅
5. Chart updates ✅
6. Riwayat penyusutan STILL shows Outlet A data ❌
```

### After Fix

```
1. User di Outlet A
2. Riwayat penyusutan shows data from Outlet A
3. User ganti ke Outlet B
4. Console: "Outlet changed to: 2"
5. Console: "Reset depreciation filters to show all assets"
6. Tabel aset updates ✅
7. Chart updates ✅
8. Riwayat penyusutan updates to Outlet B data ✅
9. Filter asset reset to "Semua Aset" ✅
10. Filter month reset to empty ✅
```

## 🔄 Data Flow

```
User changes outlet dropdown
         ↓
   onOutletChange()
         ↓
    ┌────┴────┐
    │  Reset  │
    │ Filters │
    └────┬────┘
         ↓
    ┌────┴────────────────┐
    │                     │
    ↓                     ↓
depreciationFilters   depreciationFilters
  .asset_id = 'all'     .month = ''
         ↓
    ┌────┴────────────────────┐
    │                         │
    ↓                         ↓
loadAssets()            reloadCharts()
    │                         │
    └────────┬────────────────┘
             ↓
    loadDepreciationHistory()
             ↓
    Display all depreciation
    records for new outlet
```

## 🧪 Testing

### Test Case 1: Change Outlet

1. Buka halaman Aktiva Tetap di Outlet A
2. Scroll ke "Riwayat Penyusutan"
3. Note: Riwayat shows data from Outlet A
4. Ganti outlet dropdown ke Outlet B
5. **Expected**:
    - Tabel aset updates to Outlet B
    - Chart updates to Outlet B
    - Riwayat penyusutan updates to Outlet B ✅
    - Filter asset reset to "Semua Aset" ✅
    - Filter month reset to empty ✅
    - Console: "Outlet changed to: 2"
    - Console: "Reset depreciation filters to show all assets"

### Test Case 2: Filter Asset Then Change Outlet

1. Di Outlet A, filter riwayat by specific asset
2. Riwayat shows only that asset's records
3. Ganti outlet ke Outlet B
4. **Expected**:
    - Filter asset automatically reset to "Semua Aset"
    - Riwayat shows ALL assets from Outlet B
    - Not filtered by previous asset selection

### Test Case 3: Filter Month Then Change Outlet

1. Di Outlet A, filter riwayat by specific month
2. Riwayat shows only that month's records
3. Ganti outlet ke Outlet B
4. **Expected**:
    - Filter month automatically reset to empty
    - Riwayat shows ALL months from Outlet B
    - Not filtered by previous month selection

### Test Case 4: Reset Filters Button

1. Set various filters (status, category, asset, month)
2. Klik "Reset Filter" button
3. **Expected**:
    - All filters reset to default
    - Depreciation filters also reset ✅
    - Riwayat shows all data

## 📊 Console Output Examples

### Outlet Change

```
Outlet changed to: 2
Reset depreciation filters to show all assets
Depreciation filters: {asset_id: "all", month: "", outlet_id: 2}
Showing all assets (no asset_id filter)
Loading depreciation history from: .../depreciation/history?per_page=1000&outlet_id=2
Depreciation history loaded: 85 records
Showing 85 of 85 total records
```

### Reset Filters

```
Depreciation filters: {asset_id: "all", month: "", outlet_id: 1}
Showing all assets (no asset_id filter)
Loading depreciation history from: .../depreciation/history?per_page=1000&outlet_id=1
Depreciation history loaded: 150 records
Showing 150 of 150 total records
```

## 🔑 Key Changes

| Change                               | Purpose                            |
| ------------------------------------ | ---------------------------------- |
| Created `onOutletChange()`           | Centralized outlet change handling |
| Reset `depreciationFilters.asset_id` | Show all assets from new outlet    |
| Reset `depreciationFilters.month`    | Show all months from new outlet    |
| Call `loadDepreciationHistory()`     | Reload data for new outlet         |
| Updated `resetFilters()`             | Also reset depreciation filters    |
| Added console logging                | Easy debugging                     |

## 💡 Benefits

1. **Consistent UX**: All data updates when outlet changes
2. **Clean State**: Filters reset to show all data from new outlet
3. **No Confusion**: User doesn't see old outlet's data
4. **Easy Debugging**: Console logs show what's happening
5. **Maintainable**: Centralized logic in `onOutletChange()`

## ✅ Status

**FIXED** ✅

Riwayat penyusutan sekarang otomatis ter-update dan menampilkan semua aset saat outlet berubah, dengan filter yang ter-reset ke default.

**Ready for testing!** 🚀
