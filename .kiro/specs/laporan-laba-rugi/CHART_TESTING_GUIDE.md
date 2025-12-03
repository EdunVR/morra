# Chart Visualization Testing Guide - Laporan Laba Rugi

## Overview

This guide provides step-by-step instructions for testing the chart visualization features in the Laporan Laba Rugi (Profit & Loss Statement) module.

## Prerequisites

-   Access to the application with Finance Manager role
-   At least one outlet with posted journal entries
-   Test data with various revenue and expense accounts
-   Modern browser (Chrome, Firefox, Safari, or Edge)

## Test Scenarios

### 1. Basic Chart Rendering

#### Test 1.1: Initial Page Load

**Steps:**

1. Navigate to Finance > Laporan Laba Rugi
2. Select an outlet from dropdown
3. Wait for data to load

**Expected Results:**

-   ✅ Loading spinner appears while data is being fetched
-   ✅ Three charts render after data loads:
    -   Revenue Pie Chart (top left)
    -   Expense Pie Chart (top right)
    -   Revenue vs Expense Bar Chart (bottom)
-   ✅ Charts display with proper colors and labels
-   ✅ No console errors

#### Test 1.2: Chart Loading State

**Steps:**

1. Open browser developer tools (F12)
2. Go to Network tab and throttle to "Slow 3G"
3. Refresh the page
4. Observe the loading state

**Expected Results:**

-   ✅ Loading spinner shows in each chart container
-   ✅ Text "Memuat grafik..." displays
-   ✅ Charts appear after data loads
-   ✅ Loading state disappears smoothly

### 2. Revenue Pie Chart

#### Test 2.1: Data Display

**Steps:**

1. Load page with outlet that has revenue data
2. Examine the Revenue Pie Chart

**Expected Results:**

-   ✅ Chart shows all revenue accounts with amount > 0
-   ✅ Each segment has distinct color
-   ✅ Legend displays at bottom with account names
-   ✅ Chart is responsive to container size

#### Test 2.2: Tooltip Interaction

**Steps:**

1. Hover mouse over each pie segment
2. Observe tooltip content

**Expected Results:**

-   ✅ Tooltip appears on hover
-   ✅ Shows account name
-   ✅ Shows formatted currency amount (Rp X,XXX,XXX)
-   ✅ Shows percentage of total (XX.X%)
-   ✅ Tooltip follows mouse cursor

#### Test 2.3: Click Interaction

**Steps:**

1. Click on a pie segment
2. Observe the response

**Expected Results:**

-   ✅ Notification appears with account details
-   ✅ Account expands in the table below (if has children)
-   ✅ Page scrolls to show the account in table
-   ✅ No console errors

#### Test 2.4: Empty Data State

**Steps:**

1. Select outlet with no revenue data
2. Observe the Revenue Pie Chart

**Expected Results:**

-   ✅ Chart displays "Tidak ada data" segment
-   ✅ No errors or blank chart
-   ✅ Chart maintains proper layout

### 3. Expense Pie Chart

#### Test 3.1: Data Display

**Steps:**

1. Load page with outlet that has expense data
2. Examine the Expense Pie Chart

**Expected Results:**

-   ✅ Chart shows all expense accounts with amount > 0
-   ✅ Each segment has red/orange color scheme
-   ✅ Legend displays at bottom with account names
-   ✅ Chart is responsive to container size

#### Test 3.2: Tooltip Interaction

**Steps:**

1. Hover mouse over each pie segment
2. Observe tooltip content

**Expected Results:**

-   ✅ Tooltip appears on hover
-   ✅ Shows account name
-   ✅ Shows formatted currency amount
-   ✅ Shows percentage of total
-   ✅ Tooltip follows mouse cursor

#### Test 3.3: Click Interaction

**Steps:**

1. Click on a pie segment
2. Observe the response

**Expected Results:**

-   ✅ Notification appears with account details
-   ✅ Account expands in the table below (if has children)
-   ✅ Page scrolls to show the account in table
-   ✅ No console errors

### 4. Revenue vs Expense Bar Chart

#### Test 4.1: Data Display

**Steps:**

1. Load page with outlet that has both revenue and expense data
2. Examine the Bar Chart

**Expected Results:**

-   ✅ Three bars display: Pendapatan, Beban, Laba/Rugi Bersih
-   ✅ Pendapatan bar is green
-   ✅ Beban bar is red
-   ✅ Laba/Rugi Bersih bar is blue
-   ✅ Bars have rounded corners
-   ✅ Y-axis shows formatted values (Rp Xjt or Rp Xrb)

#### Test 4.2: Tooltip Interaction

**Steps:**

1. Hover mouse over each bar
2. Observe tooltip content

**Expected Results:**

-   ✅ Tooltip appears on hover
-   ✅ Shows "Jumlah: Rp X,XXX,XXX"
-   ✅ Currency is properly formatted
-   ✅ Tooltip follows mouse cursor

#### Test 4.3: Scale and Grid

**Steps:**

1. Observe the chart axes and grid lines

**Expected Results:**

-   ✅ Y-axis starts at 0
-   ✅ Y-axis labels show millions (jt) or thousands (rb)
-   ✅ Horizontal grid lines visible
-   ✅ No vertical grid lines
-   ✅ X-axis labels are clear and readable

### 5. Trend Line Chart (Comparison Mode)

#### Test 5.1: Chart Visibility

**Steps:**

1. Load page without comparison mode
2. Observe charts section
3. Enable comparison mode checkbox
4. Set comparison dates
5. Wait for data to reload

**Expected Results:**

-   ✅ Trend chart is NOT visible initially
-   ✅ Trend chart appears after enabling comparison
-   ✅ Chart shows smooth transition
-   ✅ Chart displays below the bar chart

#### Test 5.2: Data Display

**Steps:**

1. Enable comparison mode
2. Examine the Trend Line Chart

**Expected Results:**

-   ✅ Chart shows line connecting two points
-   ✅ First point: "Periode Pembanding"
-   ✅ Second point: "Periode Saat Ini"
-   ✅ Line is blue with filled area underneath
-   ✅ Points are visible with white border
-   ✅ Legend shows "Laba/Rugi Bersih"

#### Test 5.3: Tooltip Interaction

**Steps:**

1. Hover mouse over each data point
2. Observe tooltip content

**Expected Results:**

-   ✅ Tooltip appears on hover
-   ✅ Shows "Laba/Rugi Bersih: Rp X,XXX,XXX"
-   ✅ Currency is properly formatted
-   ✅ Tooltip follows mouse cursor

#### Test 5.4: Disable Comparison

**Steps:**

1. With comparison mode enabled
2. Uncheck comparison mode checkbox
3. Wait for data to reload

**Expected Results:**

-   ✅ Trend chart disappears smoothly
-   ✅ Other charts remain visible
-   ✅ No console errors

### 6. Data Updates

#### Test 6.1: Outlet Change

**Steps:**

1. Load page with outlet A
2. Change to outlet B
3. Observe charts

**Expected Results:**

-   ✅ All charts update with new data
-   ✅ Loading state appears briefly
-   ✅ Charts reflect outlet B's data
-   ✅ No visual glitches

#### Test 6.2: Period Change

**Steps:**

1. Load page with current month
2. Change period to "Bulan Lalu"
3. Observe charts

**Expected Results:**

-   ✅ All charts update with new period data
-   ✅ Date range updates automatically
-   ✅ Charts reflect previous month's data
-   ✅ No console errors

#### Test 6.3: Custom Date Range

**Steps:**

1. Select "Custom" period
2. Set custom start and end dates
3. Observe charts

**Expected Results:**

-   ✅ All charts update with custom range data
-   ✅ Charts reflect selected date range
-   ✅ No validation errors

#### Test 6.4: Refresh Button

**Steps:**

1. Load page with data
2. Click Refresh button
3. Observe charts

**Expected Results:**

-   ✅ Loading state appears
-   ✅ Charts reload with fresh data
-   ✅ All charts update properly
-   ✅ No console errors

### 7. Responsive Design

#### Test 7.1: Desktop View (1920x1080)

**Steps:**

1. Open page in full screen desktop
2. Observe chart layout

**Expected Results:**

-   ✅ Pie charts side by side (2 columns)
-   ✅ Bar chart full width below
-   ✅ Trend chart full width below (if comparison)
-   ✅ Charts maintain aspect ratio
-   ✅ Legends are readable

#### Test 7.2: Tablet View (768x1024)

**Steps:**

1. Resize browser to tablet size
2. Observe chart layout

**Expected Results:**

-   ✅ Pie charts may stack vertically
-   ✅ Charts remain readable
-   ✅ Legends adjust properly
-   ✅ No horizontal scrolling

#### Test 7.3: Mobile View (375x667)

**Steps:**

1. Resize browser to mobile size
2. Observe chart layout

**Expected Results:**

-   ✅ All charts stack vertically
-   ✅ Charts remain interactive
-   ✅ Legends are readable
-   ✅ Touch interactions work

### 8. Performance

#### Test 8.1: Large Dataset

**Steps:**

1. Select outlet with many accounts (20+)
2. Load page and observe

**Expected Results:**

-   ✅ Charts render within 2 seconds
-   ✅ No lag or freezing
-   ✅ Smooth interactions
-   ✅ No memory leaks

#### Test 8.2: Multiple Updates

**Steps:**

1. Rapidly change outlets 5 times
2. Observe chart behavior

**Expected Results:**

-   ✅ Charts update each time
-   ✅ No visual artifacts
-   ✅ No console errors
-   ✅ Memory usage stable

### 9. Browser Compatibility

#### Test 9.1: Chrome/Edge

**Steps:**

1. Open page in Chrome or Edge
2. Test all chart features

**Expected Results:**

-   ✅ All charts render correctly
-   ✅ All interactions work
-   ✅ No console errors

#### Test 9.2: Firefox

**Steps:**

1. Open page in Firefox
2. Test all chart features

**Expected Results:**

-   ✅ All charts render correctly
-   ✅ All interactions work
-   ✅ No console errors

#### Test 9.3: Safari

**Steps:**

1. Open page in Safari
2. Test all chart features

**Expected Results:**

-   ✅ All charts render correctly
-   ✅ All interactions work
-   ✅ No console errors

### 10. Error Handling

#### Test 10.1: Network Error

**Steps:**

1. Open developer tools
2. Set network to offline
3. Try to load data

**Expected Results:**

-   ✅ Error message displays
-   ✅ Charts show previous data or empty state
-   ✅ No console crashes

#### Test 10.2: Invalid Data

**Steps:**

1. Manually modify API response (if possible)
2. Return invalid data structure

**Expected Results:**

-   ✅ Charts handle gracefully
-   ✅ Error message displays
-   ✅ No console crashes

## Common Issues and Solutions

### Issue 1: Charts Not Rendering

**Symptoms:** Blank space where charts should be
**Solutions:**

-   Check browser console for errors
-   Verify Chart.js CDN is loading
-   Check if canvas elements exist in DOM
-   Verify data is being fetched successfully

### Issue 2: Charts Not Updating

**Symptoms:** Charts show old data after filter change
**Solutions:**

-   Check if `initCharts()` is called after data load
-   Verify `destroyCharts()` is cleaning up properly
-   Check network tab for API calls

### Issue 3: Tooltips Not Showing

**Symptoms:** No tooltip on hover
**Solutions:**

-   Verify Chart.js version is 4.4.0
-   Check if tooltip callbacks are defined
-   Test in different browser

### Issue 4: Click Handler Not Working

**Symptoms:** Clicking pie segments does nothing
**Solutions:**

-   Check if `onClick` handler is defined
-   Verify `showAccountDetail()` method exists
-   Check console for JavaScript errors

## Test Data Requirements

### Minimum Test Data

-   At least 1 outlet
-   At least 3 revenue accounts with transactions
-   At least 3 expense accounts with transactions
-   At least 5 posted journal entries

### Ideal Test Data

-   Multiple outlets (3+)
-   10+ revenue accounts with varying amounts
-   10+ expense accounts with varying amounts
-   Data spanning multiple months
-   Mix of parent and child accounts

## Reporting Issues

When reporting chart-related issues, include:

1. Browser name and version
2. Screen size / device type
3. Steps to reproduce
4. Expected vs actual behavior
5. Console errors (if any)
6. Screenshots or screen recording
7. Network tab showing API responses

## Sign-off Checklist

Before marking charts as complete, verify:

-   [ ] All 4 chart types render correctly
-   [ ] Tooltips work on all charts
-   [ ] Click handlers work on pie charts
-   [ ] Comparison mode shows/hides trend chart
-   [ ] Charts update on filter changes
-   [ ] Responsive design works on all screen sizes
-   [ ] No console errors in any browser
-   [ ] Performance is acceptable with large datasets
-   [ ] Empty states handled gracefully
-   [ ] Currency formatting is correct
-   [ ] Colors are consistent with design
-   [ ] Legends are readable and positioned correctly

## Conclusion

This testing guide covers all aspects of the chart visualization feature. Follow each test scenario systematically to ensure complete functionality and quality assurance.
