# Reusable Finance Components Usage Guide

This guide explains how to use the reusable export/import mixins and notification system created for the finance module.

## Overview

Three reusable components have been created:

1. **Export Mixin** - Common export functionality for XLSX and PDF
2. **Import Mixin** - Common import functionality with file validation and upload
3. **Notification System** - Toast notifications for success, error, info, and warning messages

## Files Created

-   `public/js/finance-components.js` - Contains all reusable Alpine.js mixins and notification system
-   `resources/views/components/notifications.blade.php` - Toast notification component
-   Updated `resources/views/components/layouts/admin.blade.php` - Includes the components

## 1. Export Mixin

### Usage

Spread the `financeExportMixin()` into your Alpine.js component:

```javascript
function myFinanceComponent() {
    return {
        // Spread the export mixin
        ...financeExportMixin(),

        // Your component data
        selectedOutlet: 1,
        filters: {
            date_from: "",
            date_to: "",
            status: "all",
        },

        // Your methods
        async init() {
            // Your initialization code
        },

        // Export methods are now available
        async handleExportXLSX() {
            const filters = {
                outlet_id: this.selectedOutlet,
                date_from: this.filters.date_from,
                date_to: this.filters.date_to,
                status: this.filters.status,
            };

            await this.exportToXLSX("journal", filters, "jurnal_export.xlsx");
        },

        async handleExportPDF() {
            const filters = {
                outlet_id: this.selectedOutlet,
                date_from: this.filters.date_from,
                date_to: this.filters.date_to,
            };

            await this.exportToPDF("journal", filters, "jurnal_export.pdf");
        },
    };
}
```

### Available Methods

#### `exportToXLSX(module, filters, filename)`

Exports data to XLSX format.

**Parameters:**

-   `module` (string) - Module name: 'journal', 'accounting-book', 'fixed-assets', 'general-ledger'
-   `filters` (object) - Filter parameters to apply (optional)
-   `filename` (string) - Custom filename (optional)

**Example:**

```javascript
await this.exportToXLSX(
    "journal",
    { outlet_id: 1, status: "posted" },
    "jurnal.xlsx"
);
```

#### `exportToPDF(module, filters, filename)`

Exports data to PDF format.

**Parameters:**

-   `module` (string) - Module name
-   `filters` (object) - Filter parameters to apply (optional)
-   `filename` (string) - Custom filename (optional)

**Example:**

```javascript
await this.exportToPDF(
    "fixed-assets",
    { category: "vehicle" },
    "aktiva_tetap.pdf"
);
```

### Available Properties

-   `isExporting` (boolean) - True when export is in progress
-   `exportError` (string|null) - Error message if export fails

### HTML Example

```html
<div x-data="myFinanceComponent()">
    <!-- Export Dropdown -->
    <div x-data="{ exportOpen: false }" class="relative">
        <button
            @click="exportOpen = !exportOpen"
            :disabled="isExporting"
            class="inline-flex items-center gap-2 rounded-xl border px-4 h-10"
        >
            <i
                class="bx"
                :class="isExporting ? 'bx-loader-alt animate-spin' : 'bx-export'"
            ></i>
            <span x-text="isExporting ? 'Mengekspor...' : 'Export'"></span>
        </button>

        <div
            x-show="exportOpen"
            @click.away="exportOpen = false"
            class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-xl"
        >
            <button
                @click="handleExportXLSX(); exportOpen = false"
                class="w-full px-4 py-2 text-left hover:bg-slate-50"
            >
                <i class="bx bx-file text-green-600"></i> Export ke XLSX
            </button>
            <button
                @click="handleExportPDF(); exportOpen = false"
                class="w-full px-4 py-2 text-left hover:bg-slate-50"
            >
                <i class="bx bxs-file-pdf text-red-600"></i> Export ke PDF
            </button>
        </div>
    </div>
</div>
```

## 2. Import Mixin

### Usage

Spread the `financeImportMixin()` into your Alpine.js component:

```javascript
function myFinanceComponent() {
    return {
        // Spread the import mixin
        ...financeImportMixin(),

        // Your component data
        selectedOutlet: 1,
        showImportModal: false,

        // Your methods
        async handleImport() {
            const additionalData = {
                outlet_id: this.selectedOutlet,
            };

            const success = await this.uploadFile("journal", additionalData);

            if (success) {
                // Reload data after successful import
                await this.loadJournals();
                this.showImportModal = false;
            }
        },
    };
}
```

### Available Methods

#### `handleFileSelect(event)`

Handles file input change event.

**Example:**

```html
<input
    type="file"
    @change="handleFileSelect($event)"
    accept=".xlsx,.xls,.csv"
/>
```

#### `handleDragOver(event)`, `handleDragLeave()`, `handleFileDrop(event)`

Handles drag-and-drop file upload.

**Example:**

```html
<div
    @dragover="handleDragOver($event)"
    @dragleave="handleDragLeave()"
    @drop="handleFileDrop($event)"
    :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-slate-300'"
>
    Drop file here
</div>
```

#### `uploadFile(module, additionalData)`

Uploads and imports the file.

**Parameters:**

-   `module` (string) - Module name: 'journal', 'fixed-assets'
-   `additionalData` (object) - Additional data to send (e.g., outlet_id)

**Returns:** Promise<boolean> - True if import succeeds

**Example:**

```javascript
const success = await this.uploadFile("journal", { outlet_id: 1 });
```

#### `clearImport()`

Clears import file and results.

#### `downloadTemplate(module)`

Downloads import template file.

**Example:**

```javascript
await this.downloadTemplate("journal");
```

### Available Properties

-   `isUploading` (boolean) - True when upload is in progress
-   `isDragging` (boolean) - True when file is being dragged over drop zone
-   `importFile` (File|null) - Selected file
-   `uploadProgress` (number) - Upload progress percentage (0-100)
-   `importResults` (object|null) - Import results with success/error counts
-   `importError` (string|null) - Error message if import fails

### HTML Example

```html
<div x-data="myFinanceComponent()">
    <!-- Import Modal -->
    <div
        x-show="showImportModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="bg-white rounded-2xl p-6 max-w-lg w-full">
            <h3 class="text-xl font-semibold mb-4">Import Data</h3>

            <!-- File Upload Area -->
            <div
                @dragover="handleDragOver($event)"
                @dragleave="handleDragLeave()"
                @drop="handleFileDrop($event)"
                :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-slate-300'"
                class="border-2 border-dashed rounded-xl p-8 text-center"
            >
                <template x-if="!importFile">
                    <div>
                        <i
                            class="bx bx-cloud-upload text-4xl text-slate-400 mb-2"
                        ></i>
                        <p class="text-sm text-slate-600 mb-2">
                            Drag & drop file atau
                        </p>
                        <label
                            class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg cursor-pointer hover:bg-blue-700"
                        >
                            Pilih File
                            <input
                                type="file"
                                @change="handleFileSelect($event)"
                                accept=".xlsx,.xls,.csv"
                                class="hidden"
                            />
                        </label>
                    </div>
                </template>

                <template x-if="importFile">
                    <div>
                        <i class="bx bx-file text-4xl text-green-600 mb-2"></i>
                        <p
                            class="text-sm font-medium"
                            x-text="importFile.name"
                        ></p>
                        <button
                            @click="clearImport()"
                            class="text-sm text-red-600 hover:underline mt-2"
                        >
                            Hapus File
                        </button>
                    </div>
                </template>
            </div>

            <!-- Upload Progress -->
            <div x-show="isUploading" class="mt-4">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span>Mengupload...</span>
                    <span x-text="uploadProgress + '%'"></span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2">
                    <div
                        class="bg-blue-600 h-2 rounded-full transition-all"
                        :style="`width: ${uploadProgress}%`"
                    ></div>
                </div>
            </div>

            <!-- Import Results -->
            <div
                x-show="importResults"
                class="mt-4 p-4 rounded-lg"
                :class="importResults?.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'"
            >
                <template x-if="importResults?.success">
                    <div>
                        <p class="text-sm font-medium text-green-800">
                            Import Berhasil!
                        </p>
                        <p class="text-sm text-green-700">
                            <span x-text="importResults.imported_count"></span>
                            data berhasil diimpor
                            <template x-if="importResults.skipped_count > 0">
                                ,
                                <span
                                    x-text="importResults.skipped_count"
                                ></span>
                                data dilewati
                            </template>
                        </p>
                    </div>
                </template>
                <template x-if="!importResults?.success">
                    <div>
                        <p class="text-sm font-medium text-red-800">
                            Import Gagal
                        </p>
                        <ul class="text-sm text-red-700 mt-2 space-y-1">
                            <template
                                x-for="error in importResults?.errors"
                                :key="error"
                            >
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <!-- Actions -->
            <div class="flex gap-2 mt-6">
                <button
                    @click="downloadTemplate('journal')"
                    class="px-4 py-2 border rounded-lg hover:bg-slate-50"
                >
                    <i class="bx bx-download"></i> Download Template
                </button>
                <div class="flex-1"></div>
                <button
                    @click="showImportModal = false"
                    class="px-4 py-2 border rounded-lg hover:bg-slate-50"
                >
                    Batal
                </button>
                <button
                    @click="handleImport()"
                    :disabled="!importFile || isUploading"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                >
                    Import
                </button>
            </div>
        </div>
    </div>
</div>
```

## 3. Notification System

### Usage

The notification system is globally available via `window.showNotification()`.

### Method

#### `showNotification(type, message, duration)`

Shows a toast notification.

**Parameters:**

-   `type` (string) - Notification type: 'success', 'error', 'info', 'warning'
-   `message` (string) - Notification message
-   `duration` (number) - Auto-dismiss duration in milliseconds (default: 5000, 0 = no auto-dismiss)

**Examples:**

```javascript
// Success notification
showNotification("success", "Data berhasil disimpan");

// Error notification
showNotification("error", "Gagal menyimpan data");

// Info notification
showNotification("info", "Proses sedang berjalan");

// Warning notification
showNotification("warning", "Data akan dihapus permanen");

// Custom duration (10 seconds)
showNotification("success", "Import selesai", 10000);

// No auto-dismiss
showNotification("error", "Kesalahan kritis", 0);
```

### Using in Alpine.js Components

The notification system is automatically available in all Alpine.js components through the mixins:

```javascript
function myComponent() {
    return {
        ...financeExportMixin(),

        async saveData() {
            try {
                // Save logic
                this.showNotification("success", "Data berhasil disimpan");
            } catch (error) {
                this.showNotification(
                    "error",
                    "Gagal menyimpan: " + error.message
                );
            }
        },
    };
}
```

### Alpine Store Access

You can also access the notification store directly:

```javascript
// Add notification
Alpine.store("notifications").add("success", "Message", 5000);

// Remove notification by ID
Alpine.store("notifications").remove(notificationId);

// Clear all notifications
Alpine.store("notifications").clear();
```

## Complete Example

Here's a complete example combining all three components:

```html
<div x-data="financePageComponent()" x-init="init()">
    <!-- Header with Export/Import buttons -->
    <div class="flex gap-2">
        <!-- Export Dropdown -->
        <div x-data="{ exportOpen: false }" class="relative">
            <button
                @click="exportOpen = !exportOpen"
                :disabled="isExporting"
                class="inline-flex items-center gap-2 rounded-xl border px-4 h-10"
            >
                <i
                    class="bx"
                    :class="isExporting ? 'bx-loader-alt animate-spin' : 'bx-export'"
                ></i>
                <span x-text="isExporting ? 'Mengekspor...' : 'Export'"></span>
            </button>
            <div
                x-show="exportOpen"
                @click.away="exportOpen = false"
                class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-xl"
            >
                <button
                    @click="handleExportXLSX(); exportOpen = false"
                    class="w-full px-4 py-2 text-left hover:bg-slate-50"
                >
                    Export ke XLSX
                </button>
                <button
                    @click="handleExportPDF(); exportOpen = false"
                    class="w-full px-4 py-2 text-left hover:bg-slate-50"
                >
                    Export ke PDF
                </button>
            </div>
        </div>

        <!-- Import Button -->
        <button
            @click="showImportModal = true"
            class="inline-flex items-center gap-2 rounded-xl border px-4 h-10"
        >
            <i class="bx bx-import"></i> Import
        </button>
    </div>

    <!-- Import Modal (see Import Mixin HTML example above) -->

    <!-- Your page content -->
</div>

<script>
    function financePageComponent() {
        return {
            // Spread both mixins
            ...financeExportMixin(),
            ...financeImportMixin(),

            // Component data
            selectedOutlet: 1,
            showImportModal: false,
            filters: {},

            async init() {
                await this.loadData();
            },

            async loadData() {
                // Load your data
            },

            async handleExportXLSX() {
                await this.exportToXLSX("journal", {
                    outlet_id: this.selectedOutlet,
                });
            },

            async handleExportPDF() {
                await this.exportToPDF("journal", {
                    outlet_id: this.selectedOutlet,
                });
            },

            async handleImport() {
                const success = await this.uploadFile("journal", {
                    outlet_id: this.selectedOutlet,
                });
                if (success) {
                    await this.loadData();
                    this.showImportModal = false;
                }
            },
        };
    }
</script>
```

## Benefits

1. **Code Reusability** - No need to duplicate export/import logic across pages
2. **Consistent UX** - All finance pages have the same export/import behavior
3. **Easy Maintenance** - Update once, applies everywhere
4. **Loading States** - Built-in loading indicators
5. **Error Handling** - Automatic error notifications
6. **File Validation** - Built-in file type and size validation
7. **Progress Tracking** - Upload progress indicator
8. **Notifications** - Consistent toast notifications across the app

## Notes

-   The notification component is automatically included in the admin layout
-   The finance-components.js file is loaded globally
-   All mixins use the global `showNotification()` function for user feedback
-   Export/Import endpoints must follow the pattern: `/finance/{module}/export/{format}` and `/finance/{module}/import`
