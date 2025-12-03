# Margin Report - NaN Fix

## Issue

Summary cards menampilkan `RpNaN` untuk Total Penjualan dan nilai lainnya.

## Root Cause

1. Data dari backend mungkin berisi nilai `null` atau `undefined`
2. JavaScript tidak melakukan type conversion otomatis
3. Operasi matematika dengan nilai non-numeric menghasilkan `NaN`
4. `formatRupiah()` tidak menangani nilai `NaN`

## Solutions Applied

### 1. Backend - Controller Type Casting

**File:** `app/Http/Controllers/MarginReportController.php`

#### Invoice Data Processing

```php
// Before
$profit = $detail->subtotal - ($detail->hpp * $detail->jumlah);

// After
$hpp = floatval($detail->hpp ?? 0);
$jumlah = floatval($detail->jumlah ?? 0);
$subtotal = floatval($detail->subtotal ?? 0);
$hargaJual = floatval($detail->harga_jual ?? 0);

$profit = $subtotal - ($hpp * $jumlah);
```

#### POS Data Processing

```php
// Before
$hpp = $item->produk ? $item->produk->calculateHppBarangDagang() : 0;
$profit = $item->subtotal - ($hpp * $item->kuantitas);

// After
$hpp = $item->produk ? floatval($item->produk->calculateHppBarangDagang()) : 0;
$kuantitas = floatval($item->kuantitas ?? 0);
$subtotal = floatval($item->subtotal ?? 0);
$harga = floatval($item->harga ?? 0);

$profit = $subtotal - ($hpp * $kuantitas);
```

### 2. Frontend - Safe Parsing

**File:** `resources/views/admin/penjualan/margin/index.blade.php`

#### Calculate Summary Function

```javascript
// Before
calculateSummary() {
  const data = this.filteredData;
  this.summary = {
    total_items: data.length,
    total_hpp: data.reduce((sum, item) => sum + (item.hpp * item.qty), 0),
    total_penjualan: data.reduce((sum, item) => sum + item.subtotal, 0),
    // ...
  };
}

// After
calculateSummary() {
  const data = this.filteredData || [];

  if (data.length === 0) {
    this.summary = {
      total_items: 0,
      total_hpp: 0,
      total_penjualan: 0,
      total_profit: 0,
      avg_margin: 0
    };
    return;
  }

  this.summary = {
    total_items: data.length,
    total_hpp: data.reduce((sum, item) => {
      const hpp = parseFloat(item.hpp) || 0;
      const qty = parseFloat(item.qty) || 0;
      return sum + (hpp * qty);
    }, 0),
    total_penjualan: data.reduce((sum, item) => {
      const subtotal = parseFloat(item.subtotal) || 0;
      return sum + subtotal;
    }, 0),
    // ...
  };
}
```

#### Format Rupiah Function

```javascript
// Before
formatRupiah(value) {
  if (!value && value !== 0) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value);
}

// After
formatRupiah(value) {
  const numValue = parseFloat(value);
  if (isNaN(numValue)) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(numValue);
}
```

#### Filter Data Function

```javascript
// Before
filterData() {
  if (!this.filters.search) {
    this.filteredData = this.marginData;
  } else {
    const search = this.filters.search.toLowerCase();
    this.filteredData = this.marginData.filter(item =>
      item.produk.toLowerCase().includes(search)
    );
  }
  this.calculateSummary();
}

// After
filterData() {
  if (!this.filters.search) {
    this.filteredData = this.marginData;
  } else {
    const search = this.filters.search.toLowerCase();
    this.filteredData = this.marginData.filter(item =>
      item.produk && item.produk.toLowerCase().includes(search)
    );
  }
  this.calculateSummary();
}
```

## Key Improvements

### 1. Type Safety

-   ✅ All numeric values converted to `float` in backend
-   ✅ All numeric values parsed with `parseFloat()` in frontend
-   ✅ Null coalescing operator (`??`) for default values
-   ✅ Logical OR (`||`) for fallback to 0

### 2. NaN Prevention

-   ✅ Check for `NaN` before formatting
-   ✅ Return 'Rp 0' for invalid values
-   ✅ Initialize summary with zeros for empty data
-   ✅ Safe null checks in filter function

### 3. Data Validation

-   ✅ Check if `filteredData` exists
-   ✅ Check if `data.length === 0`
-   ✅ Check if `item.produk` exists before calling methods
-   ✅ Use default values for missing properties

## Testing Checklist

### Test Cases

1. ✅ Empty data (no transactions)

    - Summary should show all zeros
    - No NaN values

2. ✅ Data with null values

    - Should default to 0
    - Calculations should work

3. ✅ Data with undefined values

    - Should default to 0
    - No errors in console

4. ✅ Normal data

    - Summary calculates correctly
    - All values display properly

5. ✅ Search/Filter
    - Summary updates correctly
    - No NaN after filtering

### Expected Results

```
Total Items: 10
Total HPP: Rp 500,000
Total Penjualan: Rp 750,000
Total Profit: Rp 250,000
Avg Margin: 33.33%
```

## Common Scenarios

### Scenario 1: Product without HPP

```php
// Backend handles it
$hpp = floatval($detail->hpp ?? 0); // Returns 0 if null

// Frontend handles it
const hpp = parseFloat(item.hpp) || 0; // Returns 0 if NaN
```

### Scenario 2: Empty Result Set

```javascript
// Frontend handles it
if (data.length === 0) {
    this.summary = {
        /* all zeros */
    };
    return;
}
```

### Scenario 3: Invalid Subtotal

```javascript
// Frontend handles it
const subtotal = parseFloat(item.subtotal) || 0;
```

## Browser Console Checks

After fix, verify no errors:

```javascript
// Should NOT see:
❌ NaN in calculations
❌ TypeError: Cannot read property 'toLowerCase' of undefined
❌ ReferenceError: item is not defined

// Should see:
✅ Clean console
✅ Proper numeric values
✅ Correct formatting
```

## Performance Impact

-   ✅ Minimal - Type conversion is fast
-   ✅ No additional API calls
-   ✅ Client-side calculations remain efficient

## Status

✅ **FIXED** - All NaN issues resolved

## Prevention Tips

### For Future Development

1. Always use `parseFloat()` or `Number()` for numeric operations
2. Always provide default values with `??` or `||`
3. Always validate data before calculations
4. Always check for `NaN` before formatting
5. Always handle empty arrays/objects

### Code Pattern

```javascript
// Good Pattern
const value = parseFloat(data.value) || 0;
const result = value * quantity;

// Bad Pattern
const result = data.value * quantity; // Can produce NaN
```

---

**Date:** December 1, 2024
**Status:** ✅ RESOLVED
**Impact:** High - Affects all summary calculations
**Priority:** Critical - User-facing display issue
