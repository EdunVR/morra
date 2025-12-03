# Invoice Penjualan - UX Improvements

## Summary

Tiga improvement untuk meningkatkan user experience di invoice penjualan:

1. ✅ Auto-close modal setelah pembayaran berhasil
2. ✅ Tombol quick action 25%, 50%, dan Lunas
3. ✅ Print invoice menampilkan info bank dari database

---

## 1. ✅ Auto-Close Modal After Payment Success

### Problem:

-   Modal pembayaran tetap terbuka setelah pembayaran berhasil
-   User harus manual close modal
-   Tidak user-friendly

### Solution:

-   Modal langsung close setelah payment success (baik cicilan maupun lunas)
-   Tampilkan toast message yang sesuai
-   Reload data invoice dan stats

### Implementation:

**Before:**

```javascript
if (result.data.is_fully_paid) {
    this.showPaymentModal = false;
    this.showToastMessage("Invoice telah lunas!", "success");
} else {
    // Keep modal open, reset form for next installment
    this.paymentForm.jumlah_transfer = result.data.sisa_tagihan;
    // ... reset other fields
}
```

**After:**

```javascript
// Always close modal after successful payment
this.showPaymentModal = false;

// Show appropriate message
if (result.data.is_fully_paid) {
    this.showToastMessage(
        "Pembayaran berhasil! Invoice telah lunas.",
        "success"
    );
} else {
    this.showToastMessage(
        "Pembayaran cicilan berhasil dicatat. Sisa tagihan: " +
            this.formatCurrency(result.data.sisa_tagihan),
        "success"
    );
}
```

**Behavior:**

-   Payment success → modal close immediately
-   Toast message shows payment status
-   Data reloads automatically
-   User can reopen modal for next installment if needed

---

## 2. ✅ Quick Action Buttons (25%, 50%, Lunas)

### Problem:

-   User harus manual input jumlah untuk pembayaran partial
-   Tidak ada shortcut untuk pembayaran 25% atau 50%
-   Hanya ada tombol "Lunas"

### Solution:

-   Tambah 3 tombol quick action: 25%, 50%, Lunas
-   Tombol otomatis calculate berdasarkan sisa tagihan
-   Layout horizontal dengan warna berbeda

### Implementation:

```html
{{-- Quick Action Buttons --}}
<div class="flex gap-2 mt-2">
    <button
        type="button"
        @click="paymentForm.jumlah_transfer = Math.round((paymentForm.sisa_tagihan || paymentForm.total) * 0.25)"
        :disabled="processingPayment"
        class="flex-1 text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
    >
        25%
    </button>
    <button
        type="button"
        @click="paymentForm.jumlah_transfer = Math.round((paymentForm.sisa_tagihan || paymentForm.total) * 0.5)"
        :disabled="processingPayment"
        class="flex-1 text-xs bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg hover:bg-amber-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
    >
        50%
    </button>
    <button
        type="button"
        @click="paymentForm.jumlah_transfer = paymentForm.sisa_tagihan || paymentForm.total"
        :disabled="processingPayment"
        class="flex-1 text-xs bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
    >
        Lunas
    </button>
</div>
```

**Features:**

-   **25% Button** (Blue): Set jumlah bayar = 25% dari sisa tagihan
-   **50% Button** (Amber): Set jumlah bayar = 50% dari sisa tagihan
-   **Lunas Button** (Green): Set jumlah bayar = 100% (sisa tagihan penuh)
-   Auto-calculate dengan `Math.round()` untuk hasil bulat
-   Disabled saat processing payment

**Example:**

-   Sisa tagihan: Rp 1,000,000
-   Click 25% → Jumlah bayar = Rp 250,000
-   Click 50% → Jumlah bayar = Rp 500,000
-   Click Lunas → Jumlah bayar = Rp 1,000,000

---

## 3. ✅ Print Invoice - Display Bank Account Info

### Problem:

-   Print invoice tidak menampilkan informasi rekening pembayaran
-   Customer tidak tahu harus transfer ke rekening mana

### Solution:

-   Load bank accounts dari `company_bank_accounts` table
-   Filter berdasarkan `id_outlet` invoice
-   Tampilkan di print invoice dengan format yang jelas

### Implementation:

**Controller (Already Implemented):**

```php
public function invoicePrint($id)
{
    $invoice = SalesInvoice::with(['member', 'items'])->findOrFail($id);
    $setting = DB::table('setting')->first();

    // Get company bank accounts berdasarkan outlet invoice
    $bankAccounts = \App\Models\CompanyBankAccount::byOutlet($invoice->id_outlet)
        ->active()
        ->orderBy('sort_order')
        ->orderBy('bank_name')
        ->get();

    // ... generate PDF with bankAccounts
}
```

**View (Already Implemented):**

```html
@if(isset($bankAccounts) && $bankAccounts->count() > 0)
<div class="bank-accounts">
    <div class="bank-section-title">Rekening Pembayaran</div>
    @foreach($bankAccounts as $bank)
    <div class="bank-account-item">
        <span class="bank-name">{{ $bank->bank_name }}</span>
        @if($bank->branch_name)
        <span>({{ $bank->branch_name }})</span>
        @endif
        <br />
        <span class="account-number"
            >No. Rek: {{ $bank->getFormattedAccountNumber() }}</span
        >
        <br />
        <span class="account-holder">a/n {{ $bank->account_holder_name }}</span>
    </div>
    @endforeach
</div>
@endif
```

**Features:**

-   Load bank accounts by outlet
-   Only show active accounts
-   Sort by `sort_order` then `bank_name`
-   Format account number with dashes (e.g., 1234-5678-9012)
-   Show bank name, branch (if any), account number, and holder name
-   Styled section with border and background

**Database Table:**

```
company_bank_accounts
- id_company_bank_account
- id_outlet (FK to outlets)
- bank_name (e.g., "BCA", "Mandiri")
- account_number
- account_holder_name
- branch_name (optional)
- currency (default: IDR)
- is_active (boolean)
- sort_order (for ordering)
- notes
```

**Example Output:**

```
┌─────────────────────────────────────┐
│ Rekening Pembayaran                 │
├─────────────────────────────────────┤
│ BCA (KCP Sudirman)                  │
│ No. Rek: 1234-5678-9012             │
│ a/n PT Contoh Perusahaan            │
├─────────────────────────────────────┤
│ Mandiri                             │
│ No. Rek: 9876-5432-1098             │
│ a/n PT Contoh Perusahaan            │
└─────────────────────────────────────┘
```

---

## Files Modified

1. **resources/views/admin/penjualan/invoice/index.blade.php**

    - Auto-close modal after payment success
    - Add quick action buttons (25%, 50%, Lunas)

2. **app/Http/Controllers/SalesManagementController.php**

    - Already loads bank accounts in `invoicePrint()` method

3. **resources/views/admin/penjualan/invoice/print.blade.php**

    - Already displays bank accounts section

4. **app/Models/CompanyBankAccount.php**
    - Already has all required methods and scopes

---

## Testing Checklist

### Task 1: Auto-Close Modal ✅

-   [ ] Make payment (partial) → modal closes
-   [ ] Make payment (full/lunas) → modal closes
-   [ ] Toast message shows correct info
-   [ ] Invoice list updates automatically
-   [ ] Stats update automatically

### Task 2: Quick Action Buttons ✅

-   [ ] Click 25% → jumlah bayar = 25% of sisa tagihan
-   [ ] Click 50% → jumlah bayar = 50% of sisa tagihan
-   [ ] Click Lunas → jumlah bayar = 100% of sisa tagihan
-   [ ] Buttons disabled during processing
-   [ ] Calculation rounds to nearest integer
-   [ ] Works with different sisa tagihan amounts

### Task 3: Print Invoice Bank Info ✅

-   [ ] Print invoice shows bank accounts section
-   [ ] Only shows accounts for invoice's outlet
-   [ ] Only shows active accounts
-   [ ] Account numbers formatted with dashes
-   [ ] Shows bank name, branch, account number, holder name
-   [ ] Multiple accounts displayed correctly
-   [ ] Section hidden if no bank accounts

---

## Status: ✅ ALL COMPLETE

Semua 3 improvement sudah diimplementasikan dan siap untuk testing!

**Note:** Task 3 (Print Invoice Bank Info) sudah ada implementasinya di codebase, jadi tidak perlu perubahan tambahan. Hanya perlu memastikan data bank accounts sudah ada di database untuk outlet yang bersangkutan.
