# RAB Urgent Fix - Komponen Tidak Tersimpan & Auto Format Number

## Problem

1. Komponen yang diinput tidak tersimpan ke database
2. Input angka tidak ada format ribuan/jutaan

## Quick Solution

### 1. Fix Backend - Simpan Komponen sebagai Details

Update `storeRab()` di `FinanceAccountantController.php`:

```php
// Setelah create RAB template
$rab = \App\Models\RabTemplate::create([...]);

// TAMBAHKAN: Create details from components
if ($request->has('components') && is_array($request->components)) {
    foreach ($request->components as $index => $componentName) {
        if (!empty($componentName)) {
            \App\Models\RabDetail::create([
                'id_rab' => $rab->id_rab,
                'nama_komponen' => $componentName,
                'item' => $componentName,  // For compatibility
                'deskripsi' => '',
                'jumlah' => 1,
                'qty' => 1,
                'satuan' => 'pcs',
                'harga_satuan' => 0,
                'harga' => 0,
                'budget' => 0,
                'subtotal' => 0,
                'nilai_disetujui' => 0,
                'realisasi_pemakaian' => 0,
                'disetujui' => false
            ]);
        }
    }
}
```

### 2. Add Auto-Format Number Helper

Tambahkan di bagian JavaScript `<script>`:

```javascript
// Add to rabPage() return object
formatNumber(value) {
  if (!value) return '';
  let num = value.toString().replace(/[^\d]/g, '');
  return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
},

parseNumber(value) {
  if (!value) return 0;
  return parseInt(value.toString().replace(/\./g, '')) || 0;
},
```

### 3. Update Input Fields dengan Auto-Format

Ganti input budget_total dan approved_value:

```html
<!-- Budget Total -->
<div>
    <label class="text-sm text-slate-600">Budget Total</label>
    <input
        type="text"
        x-model="form.budget_total_display"
        @input="form.budget_total_display = formatNumber($event.target.value)"
        @blur="form.budget_total = parseNumber(form.budget_total_display)"
        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-right"
        placeholder="0"
    />
    <div
        class="text-xs text-slate-500 mt-1"
        x-text="'Rp ' + formatNumber(form.budget_total)"
    ></div>
</div>

<!-- Nilai Disetujui -->
<div>
    <label class="text-sm text-slate-600">Nilai Disetujui</label>
    <input
        type="text"
        x-model="form.approved_value_display"
        @input="form.approved_value_display = formatNumber($event.target.value)"
        @blur="form.approved_value = parseNumber(form.approved_value_display)"
        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-right"
        placeholder="0"
    />
    <div
        class="text-xs text-slate-500 mt-1"
        x-text="'Rp ' + formatNumber(form.approved_value)"
    ></div>
</div>
```

### 4. Update openForm()

```javascript
openForm(){
  this.form = {
    id: null,
    created_at: new Date().toISOString().slice(0,10),
    name: '',
    description: '',
    components: [],
    budget_total: 0,
    budget_total_display: '',  // For formatted display
    approved_value: 0,
    approved_value_display: '',  // For formatted display
    spends: [],
    status: 'DRAFT',
    has_product: false,
    outlet_id: this.selectedOutlet,
    book_id: this.selectedBook,
    details: []
  };
  this.showForm = true;
},
```

### 5. Update save() - Parse Numbers

```javascript
async save(){
  // ... validations ...

  // Parse formatted numbers before sending
  this.form.budget_total = this.parseNumber(this.form.budget_total_display || this.form.budget_total);
  this.form.approved_value = this.parseNumber(this.form.approved_value_display || this.form.approved_value);

  // ... rest of save logic ...
}
```

## Implementation Steps

1. Update backend `storeRab()` method
2. Add formatNumber() and parseNumber() functions
3. Update input fields
4. Update openForm()
5. Update save()
6. Test

## Testing

1. Create RAB dengan komponen
2. Cek database - komponen harus tersimpan di `rab_detail`
3. Input angka dengan format: 1000000 → 1.000.000
4. Save dan reload - angka tetap formatted

## Result

✅ Komponen tersimpan ke database
✅ Input angka dengan format ribuan/jutaan
✅ User tidak bingung dengan angka besar
✅ Data tetap valid di database
