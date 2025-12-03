# Design Document - Integrasi Aktiva Tetap dengan Jurnal Akuntansi

## Overview

Desain ini mengintegrasikan modul Aktiva Tetap ke dalam sistem ERP baru dengan menambahkan fungsi-fungsi ke `FinanceAccountantController.php`. Semua transaksi aktiva tetap (perolehan, penyusutan, pelepasan) akan otomatis mencatat jurnal akuntansi menggunakan model `JournalEntry` dan `JournalEntryDetail` yang sudah ada. Frontend akan menggunakan view yang sudah tersedia di `resources/views/admin/finance/aktiva-tetap/index.blade.php` dengan Alpine.js.

## Architecture

### Controller Structure

```
FinanceAccountantController.php
├── Fixed Assets Management
│   ├── fixedAssetsData()          - Get all fixed assets with stats
│   ├── storeFixedAsset()          - Create new asset + journal entry
│   ├── updateFixedAsset()         - Update asset data
│   ├── deleteFixedAsset()         - Delete asset (with validation)
│   ├── toggleFixedAsset()         - Activate/deactivate asset
│   ├── showFixedAsset()           - Get asset detail
│   └── generateAssetCode()        - Generate unique asset code
│
├── Depreciation Management
│   ├── calculateDepreciation()    - Calculate depreciation for period
│   ├── batchDepreciation()        - Batch process all active assets
│   ├── postDepreciation()         - Post depreciation + create journal
│   ├── reverseDepreciation()      - Reverse posted depreciation
│   └── depreciationHistoryData()  - Get depreciation history
│
├── Asset Disposal
│   ├── disposeAsset()             - Record asset disposal + journal
│   └── calculateDisposalGainLoss() - Calculate gain/loss on disposal
│
└── Reporting
    ├── fixedAssetsStats()         - Get statistics for dashboard
    ├── assetValueChartData()      - Get data for value chart
    └── assetDistributionData()    - Get data for distribution chart
```

### Database Schema Updates

#### Table: fixed_assets (new table)

```sql
CREATE TABLE fixed_assets (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    category ENUM('land', 'building', 'vehicle', 'equipment', 'furniture', 'computer') NOT NULL,
    location VARCHAR(255),

    -- Acquisition Information
    acquisition_date DATE NOT NULL,
    acquisition_cost DECIMAL(15,2) NOT NULL,
    salvage_value DECIMAL(15,2) DEFAULT 0,
    useful_life INT NOT NULL, -- in years

    -- Depreciation Configuration
    depreciation_method ENUM('straight_line', 'declining_balance', 'double_declining', 'units_of_production') DEFAULT 'straight_line',

    -- Account Mapping
    asset_account_id BIGINT UNSIGNED NOT NULL,
    depreciation_expense_account_id BIGINT UNSIGNED NOT NULL,
    accumulated_depreciation_account_id BIGINT UNSIGNED NOT NULL,
    payment_account_id BIGINT UNSIGNED NOT NULL,

    -- Current Status
    accumulated_depreciation DECIMAL(15,2) DEFAULT 0,
    book_value DECIMAL(15,2) NOT NULL,
    status ENUM('active', 'inactive', 'sold', 'disposed') DEFAULT 'active',

    -- Disposal Information
    disposal_date DATE NULL,
    disposal_value DECIMAL(15,2) NULL,
    disposal_notes TEXT NULL,

    -- Metadata
    description TEXT,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (outlet_id) REFERENCES outlets(id_outlet),
    FOREIGN KEY (asset_account_id) REFERENCES chart_of_accounts(id),
    FOREIGN KEY (depreciation_expense_account_id) REFERENCES chart_of_accounts(id),
    FOREIGN KEY (accumulated_depreciation_account_id) REFERENCES chart_of_accounts(id),
    FOREIGN KEY (payment_account_id) REFERENCES chart_of_accounts(id),

    INDEX idx_outlet_status (outlet_id, status),
    INDEX idx_category (category),
    INDEX idx_acquisition_date (acquisition_date)
);
```

#### Table: fixed_asset_depreciations (new table)

```sql
CREATE TABLE fixed_asset_depreciations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    fixed_asset_id BIGINT UNSIGNED NOT NULL,

    -- Period Information
    period INT NOT NULL, -- sequential period number
    depreciation_date DATE NOT NULL,

    -- Depreciation Values
    amount DECIMAL(15,2) NOT NULL,
    accumulated_depreciation DECIMAL(15,2) NOT NULL,
    book_value DECIMAL(15,2) NOT NULL,

    -- Journal Integration
    journal_entry_id BIGINT UNSIGNED NULL,
    status ENUM('draft', 'posted', 'reversed') DEFAULT 'draft',

    -- Metadata
    notes TEXT NULL,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (fixed_asset_id) REFERENCES fixed_assets(id) ON DELETE CASCADE,
    FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id),

    UNIQUE KEY unique_asset_period (fixed_asset_id, period),
    INDEX idx_date_status (depreciation_date, status)
);
```

## Components and Interfaces

### 1. Fixed Asset Model (app/Models/FixedAsset.php)

```php
class FixedAsset extends Model
{
    protected $fillable = [
        'outlet_id', 'code', 'name', 'category', 'location',
        'acquisition_date', 'acquisition_cost', 'salvage_value', 'useful_life',
        'depreciation_method',
        'asset_account_id', 'depreciation_expense_account_id',
        'accumulated_depreciation_account_id', 'payment_account_id',
        'accumulated_depreciation', 'book_value', 'status',
        'disposal_date', 'disposal_value', 'disposal_notes',
        'description', 'created_by'
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'disposal_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
        'disposal_value' => 'decimal:2',
    ];

    // Relationships
    public function outlet() { return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet'); }
    public function assetAccount() { return $this->belongsTo(ChartOfAccount::class, 'asset_account_id'); }
    public function depreciationExpenseAccount() { return $this->belongsTo(ChartOfAccount::class, 'depreciation_expense_account_id'); }
    public function accumulatedDepreciationAccount() { return $this->belongsTo(ChartOfAccount::class, 'accumulated_depreciation_account_id'); }
    public function paymentAccount() { return $this->belongsTo(ChartOfAccount::class, 'payment_account_id'); }
    public function depreciations() { return $this->hasMany(FixedAssetDepreciation::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    // Scopes
    public function scopeByOutlet($query, $outletId) { return $query->where('outlet_id', $outletId); }
    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeByCategory($query, $category) { return $query->where('category', $category); }

    // Methods
    public function calculateMonthlyDepreciation(): float;
    public function calculateRemainingLife(): float;
    public function canBeDeleted(): bool;
    public function updateBookValue(): void;

    // Static Methods
    public static function generateCode($outletId): string;
}
```

### 2. Fixed Asset Depreciation Model (app/Models/FixedAssetDepreciation.php)

```php
class FixedAssetDepreciation extends Model
{
    protected $fillable = [
        'fixed_asset_id', 'period', 'depreciation_date',
        'amount', 'accumulated_depreciation', 'book_value',
        'journal_entry_id', 'status', 'notes', 'created_by'
    ];

    protected $casts = [
        'depreciation_date' => 'date',
        'amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
    ];

    // Relationships
    public function fixedAsset() { return $this->belongsTo(FixedAsset::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    // Scopes
    public function scopePosted($query) { return $query->where('status', 'posted'); }
    public function scopeDraft($query) { return $query->where('status', 'draft'); }
    public function scopeByPeriod($query, $startDate, $endDate);

    // Methods
    public function canBePosted(): bool;
    public function canBeReversed(): bool;
}
```

### 3. Controller Methods Detail

#### A. fixedAssetsData(Request $request): JsonResponse

**Purpose**: Get paginated list of fixed assets with statistics

**Request Parameters**:

-   `outlet_id` (optional): Filter by outlet
-   `category` (optional): Filter by category
-   `status` (optional): Filter by status
-   `search` (optional): Search by code or name
-   `per_page` (optional): Items per page

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "AST-001",
            "name": "Gedung Kantor",
            "category": "building",
            "location": "Jakarta",
            "acquisition_date": "2020-01-15",
            "acquisition_cost": 500000000,
            "salvage_value": 50000000,
            "useful_life": 20,
            "depreciation_method": "straight_line",
            "accumulated_depreciation": 50000000,
            "book_value": 450000000,
            "status": "active",
            "monthly_depreciation": 1875000,
            "remaining_life": 16,
            "depreciation_progress": 25
        }
    ],
    "stats": {
        "totalAssets": 24,
        "activeAssets": 22,
        "totalAcquisitionCost": 1250000000,
        "totalDepreciation": 250000000,
        "totalBookValue": 1000000000,
        "depreciationRate": 20
    },
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 24,
        "last_page": 3
    }
}
```

#### B. storeFixedAsset(Request $request): JsonResponse

**Purpose**: Create new fixed asset and automatically create acquisition journal entry

**Validation Rules**:

```php
[
    'outlet_id' => 'required|exists:outlets,id_outlet',
    'code' => 'required|string|max:50|unique:fixed_assets,code',
    'name' => 'required|string|max:255',
    'category' => 'required|in:land,building,vehicle,equipment,furniture,computer',
    'location' => 'nullable|string|max:255',
    'acquisition_date' => 'required|date|before_or_equal:today',
    'acquisition_cost' => 'required|numeric|min:0',
    'salvage_value' => 'required|numeric|min:0|lt:acquisition_cost',
    'useful_life' => 'required|integer|min:1',
    'depreciation_method' => 'required|in:straight_line,declining_balance,double_declining,units_of_production',
    'asset_account_id' => 'required|exists:chart_of_accounts,id',
    'depreciation_expense_account_id' => 'required|exists:chart_of_accounts,id',
    'accumulated_depreciation_account_id' => 'required|exists:chart_of_accounts,id',
    'payment_account_id' => 'required|exists:chart_of_accounts,id',
    'description' => 'nullable|string'
]
```

**Process Flow**:

1. Validate input data
2. Validate account types match requirements
3. Create FixedAsset record with book_value = acquisition_cost
4. Create JournalEntry for acquisition:
    - Debit: asset_account_id (acquisition_cost)
    - Credit: payment_account_id (acquisition_cost)
5. Post journal entry automatically
6. Update account balances
7. Return success response

**Journal Entry Format**:

```
Transaction Number: FA-ACQ-{asset_code}
Date: {acquisition_date}
Description: Perolehan Aktiva Tetap - {asset_name}
Reference Type: fixed_asset_acquisition
Reference Number: {asset_code}

Details:
- Debit: {asset_account} = {acquisition_cost}
- Credit: {payment_account} = {acquisition_cost}
```

#### C. calculateDepreciation(Request $request): JsonResponse

**Purpose**: Calculate and create depreciation entries for a specific period

**Request Parameters**:

-   `outlet_id` (required): Outlet ID
-   `period_month` (required): Month (1-12)
-   `period_year` (required): Year (YYYY)
-   `asset_ids` (optional): Array of specific asset IDs, or all if empty

**Process Flow**:

1. Get all active fixed assets for outlet
2. Filter assets that need depreciation (book_value > salvage_value)
3. For each asset:
    - Calculate depreciation amount based on method
    - Check if depreciation for period already exists
    - Create FixedAssetDepreciation record with status 'draft'
    - Calculate new accumulated_depreciation and book_value
4. Return summary of created depreciation entries

**Depreciation Calculation Methods**:

**Straight Line**:

```php
monthly_depreciation = (acquisition_cost - salvage_value) / useful_life / 12
```

**Declining Balance (150%)**:

```php
rate = 1.5 / useful_life
monthly_depreciation = book_value * rate / 12
// Stop when book_value <= salvage_value
```

**Double Declining Balance (200%)**:

```php
rate = 2 / useful_life
monthly_depreciation = book_value * rate / 12
// Stop when book_value <= salvage_value
```

#### D. postDepreciation($id): JsonResponse

**Purpose**: Post a draft depreciation entry and create journal entry

**Process Flow**:

1. Find FixedAssetDepreciation by ID
2. Validate status is 'draft'
3. Get related FixedAsset
4. Create JournalEntry:
    - Debit: depreciation_expense_account_id (amount)
    - Credit: accumulated_depreciation_account_id (amount)
5. Update FixedAssetDepreciation:
    - Set journal_entry_id
    - Set status to 'posted'
6. Update FixedAsset:
    - Increment accumulated_depreciation
    - Decrement book_value
7. Post journal entry
8. Update account balances

**Journal Entry Format**:

```
Transaction Number: FA-DEP-{asset_code}-{period}
Date: {depreciation_date}
Description: Penyusutan Aktiva Tetap - {asset_name} - Periode {period}
Reference Type: fixed_asset_depreciation
Reference Number: {asset_code}-{period}

Details:
- Debit: {depreciation_expense_account} = {amount}
- Credit: {accumulated_depreciation_account} = {amount}
```

#### E. batchDepreciation(Request $request): JsonResponse

**Purpose**: Batch process depreciation calculation and posting for all active assets

**Request Parameters**:

-   `outlet_id` (required): Outlet ID
-   `period_month` (required): Month (1-12)
-   `period_year` (required): Year (YYYY)
-   `auto_post` (optional): Boolean, auto post after calculation

**Process Flow**:

1. Calculate depreciation for all active assets (call calculateDepreciation)
2. If auto_post = true:
    - Get all draft depreciations for the period
    - Post each depreciation (call postDepreciation)
3. Return summary:
    - Total assets processed
    - Total depreciation amount
    - Total journal entries created
    - List of errors if any

#### F. disposeAsset(Request $request, $id): JsonResponse

**Purpose**: Record asset disposal and create journal entries

**Request Parameters**:

-   `disposal_date` (required): Date of disposal
-   `disposal_value` (required): Sale/disposal value
-   `disposal_notes` (optional): Notes about disposal

**Process Flow**:

1. Find FixedAsset by ID
2. Validate asset is active
3. Calculate gain/loss:
    ```php
    gain_loss = disposal_value - book_value
    ```
4. Create JournalEntry for disposal:
    - Debit: payment_account_id (disposal_value)
    - Debit: accumulated_depreciation_account_id (accumulated_depreciation)
    - Debit/Credit: gain_loss_account (if loss/gain)
    - Credit: asset_account_id (acquisition_cost)
5. Update FixedAsset:
    - Set status to 'sold' or 'disposed'
    - Set disposal_date, disposal_value, disposal_notes
6. Post journal entry
7. Update account balances

**Journal Entry Format (with Gain)**:

```
Transaction Number: FA-DSP-{asset_code}
Date: {disposal_date}
Description: Pelepasan Aktiva Tetap - {asset_name}
Reference Type: fixed_asset_disposal
Reference Number: {asset_code}

Details:
- Debit: {payment_account} = {disposal_value}
- Debit: {accumulated_depreciation_account} = {accumulated_depreciation}
- Credit: {asset_account} = {acquisition_cost}
- Credit: Gain on Disposal = {gain_loss} (if positive)
```

**Journal Entry Format (with Loss)**:

```
Details:
- Debit: {payment_account} = {disposal_value}
- Debit: {accumulated_depreciation_account} = {accumulated_depreciation}
- Debit: Loss on Disposal = {abs(gain_loss)} (if negative)
- Credit: {asset_account} = {acquisition_cost}
```

## Data Models

### FixedAsset Attributes

-   **id**: Primary key
-   **outlet_id**: Foreign key to outlets
-   **code**: Unique asset code (e.g., "AST-202401-001")
-   **name**: Asset name
-   **category**: Asset category (land, building, vehicle, etc.)
-   **location**: Physical location
-   **acquisition_date**: Date of acquisition
-   **acquisition_cost**: Original cost
-   **salvage_value**: Residual value at end of useful life
-   **useful_life**: Useful life in years
-   **depreciation_method**: Method of depreciation
-   **asset_account_id**: Chart of account for asset
-   **depreciation_expense_account_id**: Chart of account for depreciation expense
-   **accumulated_depreciation_account_id**: Chart of account for accumulated depreciation
-   **payment_account_id**: Chart of account for payment (cash/bank/payable)
-   **accumulated_depreciation**: Current accumulated depreciation
-   **book_value**: Current book value (acquisition_cost - accumulated_depreciation)
-   **status**: Current status (active, inactive, sold, disposed)
-   **disposal_date**: Date of disposal (if applicable)
-   **disposal_value**: Sale/disposal value (if applicable)
-   **disposal_notes**: Notes about disposal
-   **description**: Additional description
-   **created_by**: User who created the record

### FixedAssetDepreciation Attributes

-   **id**: Primary key
-   **fixed_asset_id**: Foreign key to fixed_assets
-   **period**: Sequential period number (1, 2, 3, ...)
-   **depreciation_date**: Date of depreciation
-   **amount**: Depreciation amount for this period
-   **accumulated_depreciation**: Total accumulated depreciation up to this period
-   **book_value**: Book value after this depreciation
-   **journal_entry_id**: Foreign key to journal_entries (null if not posted)
-   **status**: Status (draft, posted, reversed)
-   **notes**: Additional notes
-   **created_by**: User who created the record

## Error Handling

### Validation Errors

-   **Invalid Account Type**: "Akun aset harus memiliki tipe 'asset'"
-   **Invalid Salvage Value**: "Nilai residu tidak boleh lebih besar dari nilai perolehan"
-   **Invalid Useful Life**: "Masa manfaat minimal 1 tahun"
-   **Duplicate Asset Code**: "Kode aset sudah digunakan"
-   **Invalid Outlet**: "Outlet tidak ditemukan atau tidak aktif"

### Business Logic Errors

-   **Cannot Delete Posted Asset**: "Tidak dapat menghapus aset yang sudah memiliki jurnal terposting"
-   **Cannot Post Twice**: "Penyusutan sudah diposting sebelumnya"
-   **Cannot Reverse Draft**: "Hanya penyusutan yang sudah diposting yang dapat di-reverse"
-   **Asset Already Disposed**: "Aset sudah dilepas sebelumnya"
-   **Depreciation Exceeds Limit**: "Penyusutan tidak boleh melebihi nilai yang dapat disusutkan"

### Transaction Errors

-   **Journal Creation Failed**: "Gagal membuat jurnal: {error_message}"
-   **Account Balance Update Failed**: "Gagal memperbarui saldo akun: {error_message}"
-   **Database Transaction Failed**: "Transaksi database gagal, semua perubahan dibatalkan"

## Testing Strategy

### Unit Tests

1. **FixedAsset Model Tests**

    - Test calculateMonthlyDepreciation() for each method
    - Test calculateRemainingLife()
    - Test canBeDeleted() validation
    - Test generateCode() uniqueness

2. **FixedAssetDepreciation Model Tests**

    - Test canBePosted() validation
    - Test canBeReversed() validation
    - Test relationship integrity

3. **Controller Method Tests**
    - Test storeFixedAsset() with valid data
    - Test storeFixedAsset() with invalid account types
    - Test calculateDepreciation() for each method
    - Test postDepreciation() creates correct journal
    - Test disposeAsset() calculates gain/loss correctly

### Integration Tests

1. **Asset Acquisition Flow**

    - Create asset → Verify journal created → Verify account balances updated

2. **Depreciation Flow**

    - Calculate depreciation → Post depreciation → Verify journal → Verify balances

3. **Disposal Flow**

    - Dispose asset → Verify journal with gain/loss → Verify final balances

4. **Batch Processing**
    - Batch calculate → Batch post → Verify all journals created

### API Tests

1. Test all endpoints with valid data
2. Test all endpoints with invalid data
3. Test pagination and filtering
4. Test concurrent requests
5. Test error responses

### Frontend Tests

1. Test asset creation form validation
2. Test depreciation calculation display
3. Test batch processing UI
4. Test disposal form with gain/loss calculation
5. Test charts and statistics display

## Performance Considerations

1. **Batch Processing Optimization**

    - Process assets in chunks of 50
    - Use database transactions per chunk
    - Implement progress tracking

2. **Query Optimization**

    - Use eager loading for relationships
    - Index on outlet_id, status, category
    - Cache statistics for dashboard

3. **Journal Entry Creation**
    - Bulk insert journal entry details
    - Batch update account balances
    - Use database locks for concurrent access

## Security Considerations

1. **Authorization**

    - Verify user has permission to manage fixed assets
    - Verify user belongs to the outlet
    - Audit log all asset transactions

2. **Data Validation**

    - Sanitize all input data
    - Validate account ownership
    - Prevent SQL injection

3. **Transaction Integrity**
    - Use database transactions for all operations
    - Implement rollback on errors
    - Validate data consistency before commit
