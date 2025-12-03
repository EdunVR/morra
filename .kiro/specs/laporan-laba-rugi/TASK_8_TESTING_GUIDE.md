# Task 8 Testing Guide: Detail Transaksi Per Akun

## Quick Test Checklist

### Prerequisites

-   [ ] Ensure you have test data with posted journal entries
-   [ ] Ensure you have accounts with transactions in the test period
-   [ ] Access the Profit & Loss report page: `/finance/profit-loss`

### Basic Functionality Tests

#### Test 1: Open Account Transaction Modal

1. Navigate to Profit & Loss report
2. Select an outlet and date range with data
3. Wait for the report to load
4. Click on any account name in the PENDAPATAN section
5. **Expected**: Modal opens showing transaction details
6. **Verify**:
    - Modal has blue gradient header
    - Account code and name displayed in header
    - Summary cards show transaction count, debit, credit, and balance
    - Transactions table displays with proper columns
    - Close button (X) is visible

#### Test 2: View Transaction Details

1. Open account transaction modal (from Test 1)
2. **Verify** the transactions table shows:
    - Transaction date (formatted as DD Month YYYY)
    - Transaction number
    - Description
    - Book name (in gray badge)
    - Debit amount (green, right-aligned)
    - Credit amount (red, right-aligned)
    - "Lihat Jurnal" link
3. **Verify** the summary cards show:
    - Total transaction count matches number of rows
    - Total debit matches sum of debit column
    - Total credit matches sum of credit column
    - Balance = Total Debit - Total Credit

#### Test 3: Close Modal

1. Open account transaction modal
2. Click the X button in header
3. **Expected**: Modal closes smoothly
4. Open modal again
5. Click outside the modal (on the gray overlay)
6. **Expected**: Modal closes smoothly

#### Test 4: Click on Different Account Types

Test clicking on accounts in each section:

1. **PENDAPATAN** (Revenue) - green amounts
2. **PENDAPATAN LAIN-LAIN** (Other Revenue) - green amounts
3. **BEBAN OPERASIONAL** (Operating Expense) - red amounts
4. **BEBAN LAIN-LAIN** (Other Expense) - red amounts

**Expected**: Each opens modal with correct account information

#### Test 5: Click on Child Accounts

1. Find a parent account with children (has chevron icon)
2. Click the chevron to expand
3. Click on a child account name
4. **Expected**: Modal opens showing only transactions for that child account

#### Test 6: Link to Journal Entry

1. Open account transaction modal
2. Click "Lihat Jurnal" link on any transaction
3. **Expected**:
    - Opens journal page in new tab
    - Journal page shows search results for that transaction number

### Edge Cases

#### Test 7: Account with No Transactions

1. Create or find an account with no transactions in the period
2. Click on that account
3. **Expected**:
    - Modal opens
    - Shows "Tidak ada transaksi untuk akun ini dalam periode yang dipilih"
    - Summary shows all zeros

#### Test 8: Account with Many Transactions

1. Find an account with 20+ transactions
2. Click on that account
3. **Expected**:
    - Modal opens and displays all transactions
    - Scrollbar appears in modal body
    - Performance is acceptable (loads within 2 seconds)

#### Test 9: Loading State

1. Use browser dev tools to throttle network to "Slow 3G"
2. Click on an account
3. **Expected**:
    - Modal opens immediately
    - Loading spinner displays
    - "Memuat detail transaksi..." message shows
    - After data loads, content replaces loading state

#### Test 10: Error Handling

1. **Test invalid account**: Manually modify URL to use invalid account_id
2. **Expected**: Error message displays in modal
3. **Test network error**: Disconnect internet, click account
4. **Expected**: Error message displays

### Data Accuracy Tests

#### Test 11: Verify Transaction Amounts

1. Open account transaction modal
2. Note the total debit and credit amounts
3. Manually verify against journal entries in database
4. **Expected**: Amounts match exactly

#### Test 12: Verify Only Posted Entries

1. Create a draft journal entry for an account
2. View that account's transactions
3. **Expected**: Draft entry does NOT appear in the list

#### Test 13: Verify Date Range Filtering

1. Set date range to January 2024
2. Click on an account
3. **Expected**: Only transactions from January 2024 appear
4. Change date range to February 2024
5. Click same account
6. **Expected**: Only transactions from February 2024 appear

### UI/UX Tests

#### Test 14: Hover Effects

1. Hover over account names
2. **Expected**:
    - Text turns blue
    - Underline appears
    - Info icon becomes visible
    - Cursor changes to pointer

#### Test 15: Responsive Design

Test on different screen sizes:

1. **Desktop** (1920x1080): Modal should be centered, max-width 4xl
2. **Tablet** (768x1024): Modal should be responsive, readable
3. **Mobile** (375x667): Modal should be full-width, scrollable

#### Test 16: Modal Animations

1. Click account to open modal
2. **Expected**: Smooth fade-in animation (300ms)
3. Close modal
4. **Expected**: Smooth fade-out animation (200ms)

### Integration Tests

#### Test 17: With Comparison Mode

1. Enable comparison mode
2. Set comparison dates
3. Click on an account
4. **Expected**: Modal shows transactions for current period only (not comparison period)

#### Test 18: With Different Outlets

1. Select Outlet A
2. Click on an account, note transactions
3. Close modal
4. Select Outlet B
5. Click on same account
6. **Expected**: Shows different transactions (filtered by outlet)

#### Test 19: Multiple Modal Opens

1. Click account A → view transactions → close
2. Click account B → view transactions → close
3. Click account A again
4. **Expected**: Each time shows correct data, no stale data

### Performance Tests

#### Test 20: Load Time

1. Click on account with moderate data (10-50 transactions)
2. Measure time from click to data display
3. **Expected**: < 1 second on normal connection

#### Test 21: Multiple Rapid Clicks

1. Rapidly click on different accounts
2. **Expected**:
    - No errors in console
    - Each modal shows correct data
    - No race conditions

## Common Issues and Solutions

### Issue 1: Modal doesn't open

**Solution**: Check browser console for errors. Verify Alpine.js is loaded.

### Issue 2: No transactions showing

**Solution**: Verify there are posted journal entries for that account in the selected period.

### Issue 3: Amounts don't match

**Solution**: Check if journal entries are posted (not draft). Verify outlet filter.

### Issue 4: Link to journal doesn't work

**Solution**: Verify the journal route exists and is accessible.

### Issue 5: Modal styling broken

**Solution**: Verify Tailwind CSS is loaded. Check for CSS conflicts.

## Browser Compatibility

Test on:

-   [ ] Chrome (latest)
-   [ ] Firefox (latest)
-   [ ] Safari (latest)
-   [ ] Edge (latest)

## Accessibility Tests

-   [ ] Keyboard navigation: Can close modal with Escape key?
-   [ ] Screen reader: Are labels properly announced?
-   [ ] Focus management: Does focus return to trigger element after close?

## Security Tests

-   [ ] SQL Injection: Try injecting SQL in account_id parameter
-   [ ] XSS: Try injecting scripts in transaction descriptions
-   [ ] Authorization: Can user access accounts from other outlets they don't have access to?

## Test Data Setup

### Minimal Test Data Needed:

1. At least 2 outlets
2. At least 5 accounts (2 revenue, 2 expense, 1 with children)
3. At least 10 posted journal entries
4. At least 1 draft journal entry (to test filtering)
5. Date range covering at least 2 months

### SQL to Create Test Data (Example):

```sql
-- This is just an example structure
-- Adjust based on your actual database schema

-- Create test journal entry
INSERT INTO journal_entries (outlet_id, transaction_date, transaction_number, description, status, book_id)
VALUES (1, '2024-01-15', 'TEST-001', 'Test Transaction', 'posted', 1);

-- Create journal entry details
INSERT INTO journal_entry_details (journal_entry_id, account_id, debit, credit)
VALUES
  (LAST_INSERT_ID(), 1, 5000000, 0),
  (LAST_INSERT_ID(), 2, 0, 5000000);
```

## Automated Testing (Future)

Consider adding:

1. **Unit Tests**: Test the `profitLossAccountDetails()` controller method
2. **Feature Tests**: Test the API endpoint with various scenarios
3. **Browser Tests**: Use Laravel Dusk to test the modal interaction

## Sign-off Checklist

Before marking as complete:

-   [ ] All basic functionality tests pass
-   [ ] All edge cases handled properly
-   [ ] Data accuracy verified
-   [ ] UI/UX is polished
-   [ ] No console errors
-   [ ] Performance is acceptable
-   [ ] Works on all major browsers
-   [ ] Mobile responsive
-   [ ] Documentation is complete

## Notes

-   Test with realistic data volumes (100+ transactions per account)
-   Test during peak hours to verify performance
-   Get feedback from actual users
-   Monitor error logs after deployment

## Conclusion

This testing guide ensures the account transaction details feature works correctly in all scenarios. Follow each test systematically and document any issues found.
