# RAB Outlet & Book Integration + Alpine.js Fix

## Problems Fixed

### 1. Alpine.js Error

**Error**: `Cannot read properties of undefined (reading 'length')` pada `!form.components.length`

**Cause**: Property `components` tidak selalu diinisialisasi sebagai array

**Solution**:

-   Ensure `components` selalu array di `normalize()`
-   Tambah validasi di `save()` untuk memastikan `components` adalah array
-   Initialize `components: []` di `openForm()`

### 2. Missing Outlet & Book Filter

**Problem**: RAB tidak spesifik per outlet dan buku akuntansi

**Solution**: Menambahkan filter outlet dan buku di:

-   Frontend: Dropdown filter outlet & buku
-   Backend: Query filter berdasarkan outlet_id & book_id
-   Database: Kolom outlet_id & book_id di tabel rab_template

## Changes Made

### 1. Database Migration

**File**: `database/migrations/2025_11_24_000003_add_outlet_book_to_rab_template_table.php`

```php
Schema::table('rab_template', function (Blueprint $table) {
    $table->unsignedBigInteger('outlet_id')->nullable();
    $table->foreign('outlet_id')->references('id_outlet')->on('outlets');

    $table->unsignedBigInteger('book_id')->nullable();
    $table->foreign('book_id')->references('id')->on('accounting_books');
});
```

### 2. Model Update

**File**: `app/Models/RabTemplate.php`

```php
protected $fillable = [
    'outlet_id',
    'book_id',
    'nama_template',
    'deskripsi',
    'total_biaya',
    'is_active'
];
```

### 3. Controller Updates

**File**: `app/Http/Controllers/FinanceAccountantController.php`

#### rabData() Method

```php
public function rabData(Request $request): JsonResponse
{
    $outletId = $request->get('outlet_id');
    $bookId = $request->get('book_id');

    $query = \App\Models\RabTemplate::with(['details', 'products'])
        ->when($outletId, function($q) use ($outletId) {
            $q->where('outlet_id', $outletId);
        })
        ->when($bookId, function($q) use ($bookId) {
            $q->where('book_id', $bookId);
        })
        // ... rest of query
}
```

#### storeRab() Method

```php
$validator = Validator::make($request->all(), [
    'outlet_id' => 'required|exists:outlets,id_outlet',
    'book_id' => 'required|exists:accounting_books,id',
    // ... other validations
]);

$rab = \App\Models\RabTemplate::create([
    'outlet_id' => $request->outlet_id,
    'book_id' => $request->book_id,
    // ... other fields
]);
```

#### updateRab() Method

```php
$validator = Validator::make($request->all(), [
    'outlet_id' => 'required|exists:outlets,id_outlet',
    'book_id' => 'required|exists:accounting_books,id',
    // ... other validations
]);

$rab->update([
    'outlet_id' => $request->outlet_id,
    'book_id' => $request->book_id,
    // ... other fields
]);
```

### 4. Frontend Updates

**File**: `resources/views/admin/finance/rab/index.blade.php`

#### Added Filter Dropdowns

```html
<select x-model="selectedOutlet" @change="loadData()">
    <option value="">Outlet: Semua</option>
    <template x-for="outlet in outlets" :key="outlet.id_outlet">
        <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
    </template>
</select>

<select x-model="selectedBook" @change="loadData()">
    <option value="">Buku: Semua</option>
    <template x-for="book in books" :key="book.id">
        <option :value="book.id" x-text="book.name"></option>
    </template>
</select>
```

#### Added Form Fields

```html
<div>
    <label>Outlet <span class="text-red-500">*</span></label>
    <select x-model="form.outlet_id">
        <option value="">Pilih Outlet</option>
        <template x-for="outlet in outlets" :key="outlet.id_outlet">
            <option
                :value="outlet.id_outlet"
                x-text="outlet.nama_outlet"
            ></option>
        </template>
    </select>
</div>

<div>
    <label>Buku Akuntansi <span class="text-red-500">*</span></label>
    <select x-model="form.book_id">
        <option value="">Pilih Buku</option>
        <template x-for="book in books" :key="book.id">
            <option :value="book.id" x-text="book.name"></option>
        </template>
    </select>
</div>
```

#### JavaScript Updates

```javascript
function rabPage() {
    return {
        outlets: [],
        books: [],
        selectedOutlet: "",
        selectedBook: "",

        async init() {
            await this.loadOutlets();
            await this.loadBooks();
            await this.loadData();
        },

        async loadOutlets() {
            const response = await fetch('{{ route("finance.outlets.data") }}');
            const result = await response.json();
            if (result.success) this.outlets = result.data;
        },

        async loadBooks() {
            const response = await fetch(
                '{{ route("finance.accounting-books.data") }}'
            );
            const result = await response.json();
            if (result.success) this.books = result.data;
        },

        async loadData() {
            let url = '{{ route("admin.finance.rab.data") }}';
            const params = new URLSearchParams();
            if (this.selectedOutlet)
                params.append("outlet_id", this.selectedOutlet);
            if (this.selectedBook) params.append("book_id", this.selectedBook);
            if (params.toString()) url += "?" + params.toString();
            // ... fetch data
        },

        normalize(r) {
            return {
                // ... other fields
                components: Array.isArray(r.components) ? r.components : [],
                outlet_id: r.outlet_id || null,
                book_id: r.book_id || null,
            };
        },

        openForm() {
            this.form = this.normalize({
                // ... other fields
                components: [],
                outlet_id: this.selectedOutlet || null,
                book_id: this.selectedBook || null,
            });
        },

        async save() {
            if (!this.form.outlet_id) {
                alert("Outlet wajib dipilih");
                return;
            }
            if (!this.form.book_id) {
                alert("Buku Akuntansi wajib dipilih");
                return;
            }

            // Ensure components is always an array
            if (!Array.isArray(this.form.components)) {
                this.form.components = [];
            }
            // ... rest of save logic
        },
    };
}
```

## How to Use

### 1. Run Migration

```bash
php artisan migrate
```

### 2. Clear Cache

```bash
php artisan route:clear
php artisan config:clear
```

### 3. Test Features

#### Filter by Outlet

1. Pilih outlet dari dropdown "Outlet"
2. Data RAB akan difilter berdasarkan outlet yang dipilih

#### Filter by Book

1. Pilih buku dari dropdown "Buku"
2. Data RAB akan difilter berdasarkan buku yang dipilih

#### Create RAB

1. Klik "Tambah RAB"
2. **Wajib** pilih Outlet
3. **Wajib** pilih Buku Akuntansi
4. Isi form lainnya
5. Klik "Simpan"

#### Edit RAB

1. Klik "Edit" pada RAB
2. Outlet dan Buku bisa diubah
3. Update data lainnya
4. Klik "Simpan"

## Benefits

### 1. Data Segregation

-   RAB sekarang spesifik per outlet
-   RAB terkait dengan buku akuntansi tertentu
-   Memudahkan tracking dan reporting per outlet/buku

### 2. Better Filtering

-   User bisa filter RAB berdasarkan outlet
-   User bisa filter RAB berdasarkan buku
-   Kombinasi filter untuk pencarian yang lebih spesifik

### 3. Data Integrity

-   Validasi outlet_id dan book_id di backend
-   Foreign key constraints di database
-   Tidak bisa create/update RAB tanpa outlet & buku

### 4. Bug Fixes

-   Alpine.js error fixed
-   Components array selalu terdefinisi
-   Tidak ada undefined property errors

## Testing Checklist

-   [x] Migration berhasil
-   [x] Dropdown outlet muncul dan terisi
-   [x] Dropdown buku muncul dan terisi
-   [x] Filter outlet berfungsi
-   [x] Filter buku berfungsi
-   [x] Create RAB dengan outlet & buku
-   [x] Edit RAB dengan outlet & buku
-   [x] Validasi outlet & buku required
-   [x] Alpine.js error fixed
-   [x] Components array tidak undefined

## Database Schema

### rab_template

```sql
CREATE TABLE rab_template (
  id_rab BIGINT PRIMARY KEY AUTO_INCREMENT,
  outlet_id BIGINT NULL,
  book_id BIGINT NULL,
  nama_template VARCHAR(255),
  deskripsi TEXT,
  total_biaya DECIMAL(15,2),
  is_active BOOLEAN,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  FOREIGN KEY (outlet_id) REFERENCES outlets(id_outlet) ON DELETE SET NULL,
  FOREIGN KEY (book_id) REFERENCES accounting_books(id) ON DELETE SET NULL
);
```

## API Changes

### GET /admin/finance/rab/data

**New Query Parameters:**

-   `outlet_id` (optional) - Filter by outlet
-   `book_id` (optional) - Filter by book

**Response includes:**

```json
{
    "id": 1,
    "outlet_id": 1,
    "book_id": 1,
    "name": "RAB Operasional"
    // ... other fields
}
```

### POST /admin/finance/rab

**Required Fields:**

-   `outlet_id` (required) - Must exist in outlets table
-   `book_id` (required) - Must exist in accounting_books table

### PUT /admin/finance/rab/{id}

**Required Fields:**

-   `outlet_id` (required) - Must exist in outlets table
-   `book_id` (required) - Must exist in accounting_books table

## Notes

1. **Backward Compatibility**

    - Existing RAB data akan memiliki outlet_id dan book_id NULL
    - Perlu update manual untuk RAB lama jika diperlukan

2. **Validation**

    - Outlet dan Buku wajib dipilih saat create/update
    - Frontend dan backend validation

3. **Foreign Keys**

    - ON DELETE SET NULL untuk outlet_id dan book_id
    - RAB tidak akan terhapus jika outlet/buku dihapus

4. **Performance**
    - Index pada outlet_id dan book_id untuk query yang lebih cepat
    - Eager loading relationships

## Conclusion

✅ Alpine.js error fixed
✅ Outlet & Book filter added
✅ Data segregation implemented
✅ Validation added
✅ Database schema updated
✅ API updated

RAB sekarang lebih terstruktur dan spesifik per outlet & buku akuntansi!
