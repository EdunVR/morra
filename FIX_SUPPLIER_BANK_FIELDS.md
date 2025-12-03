# Fix: Supplier Bank Fields Not Saving

## Masalah

Form supplier memiliki field untuk bank, rekening, dan atas nama, tetapi data tidak tersimpan ke database saat create atau update.

## Penyebab

1. **Controller tidak menyimpan field bank**: Method `store()` dan `update()` di `SupplierController` tidak include field bank, no_rekening, dan atas_nama
2. **Form belum memiliki input field**: Form supplier belum memiliki input untuk informasi bank

## Solusi

### 1. Update Controller

**File:** `app/Http/Controllers/SupplierController.php`

#### Method `store()`

**Sebelumnya:**

```php
public function store(Request $request)
{
    $supplier = new Supplier();
    $supplier->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0];
    $supplier->nama = $request->nama;
    $supplier->alamat = $request->alamat;
    $supplier->telepon = $request->telepon;
    $supplier->save();
    return response()->json('Data berhasil disimpan', 200);
}
```

**Sesudah:**

```php
public function store(Request $request)
{
    $supplier = new Supplier();
    $supplier->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0];
    $supplier->nama = $request->nama;
    $supplier->alamat = $request->alamat;
    $supplier->telepon = $request->telepon;
    $supplier->email = $request->email;
    $supplier->bank = $request->bank;
    $supplier->no_rekening = $request->no_rekening;
    $supplier->atas_nama = $request->atas_nama;
    $supplier->save();
    return response()->json('Data berhasil disimpan', 200);
}
```

#### Method `update()`

**Sebelumnya:**

```php
public function update(Request $request, string $id)
{
    $supplier = Supplier::find($id);
    $supplier->nama = $request->nama;
    $supplier->alamat = $request->alamat;
    $supplier->telepon = $request->telepon;
    $supplier->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0];
    $supplier->update();
    return response()->json('Data berhasil diupdate', 200);
}
```

**Sesudah:**

```php
public function update(Request $request, string $id)
{
    $supplier = Supplier::find($id);
    $supplier->nama = $request->nama;
    $supplier->alamat = $request->alamat;
    $supplier->telepon = $request->telepon;
    $supplier->email = $request->email;
    $supplier->bank = $request->bank;
    $supplier->no_rekening = $request->no_rekening;
    $supplier->atas_nama = $request->atas_nama;
    $supplier->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0];
    $supplier->update();
    return response()->json('Data berhasil diupdate', 200);
}
```

### 2. Update Form View

**File:** `resources/views/supplier/form.blade.php`

Menambahkan input fields untuk informasi pembayaran:

```html
<div class="form-group row">
    <label for="email" class="col-md-2 col-md-offset-1 control-label"
        >Email</label
    >
    <div class="col-md-9">
        <input type="email" name="email" id="email" class="form-control" />
        <span class="help-block with-errors"></span>
    </div>
</div>
<hr />
<h4 class="text-center"><strong>Informasi Pembayaran</strong></h4>
<div class="form-group row">
    <label for="bank" class="col-md-2 col-md-offset-1 control-label"
        >Bank</label
    >
    <div class="col-md-9">
        <input
            type="text"
            name="bank"
            id="bank"
            class="form-control"
            placeholder="Contoh: BCA, Mandiri, BNI"
        />
        <span class="help-block with-errors"></span>
    </div>
</div>
<div class="form-group row">
    <label for="no_rekening" class="col-md-2 col-md-offset-1 control-label"
        >No. Rekening</label
    >
    <div class="col-md-9">
        <input
            type="text"
            name="no_rekening"
            id="no_rekening"
            class="form-control"
            placeholder="Nomor rekening bank"
        />
        <span class="help-block with-errors"></span>
    </div>
</div>
<div class="form-group row">
    <label for="atas_nama" class="col-md-2 col-md-offset-1 control-label"
        >Atas Nama</label
    >
    <div class="col-md-9">
        <input
            type="text"
            name="atas_nama"
            id="atas_nama"
            class="form-control"
            placeholder="Nama pemilik rekening"
        />
        <span class="help-block with-errors"></span>
    </div>
</div>
```

### 3. Update JavaScript untuk Edit

**File:** `resources/views/supplier/index.blade.php`

Update fungsi `editForm()` untuk mengisi field bank saat edit:

**Sebelumnya:**

```javascript
$.get(url).done((response) => {
    $("#modal-form [name=nama]").val(response.nama);
    $("#modal-form [name=telepon]").val(response.telepon);
    $("#modal-form [name=alamat]").val(response.alamat);
    $("#modal-form [name=id_outlet]").val(response.id_outlet);
});
```

**Sesudah:**

```javascript
$.get(url).done((response) => {
    $("#modal-form [name=nama]").val(response.nama);
    $("#modal-form [name=telepon]").val(response.telepon);
    $("#modal-form [name=alamat]").val(response.alamat);
    $("#modal-form [name=email]").val(response.email);
    $("#modal-form [name=bank]").val(response.bank);
    $("#modal-form [name=no_rekening]").val(response.no_rekening);
    $("#modal-form [name=atas_nama]").val(response.atas_nama);
    $("#modal-form [name=id_outlet]").val(response.id_outlet);
});
```

## Perubahan yang Dilakukan

### Backend (Controller)

-   ✅ Menambahkan `email` ke store dan update
-   ✅ Menambahkan `bank` ke store dan update
-   ✅ Menambahkan `no_rekening` ke store dan update
-   ✅ Menambahkan `atas_nama` ke store dan update

### Frontend (View)

-   ✅ Menambahkan input field `email`
-   ✅ Menambahkan section "Informasi Pembayaran"
-   ✅ Menambahkan input field `bank` dengan placeholder
-   ✅ Menambahkan input field `no_rekening` dengan placeholder
-   ✅ Menambahkan input field `atas_nama` dengan placeholder
-   ✅ Update JavaScript untuk populate field saat edit

## Tampilan Form Supplier

### Sebelum:

```
┌─────────────────────────────┐
│ Nama: [____________]        │
│ Outlet: [___________]       │
│ Telepon: [__________]       │
│ Alamat: [___________]       │
└─────────────────────────────┘
```

### Sesudah:

```
┌─────────────────────────────┐
│ Nama: [____________]        │
│ Outlet: [___________]       │
│ Telepon: [__________]       │
│ Alamat: [___________]       │
│ Email: [____________]       │
│                             │
│ ═══ Informasi Pembayaran ═══│
│ Bank: [____________]        │
│ No. Rekening: [_____]       │
│ Atas Nama: [________]       │
└─────────────────────────────┘
```

## Testing

### Test Create Supplier

1. Buka menu Supplier
2. Klik "Tambah"
3. Isi semua field termasuk:
    - Nama
    - Telepon
    - Alamat
    - Email
    - Bank (contoh: BCA)
    - No. Rekening (contoh: 1234567890)
    - Atas Nama (contoh: PT ABC)
4. Klik "Simpan"
5. **Verifikasi**: Data tersimpan di database

### Test Edit Supplier

1. Buka menu Supplier
2. Klik "Edit" pada supplier yang sudah ada
3. **Verifikasi**: Field bank, no_rekening, atas_nama terisi (jika ada data)
4. Update informasi bank
5. Klik "Simpan"
6. **Verifikasi**: Data terupdate di database

### Test Print Invoice

1. Buat PO dengan supplier yang sudah memiliki info bank
2. Proses hingga status Vendor Bill atau Partial
3. Print invoice
4. **Verifikasi**: Informasi bank muncul di print invoice

## Database Schema

Tabel `supplier` sudah memiliki kolom:

-   `bank` VARCHAR(100) NULLABLE
-   `no_rekening` VARCHAR(50) NULLABLE
-   `atas_nama` VARCHAR(255) NULLABLE

Migration: `2025_11_16_154246_create_supplier_table.php`

## Notes

-   Field bank, no_rekening, dan atas_nama bersifat **optional** (nullable)
-   Jika tidak diisi, akan menampilkan "Belum diisi" di print invoice
-   Placeholder membantu user memahami format yang diharapkan
-   Section "Informasi Pembayaran" dipisahkan dengan `<hr>` untuk clarity
-   Model `Supplier` sudah include field tersebut di `$fillable`

## Troubleshooting: Data Tidak Tersimpan

### Jika data bank masih tidak tersimpan setelah update controller dan form:

#### 1. Cek Log Laravel

Saya sudah menambahkan logging di controller. Cek file `storage/logs/laravel.log` untuk melihat:

-   Data yang dikirim dari form
-   Data yang tersimpan ke database

```bash
# Lihat log terbaru
tail -f storage/logs/laravel.log
```

#### 2. Cek Request Data

Pastikan form mengirim data dengan benar. Buka Browser DevTools (F12) → Network tab:

-   Klik "Simpan" pada form supplier
-   Lihat request yang dikirim
-   Pastikan ada field: `bank`, `no_rekening`, `atas_nama`

#### 3. Cek Database Langsung

```bash
php artisan tinker
```

Kemudian jalankan:

```php
// Cek supplier terakhir
$supplier = App\Models\Supplier::latest()->first();
print_r($supplier->toArray());

// Cek apakah kolom ada
Schema::hasColumn('supplier', 'bank'); // harus return true
Schema::hasColumn('supplier', 'no_rekening'); // harus return true
Schema::hasColumn('supplier', 'atas_nama'); // harus return true
```

#### 4. Cek Model Fillable

Pastikan Model Supplier memiliki field di `$fillable`:

```php
// app/Models/Supplier.php
protected $fillable = [
    'nama',
    'telepon',
    'alamat',
    'email',
    'id_outlet',
    'is_active',
    'bank',
    'no_rekening',
    'atas_nama'
];
```

#### 5. Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### 6. Test Manual Insert

Test insert manual via tinker:

```bash
php artisan tinker
```

```php
$supplier = new App\Models\Supplier();
$supplier->nama = 'Test Supplier';
$supplier->telepon = '08123456789';
$supplier->alamat = 'Test Address';
$supplier->email = 'test@example.com';
$supplier->bank = 'BCA';
$supplier->no_rekening = '1234567890';
$supplier->atas_nama = 'Test Owner';
$supplier->id_outlet = 1;
$supplier->save();

// Cek apakah tersimpan
$supplier->refresh();
echo $supplier->bank; // harus tampil 'BCA'
```

### Kemungkinan Penyebab:

1. **Form tidak mengirim data**: Cek HTML form, pastikan ada input dengan name yang benar
2. **JavaScript error**: Cek console browser untuk error
3. **Validation error**: Cek response dari server
4. **Mass assignment protection**: Pastikan field ada di `$fillable`
5. **Database permission**: Pastikan user database punya akses write

### Logging yang Ditambahkan:

Saya sudah menambahkan logging di controller untuk membantu debug:

**Method `store()`:**

```php
\Log::info('Supplier Store Request', $request->all());
// ... save logic ...
\Log::info('Supplier Saved', $supplier->toArray());
```

**Method `update()`:**

```php
\Log::info('Supplier Update Request', ['id' => $id, 'data' => $request->all()]);
// ... update logic ...
\Log::info('Supplier Updated', $supplier->toArray());
```

Setelah mencoba save/update supplier, cek log untuk melihat:

1. Apakah data bank dikirim dari form?
2. Apakah data bank tersimpan ke database?

### Cara Cek Log:

**Windows (PowerShell):**

```powershell
Get-Content storage/logs/laravel.log -Tail 50
```

**Linux/Mac:**

```bash
tail -50 storage/logs/laravel.log
```

Atau buka file `storage/logs/laravel.log` dengan text editor dan lihat bagian paling bawah.
