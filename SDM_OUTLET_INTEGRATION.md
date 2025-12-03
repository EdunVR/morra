# 🏢 Integrasi Outlet - Modul SDM Kepegawaian

## Status: ✅ SELESAI

Modul SDM Kepegawaian sekarang sudah terintegrasi dengan sistem outlet, sehingga setiap karyawan terikat dengan outlet tertentu dan user hanya bisa melihat karyawan dari outlet yang mereka akses.

---

## 🎯 Fitur Outlet-Specific

### 1. **Outlet Assignment**

-   Setiap karyawan harus terikat dengan 1 outlet
-   Field outlet wajib diisi saat tambah/edit karyawan
-   Relasi database: `recruitments.outlet_id` → `outlets.id_outlet`

### 2. **Outlet Filtering**

-   User hanya bisa melihat karyawan dari outlet yang mereka akses
-   Super admin bisa melihat semua karyawan dari semua outlet
-   Filter outlet tersedia di halaman utama

### 3. **Permission-Based Access**

-   Menggunakan trait `HasOutletFilter` untuk konsistensi
-   Validasi akses outlet saat create/update/delete
-   Error 403 jika user tidak punya akses ke outlet tertentu

---

## 📊 Perubahan Database

### Migration Baru

**File**: `2024_12_02_000002_add_outlet_to_recruitments_table.php`

```sql
ALTER TABLE recruitments
ADD COLUMN outlet_id BIGINT UNSIGNED NULL AFTER id,
ADD FOREIGN KEY (outlet_id) REFERENCES outlets(id_outlet) ON DELETE SET NULL;
```

**Status**: ✅ Sudah dijalankan

---

## 🔧 Perubahan Backend

### 1. Model Recruitment

**File**: `app/Models/Recruitment.php`

**Perubahan**:

-   ✅ Tambah `outlet_id` ke fillable
-   ✅ Tambah relasi `outlet()` ke model Outlet

```php
public function outlet()
{
    return $this->belongsTo(\App\Models\Outlet::class, 'outlet_id', 'id_outlet');
}
```

### 2. Controller

**File**: `app/Http/Controllers/RecruitmentManagementController.php`

**Perubahan**:

-   ✅ Tambah `use HasOutletFilter` trait
-   ✅ Method `index()`: Pass `$outlets` ke view
-   ✅ Method `getData()`: Apply outlet filter
-   ✅ Method `store()`: Validasi outlet access
-   ✅ Method `update()`: Validasi outlet access
-   ✅ Method `exportPdf()`: Apply outlet filter
-   ✅ Method `exportExcel()`: Apply outlet filter

**Fitur Keamanan**:

```php
// Validasi user punya akses ke outlet
$this->authorizeOutletAccess($request->outlet_id);

// Filter query berdasarkan outlet user
$query = $this->applyOutletFilter($query, 'outlet_id');
```

### 3. Export Class

**File**: `app/Exports/RecruitmentExport.php`

**Perubahan**:

-   ✅ Tambah parameter `$outletFilter` dan `$userOutletIds`
-   ✅ Apply outlet filter di query
-   ✅ Tambah kolom "Outlet" di export
-   ✅ Load relasi outlet

---

## 🎨 Perubahan Frontend

### 1. View Index

**File**: `resources/views/admin/sdm/kepegawaian/index.blade.php`

**Perubahan**:

-   ✅ Tambah filter dropdown outlet (5 kolom filter)
-   ✅ Tambah kolom "Outlet" di tabel (9 kolom total)
-   ✅ Tambah field outlet di modal form (required)
-   ✅ Update JavaScript untuk handle outlet filter
-   ✅ Update export functions dengan outlet filter

**Filter Section**:

```html
<select id="outletFilter">
    <option value="all">Semua Outlet</option>
    @foreach($outlets as $outlet)
    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
    @endforeach
</select>
```

**Form Field**:

```html
<select id="outlet_id" required>
    <option value="">Pilih Outlet</option>
    @foreach($outlets as $outlet)
    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
    @endforeach
</select>
```

### 2. PDF Template

**File**: `resources/views/admin/sdm/kepegawaian/pdf.blade.php`

**Perubahan**:

-   ✅ Tambah kolom "Outlet" di tabel PDF

---

## 🔐 Keamanan & Validasi

### 1. Validation Rules

```php
'outlet_id' => 'required|exists:outlets,id_outlet'
```

### 2. Access Control

```php
// Saat create/update
$this->authorizeOutletAccess($request->outlet_id);

// Saat update, cek outlet lama juga
$this->authorizeOutletAccess($employee->outlet_id);
```

### 3. Query Filtering

```php
// Otomatis filter berdasarkan outlet user
$query = $this->applyOutletFilter($query, 'outlet_id');
```

---

## 📋 Cara Kerja

### Untuk Super Admin:

1. Bisa melihat semua karyawan dari semua outlet
2. Bisa assign karyawan ke outlet manapun
3. Filter outlet menampilkan semua outlet

### Untuk User Biasa:

1. Hanya bisa melihat karyawan dari outlet yang mereka akses
2. Hanya bisa assign karyawan ke outlet yang mereka akses
3. Filter outlet hanya menampilkan outlet yang mereka akses
4. Error 403 jika mencoba akses outlet lain

---

## 🧪 Testing

### Test Case 1: Super Admin

```
1. Login sebagai super_admin
2. Buka halaman kepegawaian
3. Filter outlet: Harus menampilkan semua outlet
4. Tambah karyawan: Bisa pilih outlet manapun
5. Lihat data: Menampilkan karyawan dari semua outlet
```

### Test Case 2: User dengan 1 Outlet

```
1. Login sebagai user dengan akses 1 outlet (misal: Outlet A)
2. Buka halaman kepegawaian
3. Filter outlet: Hanya menampilkan Outlet A
4. Tambah karyawan: Hanya bisa pilih Outlet A
5. Lihat data: Hanya menampilkan karyawan dari Outlet A
```

### Test Case 3: User dengan Multiple Outlets

```
1. Login sebagai user dengan akses 2 outlet (Outlet A & B)
2. Buka halaman kepegawaian
3. Filter outlet: Menampilkan Outlet A dan B
4. Tambah karyawan: Bisa pilih Outlet A atau B
5. Lihat data: Menampilkan karyawan dari Outlet A dan B
6. Filter by Outlet A: Hanya tampil karyawan Outlet A
```

### Test Case 4: Security

```
1. Login sebagai user dengan akses Outlet A saja
2. Coba tambah karyawan dengan outlet_id = Outlet B (via API/Postman)
3. Expected: Error 403 "Anda tidak memiliki akses ke outlet ini"
```

---

## 📊 Struktur Data

### Tabel Recruitments (Updated)

```
id                          BIGINT (PK)
outlet_id                   BIGINT (FK → outlets.id_outlet) ✨ NEW
name                        VARCHAR(255)
position                    VARCHAR(255)
department                  VARCHAR(255)
status                      ENUM('active','inactive','resigned')
jobdesk                     JSON
fingerprint_id              VARCHAR(255)
is_registered_fingerprint   BOOLEAN
salary                      DECIMAL(15,2)
hourly_rate                 DECIMAL(15,2)
phone                       VARCHAR(255)
email                       VARCHAR(255)
address                     TEXT
join_date                   DATE
resign_date                 DATE
created_at                  TIMESTAMP
updated_at                  TIMESTAMP
```

---

## 🎯 Benefits

### 1. **Data Isolation**

-   Setiap outlet punya data karyawan sendiri
-   Tidak ada data leak antar outlet

### 2. **Multi-Tenant Support**

-   Satu sistem bisa handle multiple outlets
-   User hanya lihat data outlet mereka

### 3. **Scalability**

-   Mudah menambah outlet baru
-   Tidak perlu duplikasi sistem

### 4. **Security**

-   Permission-based access control
-   Validasi di level controller dan database

### 5. **Reporting**

-   Laporan per outlet
-   Laporan gabungan (super admin)

---

## 📝 Catatan Penting

### 1. **Backward Compatibility**

-   Karyawan lama (sebelum update) akan punya `outlet_id = NULL`
-   Perlu update manual untuk assign outlet ke karyawan lama
-   Query tetap aman karena menggunakan `whereIn` bukan `where`

### 2. **Foreign Key Constraint**

-   `ON DELETE SET NULL`: Jika outlet dihapus, karyawan tidak ikut terhapus
-   `outlet_id` akan menjadi NULL
-   Bisa diubah ke `ON DELETE CASCADE` jika ingin karyawan ikut terhapus

### 3. **Performance**

-   Index otomatis dibuat untuk foreign key
-   Query sudah optimal dengan eager loading (`with('outlet')`)

---

## 🚀 Next Steps (Opsional)

### Priority High:

1. Script untuk assign outlet ke karyawan lama (yang outlet_id = NULL)
2. Bulk update outlet untuk multiple karyawan

### Priority Medium:

3. Dashboard stats per outlet
4. Laporan komparasi antar outlet
5. Transfer karyawan antar outlet

### Priority Low:

6. History perubahan outlet karyawan
7. Notifikasi ke admin outlet saat ada karyawan baru

---

## ✅ Checklist

-   [x] Migration outlet_id dibuat
-   [x] Migration berhasil dijalankan
-   [x] Model updated (fillable & relasi)
-   [x] Controller menggunakan HasOutletFilter trait
-   [x] Validation outlet_id di store & update
-   [x] Authorization check di store & update
-   [x] Query filtering di getData
-   [x] Query filtering di export
-   [x] View: Filter outlet dropdown
-   [x] View: Form field outlet (required)
-   [x] View: Tabel kolom outlet
-   [x] View: JavaScript handle outlet filter
-   [x] PDF: Kolom outlet
-   [x] Excel: Kolom outlet
-   [x] Testing: Super admin access
-   [x] Testing: User outlet access
-   [x] Testing: Security validation

---

## 📞 Support

Jika ada pertanyaan tentang integrasi outlet:

1. Lihat trait `HasOutletFilter` untuk referensi
2. Lihat controller lain yang sudah implement (CustomerManagementController, BahanController, dll)
3. Test dengan user yang punya akses berbeda-beda

---

**Updated**: 2 Desember 2024  
**Feature**: Outlet-Specific Integration  
**Status**: ✅ PRODUCTION READY  
**Breaking Changes**: None (backward compatible)
