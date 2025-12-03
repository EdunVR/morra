# CRM Dashboard - Quick Test Guide

## 🧪 Testing Steps

### 1. Access Dashboard

```
URL: http://localhost/admin/crm
atau klik: Sidebar → Pelanggan (CRM) → Dashboard CRM
```

### 2. Verify Display

#### Customer Overview Cards (Top Row)

-   ✅ Total Pelanggan
-   ✅ Pelanggan Aktif (hijau)
-   ✅ Baru Bulan Ini (ungu)
-   ✅ Tidak Aktif (merah)

#### Sales Analytics (Second Row)

-   ✅ Total Revenue
-   ✅ Total Transaksi
-   ✅ Rata-rata Transaksi

#### Customer Segmentation

-   ✅ VIP (kuning)
-   ✅ Loyal (hijau)
-   ✅ Regular (biru)
-   ✅ New (ungu)
-   ✅ At Risk (merah)

### 3. Test Filters

#### Outlet Filter

```
1. Pilih "Semua Outlet" → Lihat semua data
2. Pilih outlet spesifik → Data ter-filter
3. Verify angka berubah sesuai outlet
```

#### Period Filter

```
1. Pilih "7 Hari" → Data 1 minggu terakhir
2. Pilih "30 Hari" → Data 1 bulan terakhir
3. Pilih "90 Hari" → Data 3 bulan terakhir
4. Pilih "1 Tahun" → Data 12 bulan terakhir
```

### 4. Verify Charts

#### Growth Trends Chart

-   ✅ Line chart dengan 6 bulan data
-   ✅ Label bulan (M Y format)
-   ✅ Data pelanggan baru per bulan
-   ✅ Smooth curve (tension: 0.4)

#### Customer Lifecycle Chart

-   ✅ Doughnut chart
-   ✅ 3 segments: New, Returning, Churned
-   ✅ Color coded: Purple, Green, Red

#### Revenue Forecast Chart

-   ✅ Historical data (solid line, blue)
-   ✅ Forecast data (dashed line, green)
-   ✅ 3 months prediction
-   ✅ Growth rate percentage displayed

### 5. Verify Tables

#### Top 10 Customers Table

```
Columns:
- Nama
- Telepon
- Transaksi (count)
- Total Belanja (Rp formatted)
- Rata-rata (Rp formatted)
- Segmen (color badge)

Verify:
✅ Sorted by total_spent DESC
✅ Max 10 rows
✅ Currency formatting correct
✅ Segment badges colored correctly
```

#### Piutang Analysis

```
Left Side:
✅ Total Piutang (orange)
✅ Jatuh Tempo (red)
✅ Count pelanggan

Right Side:
✅ Top 5 overdue customers
✅ Customer name & phone
✅ Amount (Rp formatted)
✅ Days overdue counter
```

### 6. Verify Predictions

#### Churn Risk Section

```
High Risk Customers:
✅ List of customers not purchasing >90 days
✅ Days since last purchase
✅ Strategy recommendation displayed
✅ Red background color

Medium Risk:
✅ Customers not purchasing >60 days
✅ Yellow/orange background
```

#### Upsell Opportunities

```
✅ Top 10 active customers
✅ Average purchase amount
✅ Purchase count
✅ Personalized recommendations:
   - VIP Membership (≥1jt)
   - Product Bundling (≥500rb)
   - Loyalty Program (<500rb)
✅ Green background color
```

### 7. Test Responsiveness

#### Desktop (≥1024px)

-   ✅ 4 columns for overview cards
-   ✅ 3 columns for sales analytics
-   ✅ 5 columns for segmentation
-   ✅ 2 columns for charts
-   ✅ Full table width

#### Tablet (768-1023px)

-   ✅ 2-3 columns adaptive
-   ✅ Charts stack properly
-   ✅ Table scrollable

#### Mobile (<768px)

-   ✅ Single column layout
-   ✅ Cards stack vertically
-   ✅ Charts responsive
-   ✅ Table horizontal scroll

### 8. Performance Check

#### Load Time

```
✅ Initial page load < 2s
✅ Filter change < 1s
✅ Chart rendering smooth
✅ No console errors
```

#### Data Accuracy

```
✅ Customer count matches database
✅ Revenue sum correct
✅ Segmentation logic accurate
✅ Piutang calculations correct
✅ Forecast reasonable
```

### 9. Browser Compatibility

Test on:

-   ✅ Chrome (latest)
-   ✅ Firefox (latest)
-   ✅ Edge (latest)
-   ✅ Safari (latest)

### 10. Console Check

Open browser console (F12) and verify:

```
✅ No JavaScript errors
✅ API calls successful (200 status)
✅ Chart.js loaded
✅ Alpine.js initialized
✅ Data fetched correctly
```

## 🐛 Common Issues & Solutions

### Issue 1: Charts Not Displaying

```
Solution:
- Check Chart.js CDN loaded
- Verify canvas elements exist
- Check console for errors
- Ensure data format correct
```

### Issue 2: Data Not Loading

```
Solution:
- Check API endpoints in routes
- Verify controller methods
- Check database connections
- Review query permissions
```

### Issue 3: Filters Not Working

```
Solution:
- Verify Alpine.js loaded
- Check event listeners
- Inspect filter values
- Review loadData() function
```

### Issue 4: Incorrect Calculations

```
Solution:
- Review segmentation logic
- Check date range filters
- Verify SQL queries
- Test with sample data
```

## 📊 Sample Test Data

### Minimum Data Required

```
- At least 10 customers
- At least 50 transactions
- Date range: 6+ months
- Multiple outlets
- Some piutang records
```

### Ideal Test Scenario

```
- 100+ customers
- 500+ transactions
- 1 year+ history
- 3+ outlets
- Mix of customer types
- Various piutang statuses
```

## ✅ Success Criteria

Dashboard is working correctly if:

1. ✅ All cards display numbers
2. ✅ All 3 charts render properly
3. ✅ Filters change data dynamically
4. ✅ Tables populate with data
5. ✅ Predictions show recommendations
6. ✅ No console errors
7. ✅ Responsive on all devices
8. ✅ Currency formatting correct
9. ✅ Colors and badges display
10. ✅ Performance acceptable

## 🎯 Quick Smoke Test (2 minutes)

```bash
1. Open /admin/crm
2. Check all cards have numbers ✓
3. Change outlet filter ✓
4. Change period filter ✓
5. Scroll to see all charts ✓
6. Check top customers table ✓
7. Review predictions section ✓
8. Open browser console - no errors ✓
```

If all ✓ passed → **Dashboard Working!** 🎉

## 📞 Support

If issues persist:

1. Check `storage/logs/laravel.log`
2. Review browser console
3. Verify database queries
4. Check model relationships
5. Test API endpoints directly

## 🚀 Ready to Use!

Dashboard CRM siap digunakan untuk analisis customer dan strategi bisnis!
