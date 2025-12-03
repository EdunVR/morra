# RAB API Reference

## Base URL

```
/admin/finance/rab
```

## Authentication

Semua endpoint memerlukan autentikasi Laravel session dan CSRF token.

## Endpoints

### 1. Get RAB Index Page

Menampilkan halaman index RAB.

**Endpoint:** `GET /admin/finance/rab`

**Response:** HTML page

---

### 2. Get RAB Data

Mengambil semua data RAB dalam format JSON.

**Endpoint:** `GET /admin/finance/rab/data`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| outlet_id | integer | No | Filter by outlet ID |
| search | string | No | Search in name and description |
| status | string | No | Filter by status (all, DRAFT, APPROVED_ALL, etc) |
| has_product | string | No | Filter by product relation (all, YES, NO) |

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "created_at": "2025-11-24",
            "name": "RAB Operasional",
            "description": "Deskripsi RAB",
            "components": ["Komponen 1", "Komponen 2"],
            "budget_total": 10000000,
            "approved_value": 9000000,
            "spends": [
                {
                    "desc": "Termin 1",
                    "amount": 3000000
                }
            ],
            "status": "APPROVED_ALL",
            "has_product": true,
            "details": [
                {
                    "id": 1,
                    "nama_komponen": "Komponen 1",
                    "jumlah": 10,
                    "satuan": "pcs",
                    "harga_satuan": 500000,
                    "budget": 5000000,
                    "nilai_disetujui": 4500000,
                    "realisasi_pemakaian": 3000000,
                    "disetujui": true,
                    "deskripsi": "Deskripsi komponen"
                }
            ]
        }
    ]
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "Gagal mengambil data RAB: [error message]"
}
```

---

### 3. Create RAB

Membuat RAB baru.

**Endpoint:** `POST /admin/finance/rab`

**Headers:**

```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Request Body:**

```json
{
    "name": "RAB Operasional",
    "description": "Deskripsi RAB",
    "created_at": "2025-11-24",
    "components": ["Komponen 1", "Komponen 2"],
    "budget_total": 10000000,
    "approved_value": 9000000,
    "status": "DRAFT",
    "has_product": false,
    "spends": [
        {
            "desc": "Termin 1",
            "amount": 3000000
        }
    ],
    "details": [
        {
            "nama_komponen": "Komponen 1",
            "jumlah": 10,
            "satuan": "pcs",
            "harga_satuan": 500000,
            "budget": 5000000,
            "nilai_disetujui": 4500000,
            "realisasi_pemakaian": 0,
            "disetujui": false,
            "deskripsi": "Deskripsi komponen"
        }
    ]
}
```

**Validation Rules:**
| Field | Type | Required | Rules |
|-------|------|----------|-------|
| name | string | Yes | max:255 |
| description | string | No | - |
| created_at | date | Yes | valid date |
| components | array | No | - |
| budget_total | numeric | Yes | min:0 |
| approved_value | numeric | No | min:0 |
| status | string | Yes | DRAFT, APPROVED_ALL, APPROVED_WITH_REV, TRANSFERRED, REJECTED |
| has_product | boolean | No | - |
| spends | array | No | - |
| details | array | No | - |

**Success Response:**

```json
{
    "success": true,
    "data": {
        "id_rab": 1,
        "nama_template": "RAB Operasional",
        "deskripsi": "Deskripsi RAB",
        "total_biaya": 10000000,
        "is_active": true,
        "created_at": "2025-11-24T00:00:00.000000Z",
        "updated_at": "2025-11-24T00:00:00.000000Z"
    },
    "message": "RAB berhasil dibuat"
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "name": ["The name field is required."],
        "budget_total": ["The budget total must be at least 0."]
    }
}
```

---

### 4. Update RAB

Memperbarui RAB yang sudah ada.

**Endpoint:** `PUT /admin/finance/rab/{id}`

**Headers:**

```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Request Body:** (sama dengan Create RAB)

**Success Response:**

```json
{
    "success": true,
    "data": {
        "id_rab": 1,
        "nama_template": "RAB Operasional Updated",
        "deskripsi": "Deskripsi RAB Updated",
        "total_biaya": 12000000,
        "is_active": true,
        "created_at": "2025-11-24T00:00:00.000000Z",
        "updated_at": "2025-11-24T10:00:00.000000Z"
    },
    "message": "RAB berhasil diperbarui"
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "RAB tidak ditemukan"
}
```

---

### 5. Delete RAB

Menghapus RAB.

**Endpoint:** `DELETE /admin/finance/rab/{id}`

**Headers:**

```
X-CSRF-TOKEN: {csrf_token}
```

**Success Response:**

```json
{
    "success": true,
    "message": "RAB berhasil dihapus"
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "RAB tidak ditemukan"
}
```

---

## Status Codes

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 404  | Not Found             |
| 422  | Validation Error      |
| 500  | Internal Server Error |

## RAB Status Values

| Value             | Label                   | Description                                  |
| ----------------- | ----------------------- | -------------------------------------------- |
| DRAFT             | Draft                   | RAB masih draft, belum ada persetujuan       |
| APPROVED_ALL      | Disetujui Semua         | Semua komponen disetujui dengan budget penuh |
| APPROVED_WITH_REV | Disetujui dengan Revisi | Disetujui tapi dengan perubahan budget       |
| TRANSFERRED       | Ditransfer              | Dana sudah ditransfer                        |
| REJECTED          | Ditolak                 | RAB ditolak                                  |

## Data Models

### RAB Template

```typescript
interface RabTemplate {
    id: number;
    created_at: string;
    name: string;
    description: string;
    components: string[];
    budget_total: number;
    approved_value: number;
    spends: Spend[];
    status: RabStatus;
    has_product: boolean;
    details: RabDetail[];
}
```

### RAB Detail

```typescript
interface RabDetail {
    id: number;
    nama_komponen: string;
    jumlah: number;
    satuan: string;
    harga_satuan: number;
    budget: number;
    nilai_disetujui: number;
    realisasi_pemakaian: number;
    disetujui: boolean;
    deskripsi: string;
}
```

### Spend

```typescript
interface Spend {
    desc: string;
    amount: number;
}
```

### RAB Status

```typescript
type RabStatus =
    | "DRAFT"
    | "APPROVED_ALL"
    | "APPROVED_WITH_REV"
    | "TRANSFERRED"
    | "REJECTED";
```

## Example Usage

### JavaScript/Fetch

```javascript
// Get RAB Data
async function getRabData() {
    const response = await fetch("/admin/finance/rab/data");
    const result = await response.json();
    return result.data;
}

// Create RAB
async function createRab(data) {
    const response = await fetch("/admin/finance/rab", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify(data),
    });
    return await response.json();
}

// Update RAB
async function updateRab(id, data) {
    const response = await fetch(`/admin/finance/rab/${id}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify(data),
    });
    return await response.json();
}

// Delete RAB
async function deleteRab(id) {
    const response = await fetch(`/admin/finance/rab/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
    });
    return await response.json();
}
```

### jQuery/Ajax

```javascript
// Get RAB Data
$.ajax({
    url: "/admin/finance/rab/data",
    method: "GET",
    success: function (result) {
        console.log(result.data);
    },
});

// Create RAB
$.ajax({
    url: "/admin/finance/rab",
    method: "POST",
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    contentType: "application/json",
    data: JSON.stringify(rabData),
    success: function (result) {
        console.log(result.message);
    },
});
```

### Axios

```javascript
// Get RAB Data
const response = await axios.get("/admin/finance/rab/data");
console.log(response.data.data);

// Create RAB
const response = await axios.post("/admin/finance/rab", rabData, {
    headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
    },
});
console.log(response.data.message);
```

## Notes

1. **CSRF Token**: Semua request POST, PUT, DELETE memerlukan CSRF token. Token bisa didapat dari:

    - Meta tag: `<meta name="csrf-token" content="{{ csrf_token() }}">`
    - Hidden input: `<input type="hidden" name="_token" value="{{ csrf_token() }}">`

2. **Authentication**: User harus login untuk mengakses semua endpoint.

3. **Validation**: Backend akan memvalidasi semua input. Jika validasi gagal, akan return status 422 dengan detail error.

4. **Error Handling**: Selalu cek `success` field di response untuk mengetahui apakah request berhasil atau gagal.

5. **Date Format**: Gunakan format `YYYY-MM-DD` untuk field tanggal.

6. **Number Format**: Gunakan number tanpa separator (contoh: 10000000, bukan 10.000.000).

7. **Components vs Details**:

    - `components`: Array sederhana berisi nama komponen (untuk UI sederhana)
    - `details`: Array object lengkap dengan semua informasi komponen (untuk data lengkap)

8. **Spends**: Digunakan untuk tracking realisasi pemakaian budget.

## Support

Jika ada pertanyaan atau issue, silakan hubungi tim development atau buat issue di repository.
