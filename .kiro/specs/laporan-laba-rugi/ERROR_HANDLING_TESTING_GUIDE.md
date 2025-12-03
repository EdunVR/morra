# Error Handling & Validation Testing Guide

## Quick Testing Checklist

### 1. Outlet Validation Testing

#### Test Case 1.1: Missing Outlet

**Steps:**

1. Open Laporan Laba Rugi page
2. Clear outlet selection (if auto-selected)
3. Try to load data

**Expected Result:**

-   ⚠️ Warning notification: "Pilih outlet terlebih dahulu"
-   Error banner displayed
-   No API call made

#### Test Case 1.2: Export Without Outlet

**Steps:**

1. Clear outlet selection
2. Click Export → XLSX or PDF

**Expected Result:**

-   ⚠️ Warning notification: "Pilih outlet terlebih dahulu"
-   No download triggered

#### Test Case 1.3: Print Without Outlet

**Steps:**

1. Clear outlet selection
2. Click Print button

**Expected Result:**

-   ⚠️ Warning notification: "Pilih outlet terlebih dahulu"
-   Print dialog not opened

---

### 2. Date Range Validation Testing

#### Test Case 2.1: Missing Dates

**Steps:**

1. Select an outlet
2. Clear start_date or end_date
3. Try to load data

**Expected Result:**

-   ⚠️ Warning notification: "Tanggal mulai dan tanggal akhir wajib diisi"
-   No API call made

#### Test Case 2.2: Invalid Date Range (end < start)

**Steps:**

1. Select an outlet
2. Set start_date: 2024-01-31
3. Set end_date: 2024-01-01
4. Try to load data

**Expected Result:**

-   ⚠️ Warning notification: "Tanggal akhir harus sama atau setelah tanggal mulai"
-   No API call made

#### Test Case 2.3: Valid Date Range

**Steps:**

1. Select an outlet
2. Set start_date: 2024-01-01
3. Set end_date: 2024-01-31
4. Load data

**Expected Result:**

-   ✅ Data loaded successfully
-   No error messages

---

### 3. Comparison Mode Validation Testing

#### Test Case 3.1: Comparison Without Dates

**Steps:**

1. Select an outlet and valid date range
2. Enable comparison mode
3. Clear comparison dates
4. Try to load data

**Expected Result:**

-   ⚠️ Warning notification: "Tanggal pembanding wajib diisi saat mode perbandingan aktif"
-   No API call made

#### Test Case 3.2: Invalid Comparison Date Range

**Steps:**

1. Enable comparison mode
2. Set comparison_start_date: 2023-12-31
3. Set comparison_end_date: 2023-12-01
4. Try to load data

**Expected Result:**

-   ⚠️ Warning notification: "Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding"
-   No API call made

#### Test Case 3.3: Valid Comparison

**Steps:**

1. Enable comparison mode
2. Set valid comparison dates
3. Load data

**Expected Result:**

-   ✅ Data loaded with comparison
-   Comparison columns visible
-   Comparison changes calculated

---

### 4. Backend Validation Testing

#### Test Case 4.1: Backend Validation Error (422)

**Steps:**

1. Use browser DevTools Network tab
2. Manually send request with invalid data:
    ```
    GET /finance/profit-loss/data?outlet_id=999&start_date=2024-01-31&end_date=2024-01-01
    ```

**Expected Result:**

-   ❌ HTTP 422 response
-   Error notification displayed
-   Error banner shown with validation messages

#### Test Case 4.2: Server Error (500)

**Steps:**

1. Simulate server error (if possible)
2. Try to load data

**Expected Result:**

-   ❌ HTTP 500 response
-   Error notification: "Gagal mengambil data: [error message]"
-   Error banner displayed

---

### 5. Empty Data State Testing

#### Test Case 5.1: No Transactions in Period

**Steps:**

1. Select an outlet
2. Select a period with no transactions (e.g., future date)
3. Load data

**Expected Result:**

-   ℹ️ Info notification: "Tidak ada data untuk periode yang dipilih"
-   Empty state displayed with:
    -   Large chart icon
    -   "Tidak Ada Data" heading
    -   Period information
    -   "Muat Ulang Data" button
    -   "Coba Periode Lain" button

#### Test Case 5.2: Empty State Actions

**Steps:**

1. In empty state, click "Muat Ulang Data"

**Expected Result:**

-   Data reloaded
-   Loading state shown

**Steps:**

1. In empty state, click "Coba Periode Lain"

**Expected Result:**

-   Period changed to "Bulan Ini"
-   Data reloaded

---

### 6. Error Display Testing

#### Test Case 6.1: Error Banner Display

**Steps:**

1. Trigger any validation error
2. Observe error banner

**Expected Result:**

-   Red error banner displayed
-   Error icon visible
-   "Terjadi Kesalahan" heading
-   Detailed error message
-   Close button (X) visible

#### Test Case 6.2: Error Banner Dismissal

**Steps:**

1. Display error banner
2. Click close button (X)

**Expected Result:**

-   Error banner dismissed
-   Error state cleared

---

### 7. Notification System Testing

#### Test Case 7.1: Error Notification

**Steps:**

1. Trigger validation error

**Expected Result:**

-   🔴 Red toast notification
-   Error icon (bx-error-circle)
-   Error message
-   Close button
-   Auto-dismiss after 5 seconds

#### Test Case 7.2: Success Notification

**Steps:**

1. Successfully export data

**Expected Result:**

-   🟢 Green toast notification
-   Success icon (bx-check-circle)
-   Success message
-   Auto-dismiss after 5 seconds

#### Test Case 7.3: Warning Notification

**Steps:**

1. Try action without outlet

**Expected Result:**

-   🟠 Orange toast notification
-   Warning icon (bx-error)
-   Warning message
-   Auto-dismiss after 5 seconds

#### Test Case 7.4: Info Notification

**Steps:**

1. Load data with empty result

**Expected Result:**

-   🔵 Blue toast notification
-   Info icon (bx-info-circle)
-   Info message
-   Auto-dismiss after 5 seconds

#### Test Case 7.5: Multiple Notifications

**Steps:**

1. Trigger multiple errors quickly

**Expected Result:**

-   Multiple notifications stack vertically
-   Each notification independent
-   Each can be dismissed separately
-   Each auto-dismisses after 5 seconds

#### Test Case 7.6: Manual Notification Dismissal

**Steps:**

1. Show notification
2. Click close button before auto-dismiss

**Expected Result:**

-   Notification dismissed immediately
-   Smooth fade-out animation

---

### 8. Export Validation Testing

#### Test Case 8.1: Export XLSX Validation

**Steps:**

1. Clear outlet
2. Click Export → XLSX

**Expected Result:**

-   ⚠️ Warning notification
-   No download triggered

**Steps:**

1. Select outlet but invalid date range
2. Click Export → XLSX

**Expected Result:**

-   ⚠️ Warning notification
-   No download triggered

**Steps:**

1. Valid data
2. Click Export → XLSX

**Expected Result:**

-   ✅ Success notification
-   File download triggered

#### Test Case 8.2: Export PDF Validation

**Steps:**

1. Follow same steps as XLSX test

**Expected Result:**

-   Same validation behavior
-   PDF download on success

---

### 9. Print Validation Testing

#### Test Case 9.1: Print Without Data

**Steps:**

1. Clear outlet
2. Click Print

**Expected Result:**

-   ⚠️ Warning notification
-   Print dialog not opened

#### Test Case 9.2: Print Before Data Loaded

**Steps:**

1. Select outlet
2. Immediately click Print before data loads

**Expected Result:**

-   ⚠️ Warning notification: "Data belum dimuat. Silakan tunggu sebentar."
-   Print dialog not opened

#### Test Case 9.3: Successful Print

**Steps:**

1. Load data successfully
2. Click Print

**Expected Result:**

-   Print dialog opened
-   Print-optimized layout shown
-   Charts hidden
-   Table visible

---

### 10. Network Error Testing

#### Test Case 10.1: Network Timeout

**Steps:**

1. Disable network (or use DevTools to simulate slow network)
2. Try to load data

**Expected Result:**

-   ❌ Error notification: "Terjadi kesalahan saat memuat data. Silakan coba lagi."
-   Error banner displayed

#### Test Case 10.2: Network Recovery

**Steps:**

1. After network error, restore network
2. Click "Muat Ulang Data"

**Expected Result:**

-   Data loaded successfully
-   Error cleared
-   ✅ Success state

---

## Automated Testing Commands

### Run Diagnostics

```bash
# Check for syntax errors
php artisan route:list | grep profit-loss
```

### Manual API Testing with cURL

#### Test Valid Request

```bash
curl -X GET "http://localhost/finance/profit-loss/data?outlet_id=1&start_date=2024-01-01&end_date=2024-01-31"
```

#### Test Missing Outlet

```bash
curl -X GET "http://localhost/finance/profit-loss/data?start_date=2024-01-01&end_date=2024-01-31"
```

#### Test Invalid Date Range

```bash
curl -X GET "http://localhost/finance/profit-loss/data?outlet_id=1&start_date=2024-01-31&end_date=2024-01-01"
```

#### Test Invalid Comparison

```bash
curl -X GET "http://localhost/finance/profit-loss/data?outlet_id=1&start_date=2024-01-01&end_date=2024-01-31&comparison=true"
```

---

## Expected Validation Messages

### Backend Validation Messages (Indonesian)

-   ✅ "Outlet wajib dipilih"
-   ✅ "Outlet tidak ditemukan"
-   ✅ "Tanggal mulai wajib diisi"
-   ✅ "Tanggal akhir wajib diisi"
-   ✅ "Format tanggal mulai tidak valid"
-   ✅ "Format tanggal akhir tidak valid"
-   ✅ "Tanggal akhir harus sama atau setelah tanggal mulai"
-   ✅ "Tanggal mulai pembanding wajib diisi saat mode perbandingan aktif"
-   ✅ "Tanggal akhir pembanding wajib diisi saat mode perbandingan aktif"
-   ✅ "Format tanggal mulai pembanding tidak valid"
-   ✅ "Format tanggal akhir pembanding tidak valid"
-   ✅ "Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding"

### Frontend Validation Messages (Indonesian)

-   ✅ "Pilih outlet terlebih dahulu"
-   ✅ "Tanggal mulai dan tanggal akhir wajib diisi"
-   ✅ "Tanggal akhir harus sama atau setelah tanggal mulai"
-   ✅ "Tanggal pembanding wajib diisi saat mode perbandingan aktif"
-   ✅ "Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding"
-   ✅ "Data belum dimuat. Silakan tunggu sebentar."
-   ✅ "Tidak ada data untuk periode yang dipilih"
-   ✅ "Gagal memuat data laporan laba rugi"
-   ✅ "Terjadi kesalahan saat memuat data. Silakan coba lagi."
-   ✅ "Export XLSX berhasil dimulai"
-   ✅ "Export PDF berhasil dimulai"
-   ✅ "Gagal mengekspor data ke XLSX"
-   ✅ "Gagal mengekspor data ke PDF"
-   ✅ "Gagal mencetak laporan"

---

## Browser Testing

### Recommended Browsers

-   ✅ Chrome/Edge (latest)
-   ✅ Firefox (latest)
-   ✅ Safari (latest)

### Responsive Testing

-   ✅ Desktop (1920x1080)
-   ✅ Tablet (768x1024)
-   ✅ Mobile (375x667)

### Accessibility Testing

-   ✅ Keyboard navigation
-   ✅ Screen reader compatibility
-   ✅ Color contrast
-   ✅ Focus indicators

---

## Performance Testing

### Load Time

-   ✅ Initial page load < 2s
-   ✅ Data fetch < 3s
-   ✅ Chart rendering < 1s

### Notification Performance

-   ✅ Notification appears instantly
-   ✅ Animation smooth (60fps)
-   ✅ Auto-dismiss timing accurate (5s)
-   ✅ Multiple notifications don't lag

---

## Regression Testing

After implementing error handling, verify:

-   ✅ Normal data loading still works
-   ✅ Charts still render correctly
-   ✅ Export still works
-   ✅ Print still works
-   ✅ Comparison mode still works
-   ✅ All existing features unaffected

---

## Sign-off Checklist

-   [ ] All validation rules working
-   [ ] All error messages displayed correctly
-   [ ] Empty state displays properly
-   [ ] Notifications work as expected
-   [ ] No console errors
-   [ ] No visual glitches
-   [ ] Responsive on all devices
-   [ ] Accessible to all users
-   [ ] Performance acceptable
-   [ ] No regressions in existing features

---

## Notes

-   Test with real data when possible
-   Test edge cases (very long error messages, multiple errors, etc.)
-   Test with different user roles/permissions
-   Test with different outlets
-   Test with different date ranges
-   Document any issues found
-   Verify fixes before marking complete
