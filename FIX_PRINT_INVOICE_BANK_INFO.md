# Fix: Informasi Bank Supplier di Print Invoice

## Masalah

Informasi pembayaran (bank, rekening, atas nama) tidak muncul di print invoice.

## Penyebab

Kondisi `@if` terlalu ketat - hanya menampilkan jika ada data bank ATAU no_rekening. Jika supplier belum memiliki data tersebut, section tidak muncul sama sekali.

## Solusi

### File: `resources/views/admin/pembelian/purchase-order/print-invoice.blade.php`

**Sebelumnya:**

```blade
@if($purchaseOrder->supplier && ($purchaseOrder->supplier->bank || $purchaseOrder->supplier->no_rekening))
<div class="bank-info">
    <h4>Informasi Pembayaran - {{ $purchaseOrder->supplier->nama }}</h4>
    @if($purchaseOrder->supplier->bank)
    <div class="info-item">
        <span class="info-label">Bank:</span>
        <span class="info-value">{{ $purchaseOrder->supplier->bank }}</span>
    </div>
    @endif
    ...
</div>
@endif
```

**Sesudah:**

```blade
@if($purchaseOrder->supplier)
<div class="bank-info">
    <h4>Informasi Pembayaran - {{ $purchaseOrder->supplier->nama }}</h4>
    <div class="info-item">
        <span class="info-label">Bank:</span>
        <span class="info-value">{{ $purchaseOrder->supplier->bank ?? 'Belum diisi' }}</span>
    </div>
    <div class="info-item">
        <span class="info-label">No. Rekening:</span>
        <span class="info-value">{{ $purchaseOrder->supplier->no_rekening ?? 'Belum diisi' }}</span>
    </div>
    <div class="info-item">
        <span class="info-label">Atas Nama:</span>
        <span class="info-value">{{ $purchaseOrder->supplier->atas_nama ?? 'Belum diisi' }}</span>
    </div>
</div>
@endif
```

## Perubahan

1. **Kondisi lebih sederhana**: Hanya cek apakah supplier ada
2. **Selalu tampilkan section**: Informasi bank selalu muncul
3. **Fallback value**: Jika data kosong, tampilkan "Belum diisi"

## Manfaat

-   ✅ Section informasi bank selalu terlihat di invoice
-   ✅ User tahu bahwa ada field yang perlu diisi
-   ✅ Lebih informatif untuk supplier yang belum lengkap datanya
-   ✅ Konsisten dengan format invoice profesional

## Cara Mengisi Data Bank Supplier

1. Buka menu **Pembelian → Purchase Order**
2. Klik tombol **"Kelola Supplier"**
3. Edit supplier yang ingin ditambahkan info bank
4. Isi field:
    - **Bank**: Nama bank (contoh: BCA, Mandiri, BNI)
    - **No. Rekening**: Nomor rekening bank
    - **Atas Nama**: Nama pemilik rekening
5. Simpan

## Testing

1. **Print invoice dengan supplier yang sudah ada data bank**

    - Harus menampilkan: Bank, No. Rekening, Atas Nama dengan data lengkap

2. **Print invoice dengan supplier yang belum ada data bank**

    - Harus menampilkan: Bank, No. Rekening, Atas Nama dengan text "Belum diisi"

3. **Print invoice dengan supplier yang sebagian data bank**
    - Data yang ada: tampil normal
    - Data yang kosong: tampil "Belum diisi"

## Catatan

-   Informasi bank supplier disimpan di tabel `supplier`
-   Kolom: `bank`, `no_rekening`, `atas_nama` (semua nullable)
-   Migration sudah ada di: `2025_11_16_154246_create_supplier_table.php`
-   Model `Supplier` sudah include field tersebut di `$fillable`
