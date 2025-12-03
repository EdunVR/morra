# Task 8 Feature Overview: Account Transaction Details

## Feature Description

The Account Transaction Details feature allows users to click on any account name in the Profit & Loss report to view a detailed breakdown of all transactions that affected that account during the selected period.

## User Flow

```
1. User views Profit & Loss Report
   ↓
2. User clicks on an account name (e.g., "Pendapatan Penjualan")
   ↓
3. Modal opens showing transaction details
   ↓
4. User reviews transactions, clicks "Lihat Jurnal" to see full entry
   ↓
5. User closes modal to return to report
```

## Visual Components

### 1. Clickable Account Names

**Before (Old)**:

```
4000 - Pendapatan Penjualan    Rp 50,000,000
```

**After (New)**:

```
4000 - [Pendapatan Penjualan ℹ️]    Rp 50,000,000
      ↑ Clickable, shows blue underline on hover
```

### 2. Modal Structure

```
┌─────────────────────────────────────────────────────────────┐
│ [🔍] Detail Transaksi Akun                            [✕]   │ ← Blue gradient header
│      4000 - Pendapatan Penjualan                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│ │ Total    │ │ Total    │ │ Total    │ │ Saldo    │       │ ← Summary cards
│ │ Transaksi│ │ Debit    │ │ Kredit   │ │          │       │
│ │    10    │ │ Rp 0     │ │ Rp 50jt  │ │ -Rp 50jt │       │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                              │
│ ┌────────────────────────────────────────────────────────┐ │
│ │ Tanggal │ No. Trans │ Deskripsi │ Buku │ Debit │ Kredit│ │ ← Transactions table
│ ├────────────────────────────────────────────────────────┤ │
│ │ 15 Jan  │ JRN-001   │ Penjualan │ Buku │   -   │ 5jt   │ │
│ │ 2024    │           │ Produk A  │ Penj │       │       │ │
│ │         │           │           │      │       │ [Link]│ │
│ ├────────────────────────────────────────────────────────┤ │
│ │ 20 Jan  │ JRN-002   │ Penjualan │ Buku │   -   │ 10jt  │ │
│ │ 2024    │           │ Produk B  │ Penj │       │       │ │
│ │         │           │           │      │       │ [Link]│ │
│ └────────────────────────────────────────────────────────┘ │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                          [Tutup]             │ ← Footer
└─────────────────────────────────────────────────────────────┘
```

### 3. Loading State

```
┌─────────────────────────────────────────────────────────────┐
│ [🔍] Detail Transaksi Akun                            [✕]   │
│      4000 - Pendapatan Penjualan                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│                        ⟳                                     │ ← Spinning loader
│                                                              │
│              Memuat detail transaksi...                      │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                          [Tutup]             │
└─────────────────────────────────────────────────────────────┘
```

### 4. Empty State

```
┌─────────────────────────────────────────────────────────────┐
│ [🔍] Detail Transaksi Akun                            [✕]   │
│      4000 - Pendapatan Penjualan                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│ │ Total    │ │ Total    │ │ Total    │ │ Saldo    │       │
│ │ Transaksi│ │ Debit    │ │ Kredit   │ │          │       │
│ │     0    │ │ Rp 0     │ │ Rp 0     │ │ Rp 0     │       │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                              │
│                        ℹ️                                     │
│                                                              │
│     Tidak ada transaksi untuk akun ini                      │
│          dalam periode yang dipilih                          │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                          [Tutup]             │
└─────────────────────────────────────────────────────────────┘
```

### 5. Error State

```
┌─────────────────────────────────────────────────────────────┐
│ [🔍] Detail Transaksi Akun                            [✕]   │
│      4000 - Pendapatan Penjualan                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│                        ⚠️                                     │
│                                                              │
│                 Gagal Memuat Data                            │
│                                                              │
│         Terjadi kesalahan saat memuat detail transaksi      │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                          [Tutup]             │
└─────────────────────────────────────────────────────────────┘
```

## Color Coding

-   **Blue**: Account names on hover, modal header, links
-   **Green**: Debit amounts (for revenue accounts)
-   **Red**: Credit amounts (for revenue accounts), Debit amounts (for expense accounts)
-   **Gray**: Secondary information (book names, codes)
-   **Purple**: Balance/summary information

## Interactive Elements

### Hover States

1. **Account Name**:

    - Default: Black text
    - Hover: Blue text with underline, info icon appears
    - Cursor: Pointer

2. **"Lihat Jurnal" Link**:

    - Default: Blue text
    - Hover: Darker blue
    - Cursor: Pointer

3. **Close Button (X)**:
    - Default: White
    - Hover: Light blue
    - Cursor: Pointer

### Click Actions

1. **Account Name** → Opens modal with transaction details
2. **"Lihat Jurnal"** → Opens journal page in new tab with search filter
3. **Close Button (X)** → Closes modal
4. **Click Outside Modal** → Closes modal

## Responsive Behavior

### Desktop (> 1024px)

-   Modal: Max-width 4xl (896px), centered
-   Table: Full width, all columns visible
-   Summary cards: 4 columns

### Tablet (768px - 1024px)

-   Modal: Max-width 90%, centered
-   Table: Scrollable horizontally if needed
-   Summary cards: 2x2 grid

### Mobile (< 768px)

-   Modal: Full width with padding
-   Table: Scrollable horizontally
-   Summary cards: 2x2 grid or stacked

## Accessibility Features

-   **Keyboard Navigation**:

    -   Tab to navigate between clickable elements
    -   Escape key to close modal
    -   Enter/Space to activate buttons

-   **Screen Readers**:

    -   Proper ARIA labels on buttons
    -   Table headers properly marked
    -   Modal announced when opened

-   **Focus Management**:
    -   Focus trapped within modal when open
    -   Focus returns to trigger element when closed

## Performance Considerations

-   **Lazy Loading**: Modal content only loads when opened
-   **Efficient Queries**: Backend filters data at database level
-   **Caching**: Consider caching frequently accessed accounts
-   **Pagination**: For accounts with 100+ transactions (future enhancement)

## Integration Points

### With Existing Features

1. **Profit & Loss Report**: Seamlessly integrated into existing report
2. **Journal Entries**: Links directly to journal page
3. **Outlet Filter**: Respects current outlet selection
4. **Date Range**: Respects current date range selection

### API Endpoint

```
GET /finance/profit-loss/account-details
```

**Parameters**:

-   `outlet_id`: Current outlet
-   `account_id`: Clicked account
-   `start_date`: Report start date
-   `end_date`: Report end date

**Response**: JSON with account info, transactions, and summary

## User Benefits

1. **Transparency**: See exactly what transactions make up each account balance
2. **Verification**: Quickly verify data accuracy
3. **Analysis**: Understand transaction patterns
4. **Navigation**: Easy access to full journal entries
5. **Efficiency**: No need to manually search for transactions

## Business Value

1. **Audit Trail**: Clear audit trail for financial data
2. **Error Detection**: Easier to spot data entry errors
3. **Decision Making**: Better informed financial decisions
4. **Compliance**: Supports financial compliance requirements
5. **User Satisfaction**: Improves user experience and trust

## Future Enhancements

1. **Export**: Export account transactions to Excel/PDF
2. **Filters**: Filter transactions by book, amount, date
3. **Sorting**: Sort transactions by any column
4. **Search**: Search within transactions
5. **Pagination**: Handle large transaction volumes
6. **Drill-down**: View full journal entry in modal
7. **Comparison**: Compare transactions between periods
8. **Charts**: Visualize transaction patterns

## Technical Notes

-   Built with Alpine.js for reactivity
-   Uses Tailwind CSS for styling
-   Follows Laravel best practices
-   RESTful API design
-   Responsive and mobile-friendly
-   Accessible (WCAG 2.1 AA compliant)

## Conclusion

The Account Transaction Details feature significantly enhances the Profit & Loss report by providing transparency and easy access to underlying transaction data. It follows modern UI/UX patterns and integrates seamlessly with the existing system.
