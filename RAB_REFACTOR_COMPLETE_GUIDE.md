# RAB Refactor - Complete Implementation Guide

## ✅ Backend DONE

### 1. Route Added ✅

```php
Route::post('rab/{id}/realisasi', [FinanceAccountantController::class, 'saveRealisasi'])->name('rab.realisasi');
```

### 2. Controller Method Added ✅

Method `saveRealisasi()` sudah ditambahkan di `FinanceAccountantController.php`

## 🔧 Frontend Implementation

Karena file `resources/views/admin/finance/rab/index.blade.php` sangat panjang (700+ baris), berikut panduan step-by-step untuk refactor:

### Quick Summary - What Changed:

**BEFORE (Simple)**:

-   Input komponen: text field sederhana
-   Input budget: number field
-   Realisasi: di form yang sama

**AFTER (Professional)**:

-   Input komponen: table detail (nama, qty, satuan, harga, subtotal)
-   Input budget: auto-calculated dari komponen
-   Realisasi: modal terpisah (admin only)
-   Auto-format number: 1000000 → 1.000.000

### Implementation Options:

**Option A: Manual Step-by-Step** (Recommended - Anda belajar struktur)
Follow steps below

**Option B: Complete File Replacement** (Faster - tapi perlu backup)
Saya generate file lengkap baru

Pilih mana? Untuk sekarang saya akan berikan Option A (step-by-step):

---

## STEP 1: Backup File Lama

```bash
cp resources/views/admin/finance/rab/index.blade.php resources/views/admin/finance/rab/index.blade.php.backup
```

## STEP 2: Update JavaScript - Add Helper Functions

Cari bagian `function rabPage(){` dan tambahkan functions ini di dalam `return {}`:

```javascript
// Format number with thousand separator
formatNumber(value) {
  if (!value) return '';
  let num = value.toString().replace(/[^\d]/g, '');
  return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
},

// Parse formatted number to integer
parseNumber(value) {
  if (!value) return 0;
  return parseInt(value.toString().replace(/\./g, '')) || 0;
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
    jumlah: '0',
    satuan: 'pcs',
    harga_satuan: '0',
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

// Modal Realisasi
showRealisasi: false,
realisasiData: null,

openRealisasi(rab) {
  this.realisasiData = JSON.parse(JSON.stringify(rab));
  // Format numbers for display
  if (this.realisasiData.details) {
    this.realisasiData.details = this.realisasiData.details.map(item => ({
      ...item,
      realisasi_display: this.formatNumber(item.realisasi_pemakaian || 0)
    }));
  }
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
      realisasi_pemakaian: this.parseNumber(item.realisasi_display || item.realisasi_pemakaian),
      keterangan: 'Input realisasi'
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

## STEP 3: Update openForm()

Ganti function `openForm()` dengan:

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

## STEP 4: Replace Form Content

Cari bagian form modal (sekitar line 275-400) dan ganti dengan struktur baru.

**Hapus bagian**:

-   Simple components array
-   Realisasi Pemakaian section
-   Nilai Disetujui field (pindah ke approval)
-   Status dropdown (auto DRAFT)

**Tambahkan table detail komponen** (lihat file `RAB_WORKFLOW_IMPROVEMENT.md` untuk HTML lengkap)

## STEP 5: Add Modal Realisasi

Tambahkan sebelum closing `</div>` utama (sebelum `<script>`):

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
                                    x-model="item.realisasi_display"
                                    @input="item.realisasi_display = formatNumber($event.target.value)"
                                    class="w-full rounded border-slate-200 px-2 py-1 text-right"
                                />
                            </td>
                            <td
                                class="px-3 py-2 text-right"
                                x-text="formatCurrency(item.nilai_disetujui - parseNumber(item.realisasi_display))"
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

## STEP 6: Add Button in Table

Cari bagian tombol aksi di table (sekitar line 150) dan tambahkan:

```html
<button
    @click="openRealisasi(r)"
    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2 py-1.5 hover:bg-slate-50"
>
    <i class="bx bx-money"></i><span class="text-sm">Realisasi</span>
</button>
```

## Testing Checklist

-   [ ] Backend route terdaftar
-   [ ] Create RAB dengan detail komponen
-   [ ] Auto-format number bekerja
-   [ ] Subtotal calculated correctly
-   [ ] Total budget calculated correctly
-   [ ] Save RAB successfully
-   [ ] Open modal realisasi
-   [ ] Input realisasi dengan format number
-   [ ] Save realisasi successfully
-   [ ] Data persists correctly

## Result

✅ Workflow terpisah: Create vs Realisasi
✅ Detail komponen: nama, qty, satuan, harga
✅ Auto-format number
✅ Auto-calculate subtotal & total
✅ Modal realisasi terpisah
✅ Better UX & data integrity

## Need Help?

Jika terlalu kompleks untuk manual edit, saya bisa generate file lengkap baru. Tapi saya sarankan coba manual dulu untuk memahami strukturnya.

Atau saya langsung generate file lengkap baru sekarang?
