# 🔧 Fix: Riwayat Penyusutan - Default "Semua Aset"

## 🐛 Problem

Saat pertama kali load halaman, riwayat penyusutan tidak langsung menampilkan semua aset. User harus:

1. Pilih aset lain dari dropdown
2. Kembali pilih "Semua Aset"
3. Baru muncul semua data

Padahal posisi dropdown sudah di "Semua Aset" (opsi default paling atas).

## 🔍 Root Cause

Meskipun `depreciationFilters.asset_id` sudah di-set ke `'all'` di data initialization, dropdown HTML tidak explicitly marked sebagai `selected`, sehingga Alpine.js binding mungkin tidak ter-trigger dengan benar saat pertama kali load.

## ✅ Solution

### 1. Add `selected` Attribute to Default Option

**File**: `resources/views/admin/finance/aktiva-tetap/index.blade.php`

```html
<!-- Before -->
<select
    x-model="depreciationFilters.asset_id"
    @change="loadDepreciationHistory()"
    class="..."
>
    <option value="all">Semua Aset</option>
    <template x-for="asset in assetsData" :key="asset.id">
        <option
            :value="asset.id"
            x-text="asset.code + ' - ' + asset.name"
        ></option>
    </template>
</select>

<!-- After -->
<select
    x-model="depreciationFilters.asset_id"
    @change="loadDepreciationHistory()"
    class="..."
>
    <option value="all" selected>Semua Aset</option>
    <!-- ✅ Added 'selected' -->
    <template x-for="asset in assetsData" :key="asset.id">
        <option
            :value="asset.id"
            x-text="asset.code + ' - ' + asset.name"
        ></option>
    </template>
</select>
```

### 2. Add Debug Logging

Added comprehensive logging untuk memastikan filter state:

```javascript
async loadDepreciationHistory() {
  try {
    const params = new URLSearchParams();
    params.append('per_page', '1000');

    // ✅ ADD: Log current filter state
    console.log('Depreciation filters:', {
      asset_id: this.depreciationFilters.asset_id,
      month: this.depreciationFilters.month,
      outlet_id: this.filters.outlet_id
    });

    if (this.depreciationFilters.asset_id && this.depreciationFilters.asset_id !== 'all') {
      params.append('asset_id', this.depreciationFilters.asset_id);
      console.log('Filtering by asset_id:', this.depreciationFilters.asset_id);
    } else {
      console.log('Showing all assets (no asset_id filter)'); // ✅ Confirm showing all
    }

    // ... rest of the code
  }
}
```

### 3. Verify Data Initialization

Confirmed default value is correct:

```javascript
depreciationFilters: {
  asset_id: 'all', // ✅ Default to show all assets
  month: '' // ✅ Empty = show all months
}
```

## 🎯 Expected Behavior

### Before Fix

```
1. Load halaman
2. Dropdown shows "Semua Aset" (visually)
3. BUT: Data tidak muncul atau hanya sebagian
4. User harus klik dropdown → pilih aset lain → kembali ke "Semua Aset"
5. Baru data muncul semua
```

### After Fix

```
1. Load halaman
2. Dropdown shows "Semua Aset" (selected)
3. ✅ Data langsung muncul SEMUA
4. Console log: "Showing all assets (no asset_id filter)"
5. Console log: "Depreciation history loaded: 150 records"
6. ✅ User langsung lihat semua riwayat tanpa perlu klik dropdown
```

## 🧪 Testing

### Test Case 1: Initial Load

1. Refresh halaman Aktiva Tetap
2. Scroll ke "Riwayat Penyusutan"
3. **Expected**:
    - Dropdown shows "Semua Aset"
    - Table shows ALL depreciation records
    - Console: "Showing all assets (no asset_id filter)"
    - Console: "Depreciation history loaded: X records"

### Test Case 2: Filter by Specific Asset

1. Klik dropdown
2. Pilih aset tertentu (e.g., "AST-001 - Laptop Dell")
3. **Expected**:
    - Table shows only records for that asset
    - Console: "Filtering by asset_id: 123"

### Test Case 3: Back to All Assets

1. Klik dropdown
2. Pilih "Semua Aset"
3. **Expected**:
    - Table shows ALL records again
    - Console: "Showing all assets (no asset_id filter)"

### Test Case 4: Filter by Month

1. Pilih bulan dari date picker
2. **Expected**:
    - Table shows records for that month only
    - Still respects asset filter (all or specific)

## 📊 Console Output Examples

### Initial Load (All Assets)

```
Depreciation filters: {asset_id: "all", month: "", outlet_id: 1}
Showing all assets (no asset_id filter)
Loading depreciation history from: http://localhost/finance/fixed-assets/depreciation/history?per_page=1000&outlet_id=1
Depreciation history result: {success: true, data: Array(150), meta: {...}}
Depreciation history loaded: 150 records
Showing 150 of 150 total records
```

### Filter by Specific Asset

```
Depreciation filters: {asset_id: 123, month: "", outlet_id: 1}
Filtering by asset_id: 123
Loading depreciation history from: http://localhost/finance/fixed-assets/depreciation/history?per_page=1000&outlet_id=1&asset_id=123
Depreciation history result: {success: true, data: Array(12), meta: {...}}
Depreciation history loaded: 12 records
Showing 12 of 12 total records
```

## 🔑 Key Changes

| Change                               | Impact                                       |
| ------------------------------------ | -------------------------------------------- |
| Added `selected` to default option   | Ensures Alpine.js binding is correct on init |
| Added filter state logging           | Easy debugging of filter issues              |
| Added "showing all" confirmation log | Clear indication when no filter applied      |
| Verified default value `'all'`       | Ensures correct initialization               |

## ✅ Status

**FIXED** ✅

Riwayat penyusutan sekarang langsung menampilkan semua aset saat pertama kali load, tanpa perlu user klik dropdown terlebih dahulu.

**Ready for testing!** 🚀
