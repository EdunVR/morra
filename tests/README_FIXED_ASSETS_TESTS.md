# Fixed Assets Integration Tests

This document describes the comprehensive test suite created for the Fixed Assets integration with the accounting journal system.

## Test Files Created

### 1. Unit Tests

#### `tests/Unit/FixedAssetDepreciationCalculationTest.php`

Tests the depreciation calculation logic for all supported methods:

-   Straight-line depreciation calculation
-   Declining balance (150%) depreciation calculation
-   Double declining balance (200%) depreciation calculation
-   Validation that depreciation doesn't exceed depreciable amount
-   Validation that depreciation stops at salvage value
-   Edge cases for fully depreciated assets

**Total Tests**: 7 test cases

### 2. Feature Tests

#### `tests/Feature/FixedAssetAcquisitionTest.php`

Tests the asset acquisition process and automatic journal entry creation:

-   Creating assets with valid data
-   Automatic journal entry creation on acquisition
-   Account balance updates after acquisition
-   Support for different asset categories (land, building, vehicle, equipment, furniture, computer)

**Total Tests**: 4 test cases

#### `tests/Feature/FixedAssetDepreciationPostingTest.php`

Tests the depreciation posting process and journal integration:

-   Journal entry creation when posting depreciation
-   Correct journal entry format (debit/credit structure)
-   Account balance updates after posting
-   Prevention of double posting
-   Depreciation status updates
-   Fixed asset accumulated depreciation updates

**Total Tests**: 6 test cases

#### `tests/Feature/FixedAssetDisposalTest.php`

Tests the asset disposal process with gain/loss calculations:

-   Disposal with gain (disposal value > book value)
-   Disposal with loss (disposal value < book value)
-   Correct journal entry format for disposal with gain
-   Correct journal entry format for disposal with loss
-   Account balance updates after disposal
-   Disposal at exact book value (no gain/loss)

**Total Tests**: 6 test cases

#### `tests/Feature/FixedAssetValidationTest.php`

Tests validation rules and error handling:

-   Invalid account type rejection
-   Salvage value validation (must be less than acquisition cost)
-   Prevention of deleting assets with posted journals
-   Prevention of posting already posted depreciation
-   Prevention of reversing draft depreciation
-   Useful life minimum validation
-   Acquisition date validation (not in future)
-   Inactive account rejection
-   Prevention of updating acquisition cost with existing depreciation

**Total Tests**: 9 test cases

#### `tests/Feature/FixedAssetBatchProcessingTest.php`

Tests batch processing functionality:

-   Batch calculation for multiple assets
-   Batch posting with auto_post flag
-   Error handling in batch processing
-   Correct summary results
-   Skipping inactive assets
-   Prevention of duplicate depreciation for same period

**Total Tests**: 6 test cases

#### `tests/Feature/FixedAssetFrontendIntegrationTest.php`

Tests frontend-backend API integration:

-   Loading fixed assets data from API
-   Loading depreciation history from API
-   Creating assets via form submission
-   Updating assets via form submission
-   Calculating depreciation via API
-   Posting depreciation via API
-   Disposing assets via form
-   Loading chart data for asset value
-   Loading chart data for asset distribution

**Total Tests**: 9 test cases

## Supporting Files Created

### Factory Files

#### `database/factories/OutletFactory.php`

Factory for creating test outlet data.

#### `database/factories/ChartOfAccountFactory.php`

Factory for creating test chart of account data.

## Test Coverage Summary

**Total Test Files**: 7
**Total Test Cases**: 47

### Coverage by Requirement

-   **Requirement 1 (Asset Acquisition)**: 4 tests
-   **Requirement 2 (Account Configuration)**: Covered in validation tests
-   **Requirement 3 (Depreciation Journal)**: 6 tests
-   **Requirement 4 (Depreciation Methods)**: 7 tests
-   **Requirement 5 (Asset Disposal)**: 6 tests
-   **Requirement 6 (Reporting)**: Covered in frontend integration tests
-   **Requirement 7 (Depreciation History)**: Covered in frontend integration tests
-   **Requirement 8 (Validation)**: 9 tests
-   **Requirement 9 (Multi-outlet)**: Covered across all tests
-   **Requirement 10 (Batch Processing)**: 6 tests

## Running the Tests

### Run All Fixed Asset Tests

```bash
php artisan test --testsuite=Feature --filter=FixedAsset
php artisan test --testsuite=Unit --filter=FixedAsset
```

### Run Specific Test Files

```bash
# Unit tests
php artisan test tests/Unit/FixedAssetDepreciationCalculationTest.php

# Feature tests
php artisan test tests/Feature/FixedAssetAcquisitionTest.php
php artisan test tests/Feature/FixedAssetDepreciationPostingTest.php
php artisan test tests/Feature/FixedAssetDisposalTest.php
php artisan test tests/Feature/FixedAssetValidationTest.php
php artisan test tests/Feature/FixedAssetBatchProcessingTest.php
php artisan test tests/Feature/FixedAssetFrontendIntegrationTest.php
```

### Run Specific Test Cases

```bash
php artisan test --filter="it_calculates_straight_line_depreciation_correctly"
php artisan test --filter="it_creates_journal_entry_on_asset_acquisition"
```

## Prerequisites

Before running the tests, ensure:

1. Database is properly configured in `.env` file
2. All migrations have been run:
    ```bash
    php artisan migrate:fresh
    ```
3. Test database is set up (or use in-memory SQLite for faster tests)

## Test Database Configuration

For faster test execution, consider using SQLite in-memory database for tests. Update `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Notes

-   All tests use `RefreshDatabase` trait to ensure clean state between tests
-   Tests create necessary test data using factories
-   Tests validate both successful operations and error conditions
-   Tests verify database state, journal entries, and account balances
-   Tests follow Laravel testing best practices

## Test Maintenance

When updating the Fixed Assets functionality:

1. Update corresponding test cases
2. Add new test cases for new features
3. Ensure all tests pass before deploying
4. Maintain test coverage above 80%

## Continuous Integration

These tests are designed to be run in CI/CD pipelines. Ensure your CI configuration includes:

```yaml
- php artisan test --coverage --min=80
```
