# Piutang POS Integration - Implementation Guide

## Overview

Integrasi piutang dari POS ke halaman Piutang, dengan fitur:

-   List piutang POS bersama invoice
-   Link ke preview nota POS
-   Modal pembayaran untuk piutang POS

## Backend Changes

### 1. Updated `getPiutangData()` Method

**File:** `app/Http/Controllers/FinanceAccountantController.php`

**Changes:**

-   Added query for POS sales with `is_bon = true`
-   Merged POS piutang with invoice piutang
-   Added `source` field ('invoice' or 'pos')
-   Sorted combined data by date

**Query Structure:**

```php
// Query 1: Sales Invoice
$invoices = DB::table('sales_invoice as si')
    ->leftJoin('member', ...)
    ->where('si.status', '!=', 'draft')
    ->get();

// Query 2: POS Sales (BON)
$posSales = DB::table('pos_sales as ps')
    ->leftJoin('member', ...)
    ->leftJoin('piutang', ...)
    ->where('ps.is_bon', true)
    ->where('ps.status', 'menunggu')
    ->get();

// Merge and sort
$formattedData = array_merge($invoiceData, $posData);
usort($formattedData, by date desc);
```

**Response Format:**

```json
{
    "success": true,
    "data": [
        {
            "id_piutang": 123,
            "id_penjualan": 456,
            "tanggal": "2025-12-01",
            "tanggal_jatuh_tempo": "2025-12-31",
            "nama_customer": "John Doe",
            "outlet": "Outlet 1",
            "jumlah_piutang": 100000,
            "jumlah_dibayar": 0,
            "sisa_piutang": 100000,
            "status": "belum_lunas",
            "is_overdue": false,
            "days_overdue": 0,
            "invoice_number": "POS-20251201-001",
            "source": "pos"
        }
    ],
    "summary": {
        "total_piutang": 500000,
        "total_dibayar": 100000,
        "total_sisa": 400000,
        "count_belum_lunas": 5,
        "count_lunas": 2,
        "count_overdue": 1
    }
}
```

## Frontend Implementation

### 1. Display Source Column

Add column to show source (POS/Invoice):

```javascript
{
    data: 'source',
    name: 'source',
    render: function(data) {
        if (data === 'pos') {
            return '<span class="badge bg-info">POS</span>';
        }
        return '<span class="badge bg-primary">Invoice</span>';
    }
}
```

### 2. Invoice Number with Link

Make invoice number clickable based on source:

```javascript
{
    data: 'invoice_number',
    name: 'invoice_number',
    render: function(data, type, row) {
        if (row.source === 'pos') {
            // Link to POS nota preview
            const url = `/penjualan/pos/${row.id_piutang}/print?type=besar`;
            return `<a href="${url}" target="_blank" class="text-primary hover:underline">
                        <i class="fa fa-receipt"></i> ${data}
                    </a>`;
        } else {
            // Link to invoice detail (existing)
            return `<a href="#" onclick="showInvoiceDetail(${row.id_piutang})" class="text-primary hover:underline">
                        <i class="fa fa-file-invoice"></i> ${data}
                    </a>`;
        }
    }
}
```

### 3. Payment Button

Different action based on source:

```javascript
{
    data: 'action',
    name: 'action',
    render: function(data, type, row) {
        if (row.status === 'lunas') {
            return '<span class="badge bg-success">Lunas</span>';
        }

        let button = '';
        if (row.source === 'pos') {
            // POS payment modal
            button = `<button onclick="showPosPaymentModal(${row.id_piutang}, ${row.sisa_piutang})"
                             class="btn btn-sm btn-success">
                        <i class="fa fa-money-bill"></i> Bayar
                      </button>`;
        } else {
            // Invoice payment modal (existing)
            button = `<button onclick="showPaymentModal(${row.id_piutang})"
                             class="btn btn-sm btn-success">
                        <i class="fa fa-money-bill"></i> Bayar
                      </button>`;
        }

        return button;
    }
}
```

### 4. POS Payment Modal

Add modal for POS piutang payment:

```html
<!-- Modal Bayar Piutang POS -->
<div class="modal fade" id="posPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bayar Piutang POS</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="posPaymentForm">
                <div class="modal-body">
                    <input type="hidden" id="pos_piutang_id" />

                    <div class="form-group">
                        <label>Sisa Piutang</label>
                        <input
                            type="text"
                            id="pos_sisa_piutang"
                            class="form-control"
                            readonly
                        />
                    </div>

                    <div class="form-group">
                        <label
                            >Jumlah Bayar
                            <span class="text-danger">*</span></label
                        >
                        <input
                            type="number"
                            id="pos_jumlah_bayar"
                            class="form-control"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label
                            >Metode Pembayaran
                            <span class="text-danger">*</span></label
                        >
                        <select
                            id="pos_metode_bayar"
                            class="form-control"
                            required
                        >
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Bayar</label>
                        <input
                            type="date"
                            id="pos_tanggal_bayar"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>"
                        />
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea
                            id="pos_keterangan"
                            class="form-control"
                            rows="3"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

### 5. JavaScript Functions

```javascript
// Show POS payment modal
function showPosPaymentModal(piutangId, sisaPiutang) {
    $("#pos_piutang_id").val(piutangId);
    $("#pos_sisa_piutang").val("Rp " + formatNumber(sisaPiutang));
    $("#pos_jumlah_bayar").val(sisaPiutang);
    $("#pos_jumlah_bayar").attr("max", sisaPiutang);
    $("#posPaymentModal").modal("show");
}

// Submit POS payment
$("#posPaymentForm").on("submit", function (e) {
    e.preventDefault();

    const piutangId = $("#pos_piutang_id").val();
    const jumlahBayar = $("#pos_jumlah_bayar").val();
    const metodeBayar = $("#pos_metode_bayar").val();
    const tanggalBayar = $("#pos_tanggal_bayar").val();
    const keterangan = $("#pos_keterangan").val();

    $.ajax({
        url: `/finance/piutang/pos/${piutangId}/bayar`,
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            jumlah_bayar: jumlahBayar,
            metode_bayar: metodeBayar,
            tanggal_bayar: tanggalBayar,
            keterangan: keterangan,
        },
        success: function (response) {
            if (response.success) {
                $("#posPaymentModal").modal("hide");
                Swal.fire("Berhasil!", response.message, "success");
                table.ajax.reload();
            } else {
                Swal.fire("Gagal!", response.message, "error");
            }
        },
        error: function (xhr) {
            Swal.fire(
                "Error!",
                "Terjadi kesalahan saat menyimpan pembayaran",
                "error"
            );
        },
    });
});
```

## Backend Payment Processing

### New Method: `payPosPiutang()`

**File:** `app/Http/Controllers/FinanceAccountantController.php`

```php
/**
 * Process POS piutang payment
 */
public function payPosPiutang(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_bayar' => 'required|in:cash,transfer,qris',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::transaction(function() use ($request, $id) {
            // Get POS sale
            $posSale = \App\Models\PosSale::findOrFail($id);

            // Get piutang record
            $piutang = \App\Models\Piutang::where('id_penjualan', $posSale->id_penjualan)->first();

            if (!$piutang) {
                throw new \Exception('Data piutang tidak ditemukan');
            }

            $jumlahBayar = $request->jumlah_bayar;

            // Validate payment amount
            if ($jumlahBayar > $piutang->sisa_piutang) {
                throw new \Exception('Jumlah bayar melebihi sisa piutang');
            }

            // Update piutang
            $piutang->jumlah_dibayar += $jumlahBayar;
            $piutang->sisa_piutang -= $jumlahBayar;

            if ($piutang->sisa_piutang <= 0) {
                $piutang->status = 'lunas';
                $posSale->status = 'lunas';
            }

            $piutang->save();
            $posSale->save();

            // Create payment history
            \App\Models\PiutangPaymentHistory::create([
                'id_piutang' => $piutang->id_piutang,
                'id_pos_sale' => $posSale->id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar' => $jumlahBayar,
                'metode_bayar' => $request->metode_bayar,
                'keterangan' => $request->keterangan,
                'created_by' => auth()->id()
            ]);

            // Create journal entry for payment
            $this->createPiutangPaymentJournal($posSale, $jumlahBayar, $request->metode_bayar);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran piutang berhasil disimpan'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error paying POS piutang: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Create journal entry for piutang payment
 */
private function createPiutangPaymentJournal($posSale, $jumlahBayar, $metodeBayar)
{
    $setting = \App\Models\SettingCOAPos::getByOutlet($posSale->id_outlet);

    if (!$setting || !$setting->accounting_book_id) {
        return; // Skip if COA not configured
    }

    $entries = [];

    // Debit: Kas/Bank
    $akunKasBank = $metodeBayar === 'cash'
        ? $setting->akun_kas
        : $setting->akun_bank;

    $entries[] = [
        'account_id' => $this->getAccountIdByCode($akunKasBank, $posSale->id_outlet),
        'debit' => $jumlahBayar,
        'credit' => 0,
        'memo' => 'Penerimaan pembayaran piutang POS'
    ];

    // Credit: Piutang Usaha
    $entries[] = [
        'account_id' => $this->getAccountIdByCode($setting->akun_piutang_usaha, $posSale->id_outlet),
        'debit' => 0,
        'credit' => $jumlahBayar,
        'memo' => 'Pengurangan piutang usaha'
    ];

    $this->journalService->createAutomaticJournal(
        'piutang_payment',
        $posSale->id,
        now(),
        "Pembayaran Piutang POS {$posSale->no_transaksi}",
        $entries,
        $setting->accounting_book_id,
        $posSale->id_outlet
    );
}
```

### New Route

**File:** `routes/web.php`

```php
// POS Piutang Payment
Route::post('piutang/pos/{id}/bayar', [FinanceAccountantController::class, 'payPosPiutang'])
    ->name('piutang.pos.bayar');
```

## Database Schema

### piutang_payment_history (New Table)

```sql
CREATE TABLE piutang_payment_history (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_piutang BIGINT,
    id_pos_sale BIGINT NULL,
    id_sales_invoice BIGINT NULL,
    tanggal_bayar DATE,
    jumlah_bayar DECIMAL(15,2),
    metode_bayar ENUM('cash','transfer','qris'),
    keterangan TEXT NULL,
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (id_piutang) REFERENCES piutang(id_piutang),
    FOREIGN KEY (id_pos_sale) REFERENCES pos_sales(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

## Testing Checklist

### Backend

-   [ ] POS piutang muncul di list
-   [ ] Filter by outlet works
-   [ ] Filter by status works
-   [ ] Search works for POS number
-   [ ] Summary calculation correct

### Frontend

-   [ ] Source column shows POS/Invoice
-   [ ] Invoice number clickable
-   [ ] POS link opens nota preview
-   [ ] Payment button shows for POS
-   [ ] Modal opens correctly

### Payment

-   [ ] Payment form validation
-   [ ] Payment amount validation
-   [ ] Payment saves correctly
-   [ ] Piutang status updates
-   [ ] POS status updates
-   [ ] Journal entry created
-   [ ] Payment history recorded

## Status

🚧 **IN PROGRESS** - Backend complete, frontend implementation needed

**Completed:**

-   ✅ Backend query for POS piutang
-   ✅ Merge POS with invoice data
-   ✅ Add source field
-   ✅ Payment processing method
-   ✅ Journal entry for payment

**TODO:**

-   ⏳ Update piutang view
-   ⏳ Add payment modal
-   ⏳ Add JavaScript functions
-   ⏳ Create payment history table
-   ⏳ Test integration

---

**Document Version:** 1.0  
**Last Updated:** December 1, 2025  
**Author:** Development Team
