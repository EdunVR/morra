# RAB Workflow Improvement - Implementation Guide

## Overview

Memisahkan workflow RAB menjadi:

1. **Create/Edit RAB**: Pegawai input komponen detail (nama, qty, satuan, harga)
2. **Approval**: Admin approve dan set nilai disetujui
3. **Realisasi**: Admin input realisasi pemakaian (modal terpisah)

## Changes Needed

### 1. Form Create/Edit RAB (Pegawai)

**Hapus bagian**:

-   Realisasi Pemakaian section
-   Nilai Disetujui field
-   Status dropdown (auto DRAFT)
-   Ringkasan progress

**Ubah komponen dari simple list menjadi detail table**:

```html
<div class="sm:col-span-2">
    <div class="flex items-center justify-between mb-2">
        <label class="text-sm font-medium text-slate-700"
            >Detail Komponen Biaya</label
        >
        <button
            @click="addComponent()"
            class="text-sm px-3 py-1.5 rounded-lg bg-primary-600 text-white hover:bg-primary-700"
        >
            <i class="bx bx-plus"></i> Tambah Komponen
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-2 text-left">Nama Komponen</th>
                    <th class="px-3 py-2 text-left w-24">Qty</th>
                    <th class="px-3 py-2 text-left w-24">Satuan</th>
                    <th class="px-3 py-2 text-left w-40">Harga Satuan</th>
                    <th class="px-3 py-2 text-left w-40">Subtotal</th>
                    <th class="px-3 py-2 text-center w-20">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, idx) in form.details" :key="idx">
                    <tr class="border-t">
                        <td class="px-3 py-2">
                            <input
                                type="text"
                                x-model="item.nama_komponen"
                                class="w-full rounded border-slate-200 px-2 py-1"
                                placeholder="Nama komponen"
                            />
                        </td>
                        <td class="px-3 py-2">
                            <input
                                type="text"
                                x-model="item.jumlah"
                                @input="item.jumlah = formatNumber($event.target.value); calculateSubtotal(item)"
                                @blur="item.jumlah = parseNumber(item.jumlah)"
                                class="w-full rounded border-slate-200 px-2 py-1 text-right"
                                placeholder="0"
                            />
                        </td>
                        <td class="px-3 py-2">
                            <input
                                type="text"
                                x-model="item.satuan"
                                class="w-full rounded border-slate-200 px-2 py-1"
                                placeholder="pcs"
                            />
                        </td>
                        <td class="px-3 py-2">
                            <input
                                type="text"
                                x-model="item.harga_satuan"
                                @input="item.harga_satuan = formatNumber($event.target.value); calculateSubtotal(item)"
                                @blur="item.harga_satuan = parseNumber(item.harga_satuan)"
                                class="w-full rounded border-slate-200 px-2 py-1 text-right"
                                placeholder="0"
                            />
                        </td>
                        <td class="px-3 py-2">
                            <div
                                class="text-right font-medium"
                                x-text="formatCurrency(item.budget)"
                            ></div>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button
                                @click="form.details.splice(idx, 1); calculateTotal()"
                                class="text-red-600 hover:text-red-700"
                            >
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                </template>
                <tr x-show="!form.details || form.details.length === 0">
                    <td
                        colspan="6"
                        class="px-3 py-8 text-center text-slate-500"
                    >
                        Belum ada komponen. Klik "Tambah Komponen" untuk
                        menambah.
                    </td>
                </tr>
            </tbody>
            <tfoot class="bg-slate-50 font-medium">
                <tr>
                    <td colspan="4" class="px-3 py-2 text-right">
                        Total Budget:
                    </td>
                    <td
                        class="px-3 py-2 text-right"
                        x-text="formatCurrency(form.budget_total)"
                    ></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
```

### 2. JavaScript Functions

**Add these helper functions**:

```javascript
// Format number with thousand separator while typing
formatNumber(value) {
  if (!value) return '';
  // Remove non-numeric characters except decimal point
  let num = value.toString().replace(/[^\d.]/g, '');
  // Split by decimal point
  let parts = num.split('.');
  // Format integer part with thousand separator
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  // Return formatted number
  return parts.join(',');
},

// Parse formatted number back to float
parseNumber(value) {
  if (!value) return 0;
  // Remove thousand separator and replace comma with dot
  return parseFloat(value.toString().replace(/\./g, '').replace(',', '.')) || 0;
},

// Format currency for display
formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(value || 0);
},

// Add new component row
addComponent() {
  if (!Array.isArray(this.form.details)) {
    this.form.details = [];
  }
  this.form.details.push({
    nama_komponen: '',
    jumlah: 0,
    satuan: 'pcs',
    harga_satuan: 0,
    budget: 0,
    nilai_disetujui: 0,
    realisasi_pemakaian: 0,
    disetujui: false
  });
},

// Calculate subtotal for a component
calculateSubtotal(item) {
  const qty = this.parseNumber(item.jumlah);
  const price = this.parseNumber(item.harga_satuan);
  item.budget = qty * price;
  this.calculateTotal();
},

// Calculate total budget
calculateTotal() {
  this.form.budget_total = this.form.details.reduce((sum, item) => {
    return sum + (item.budget || 0);
  }, 0);
},
```

### 3. Update openForm()

```javascript
openForm(){
  this.form = {
    id: null,
    created_at: new Date().toISOString().slice(0,10),
    name: '',
    description: '',
    outlet_id: this.selectedOutlet,
    book_id: this.selectedBook,
    budget_total: 0,
    status: 'DRAFT',
    has_product: false,
    details: []  // Array of component objects
  };
  this.showForm = true;
},
```

### 4. Update save() function

```javascript
async save(){
  if(!this.form.name || !this.form.created_at){
    alert('Nama & Tanggal wajib diisi');
    return;
  }
  if(!this.form.outlet_id){
    alert('Outlet wajib dipilih');
    return;
  }
  if(!this.form.book_id){
    alert('Buku Akuntansi wajib dipilih');
    return;
  }
  if(!this.form.details || this.form.details.length === 0){
    alert('Minimal harus ada 1 komponen');
    return;
  }

  // Parse all formatted numbers back to float
  this.form.details = this.form.details.map(item => ({
    ...item,
    jumlah: this.parseNumber(item.jumlah),
    harga_satuan: this.parseNumber(item.harga_satuan),
    budget: item.budget || 0
  }));

  this.loading = true;
  try {
    const url = this.form.id
      ? `{{ url('admin/finance/rab') }}/${this.form.id}`
      : '{{ route("admin.finance.rab.store") }}';

    const method = this.form.id ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(this.form)
    });

    const result = await response.json();

    if(result.success){
      await this.loadData();
      this.showForm = false;
      alert(result.message || 'Data berhasil disimpan');
    } else {
      alert(result.message || 'Gagal menyimpan data');
    }
  } catch(e) {
    console.error('Error saving RAB:', e);
    alert('Terjadi kesalahan saat menyimpan data');
  } finally {
    this.loading = false;
  }
},
```

### 5. Add Modal Realisasi (Admin Only)

```html
<!-- Modal Realisasi -->
<div
    x-show="showRealisasi"
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3"
>
    <div
        @click.outside="closeRealisasi()"
        class="w-full max-w-4xl bg-white rounded-2xl shadow-float overflow-hidden"
    >
        <div
            class="px-5 py-3 border-b border-slate-100 flex items-center justify-between"
        >
            <div>
                <div class="font-semibold">Input Realisasi Pemakaian</div>
                <div
                    class="text-sm text-slate-600"
                    x-text="realisasiData?.name"
                ></div>
            </div>
            <button
                @click="closeRealisasi()"
                class="p-2 -m-2 hover:bg-slate-100 rounded-lg"
            >
                <i class="bx bx-x text-xl"></i>
            </button>
        </div>

        <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Komponen</th>
                        <th class="px-3 py-2 text-right">Budget</th>
                        <th class="px-3 py-2 text-right">Disetujui</th>
                        <th class="px-3 py-2 text-right">Realisasi</th>
                        <th class="px-3 py-2 text-right">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                    <template
                        x-for="(item, idx) in realisasiData?.details"
                        :key="idx"
                    >
                        <tr class="border-t">
                            <td
                                class="px-3 py-2"
                                x-text="item.nama_komponen"
                            ></td>
                            <td
                                class="px-3 py-2 text-right"
                                x-text="formatCurrency(item.budget)"
                            ></td>
                            <td
                                class="px-3 py-2 text-right"
                                x-text="formatCurrency(item.nilai_disetujui)"
                            ></td>
                            <td class="px-3 py-2">
                                <input
                                    type="text"
                                    x-model="item.realisasi_pemakaian"
                                    @input="item.realisasi_pemakaian = formatNumber($event.target.value)"
                                    @blur="item.realisasi_pemakaian = parseNumber(item.realisasi_pemakaian)"
                                    class="w-full rounded border-slate-200 px-2 py-1 text-right"
                                />
                            </td>
                            <td
                                class="px-3 py-2 text-right"
                                x-text="formatCurrency(item.nilai_disetujui - parseNumber(item.realisasi_pemakaian))"
                            ></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div
            class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2"
        >
            <button
                @click="closeRealisasi()"
                class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50"
            >
                Batal
            </button>
            <button
                @click="saveRealisasi()"
                class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700"
            >
                Simpan Realisasi
            </button>
        </div>
    </div>
</div>
```

### 6. Add Realisasi Functions

```javascript
showRealisasi: false,
realisasiData: null,

openRealisasi(rab) {
  this.realisasiData = JSON.parse(JSON.stringify(rab));
  // Format numbers for display
  this.realisasiData.details = this.realisasiData.details.map(item => ({
    ...item,
    realisasi_pemakaian: this.formatNumber(item.realisasi_pemakaian || 0)
  }));
  this.showRealisasi = true;
},

closeRealisasi() {
  this.showRealisasi = false;
  this.realisasiData = null;
},

async saveRealisasi() {
  if (!this.realisasiData) return;

  this.loading = true;
  try {
    // Parse formatted numbers
    const details = this.realisasiData.details.map(item => ({
      id: item.id,
      realisasi_pemakaian: this.parseNumber(item.realisasi_pemakaian)
    }));

    const response = await fetch(`{{ url('admin/finance/rab') }}/${this.realisasiData.id}/realisasi`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ details })
    });

    const result = await response.json();

    if(result.success){
      await this.loadData();
      this.closeRealisasi();
      alert('Realisasi berhasil disimpan');
    } else {
      alert(result.message || 'Gagal menyimpan realisasi');
    }
  } catch(e) {
    console.error('Error saving realisasi:', e);
    alert('Terjadi kesalahan saat menyimpan realisasi');
  } finally {
    this.loading = false;
  }
},
```

### 7. Add Button in Table

```html
<button
    @click="openRealisasi(r)"
    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2 py-1.5 hover:bg-slate-50"
>
    <i class="bx bx-money"></i><span class="text-sm">Realisasi</span>
</button>
```

### 8. Backend Route

Add to `routes/web.php`:

```php
Route::post('rab/{id}/realisasi', [FinanceAccountantController::class, 'saveRealisasi'])->name('rab.realisasi');
```

### 9. Backend Controller Method

Add to `FinanceAccountantController.php`:

```php
public function saveRealisasi(Request $request, $id): JsonResponse
{
    DB::beginTransaction();
    try {
        $rab = \App\Models\RabTemplate::find($id);

        if (!$rab) {
            return response()->json([
                'success' => false,
                'message' => 'RAB tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'details' => 'required|array',
            'details.*.id' => 'required|exists:rab_detail,id',
            'details.*.realisasi_pemakaian' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->details as $detailData) {
            $detail = \App\Models\RabDetail::find($detailData['id']);
            if ($detail && $detail->id_rab == $rab->id_rab) {
                $detail->update([
                    'realisasi_pemakaian' => $detailData['realisasi_pemakaian']
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Realisasi berhasil disimpan'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error saving realisasi: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan realisasi: ' . $e->getMessage()
        ], 500);
    }
}
```

## Summary of Changes

### What's Removed:

-   ❌ Simple components array (replaced with detailed table)
-   ❌ Realisasi section from create/edit form
-   ❌ Nilai disetujui from create form
-   ❌ Status dropdown (auto DRAFT)
-   ❌ Progress summary in form

### What's Added:

-   ✅ Detailed component table (nama, qty, satuan, harga, subtotal)
-   ✅ Auto-format number with thousand separator
-   ✅ Auto-calculate subtotal and total
-   ✅ Separate modal for realisasi input
-   ✅ Better validation
-   ✅ Cleaner workflow

### Benefits:

1. **Clear Separation**: Pegawai create, Admin approve & input realisasi
2. **Better UX**: Auto-format numbers, no confusion with thousands/millions
3. **Data Integrity**: Proper validation, structured data
4. **Scalability**: Easy to add approval workflow later

## Implementation Steps

1. Backup current file
2. Update frontend template (form section)
3. Add JavaScript helper functions
4. Add realisasi modal
5. Update backend controller
6. Add route
7. Test thoroughly

## Testing Checklist

-   [ ] Create RAB with multiple components
-   [ ] Auto-format numbers work
-   [ ] Subtotal calculated correctly
-   [ ] Total budget calculated correctly
-   [ ] Save RAB successfully
-   [ ] Edit RAB loads correctly
-   [ ] Open realisasi modal
-   [ ] Input realisasi
-   [ ] Save realisasi successfully
-   [ ] Data persists correctly
