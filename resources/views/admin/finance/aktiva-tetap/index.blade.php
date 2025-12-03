{{-- resources/views/admin/finance/fixed-assets/index.blade.php --}}
<x-layouts.admin :title="'Aktiva Tetap'">
  <div x-data="fixedAssetsManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Aktiva Tetap</h1>
        <p class="text-slate-600 text-sm">Kelola aset tetap perusahaan dan perhitungan penyusutan</p>
      </div>

      <div class="flex flex-wrap gap-2">
        <button @click="openCreateAsset()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 h-10 hover:bg-emerald-700">
          <i class='bx bx-plus'></i> Tambah Aset
        </button>
        <button @click="calculateDepreciation()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700">
          <i class='bx bx-calculator'></i> Hitung Penyusutan
        </button>
        
        {{-- Export Dropdown --}}
        <div x-data="{ exportOpen: false }" class="relative">
          <button @click="exportOpen = !exportOpen" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
            <i class='bx bx-export'></i> Export
            <i class='bx bx-chevron-down text-sm'></i>
          </button>
          <div x-show="exportOpen" @click.away="exportOpen = false" 
               class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg z-10"
               style="display: none;">
            <button @click="exportToXLSX(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-t-xl">
              <i class='bx bx-file text-green-600'></i> Export ke XLSX
            </button>
            <button @click="exportToPDF(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-b-xl">
              <i class='bx bxs-file-pdf text-red-600'></i> Export ke PDF
            </button>
          </div>
        </div>

        <button @click="openImportModal()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
          <i class='bx bx-import'></i> Import
        </button>
      </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="flex flex-wrap gap-3 items-center">
        <div class="flex items-center gap-2">
          <i class='bx bx-filter text-slate-600'></i>
          <span class="text-sm font-medium text-slate-700">Filter:</span>
        </div>
        
        <select x-model="filters.outlet_id" @change="onOutletChange()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          <template x-for="outlet in outlets" :key="outlet.id_outlet">
            <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
          </template>
        </select>

        <select x-model="filters.book_id" @change="loadAssets()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          <template x-for="book in books" :key="book.id">
            <option :value="book.id" x-text="book.name"></option>
          </template>
        </select>

        <select x-model="filters.status" @change="loadAssets()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          <option value="all">Semua Status</option>
          <option value="active">Aktif</option>
          <option value="inactive">Tidak Aktif</option>
          <option value="disposed">Dijual/Dihapus</option>
        </select>

        <select x-model="filters.category" @change="loadAssets()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          <option value="all">Semua Kategori</option>
          <option value="building">Bangunan</option>
          <option value="vehicle">Kendaraan</option>
          <option value="equipment">Peralatan</option>
          <option value="furniture">Furniture</option>
          <option value="electronics">Elektronik</option>
          <option value="other">Lainnya</option>
        </select>

        <button @click="resetFilters()" 
                class="ml-auto inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm hover:bg-slate-50">
          <i class='bx bx-reset'></i> Reset Filter
        </button>
      </div>
    </div>

    {{-- Infografis Aktiva Tetap --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Asset Value Overview --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Nilai Aset Tetap</h3>
          <select x-model="chartPeriod" @change="reloadCharts()" class="rounded-lg border border-slate-200 px-3 py-1 text-sm">
            <option value="current">Tahun Berjalan</option>
            <option value="all">Semua Tahun</option>
          </select>
        </div>
        <div class="h-64">
          <canvas id="assetValueChart" x-ref="assetValueChart"></canvas>
        </div>
      </div>

      {{-- Asset Distribution --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Distribusi Aset per Kategori</h3>
          <span class="text-sm text-slate-500" x-text="assetStats.totalAssets + ' aset'"></span>
        </div>
        <div class="h-64">
          <canvas id="assetDistributionChart" x-ref="assetDistributionChart"></canvas>
        </div>
      </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-building-house text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="assetStats.totalAssets"></div>
            <div class="text-sm text-slate-600">Total Aset</div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-1 text-xs">
          <i class='bx bx-check-circle text-green-500'></i>
          <span class="text-green-600" x-text="assetStats.activeAssets"></span>
          <span class="text-slate-500">aktif</span>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-wallet text-2xl text-green-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="formatCurrency(assetStats.totalAcquisitionCost)"></div>
            <div class="text-sm text-slate-600">Nilai Perolehan</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          Seluruh aset
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
            <i class='bx bx-trending-down text-2xl text-red-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="formatCurrency(assetStats.totalDepreciation)"></div>
            <div class="text-sm text-slate-600">Akumulasi Penyusutan</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          <span x-text="assetStats.depreciationRate + '%'"></span> dari nilai perolehan
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
            <i class='bx bx-line-chart text-2xl text-purple-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="formatCurrency(assetStats.totalBookValue)"></div>
            <div class="text-sm text-slate-600">Nilai Buku</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          Nilai saat ini
        </div>
      </div>
    </div>

    {{-- Upcoming Depreciations --}}
    <div class="rounded-2xl border border-orange-200 bg-orange-50 p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <i class='bx bx-calendar-exclamation text-orange-600 text-xl'></i>
          <h3 class="text-lg font-semibold text-orange-800">Penyusutan Mendatang</h3>
        </div>
        <span class="text-orange-700 font-medium" x-text="upcomingDepreciations.length + ' aset'"></span>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="asset in upcomingDepreciations" :key="asset.id">
          <div class="bg-white rounded-lg border border-orange-200 p-4">
            <div class="flex items-start justify-between mb-2">
              <div>
                <div class="font-semibold text-slate-800" x-text="asset.name"></div>
                <div class="text-sm text-slate-500" x-text="asset.category"></div>
              </div>
              <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs" x-text="asset.depreciation_method"></span>
            </div>
            <div class="space-y-1 text-sm">
              <div class="flex justify-between">
                <span class="text-slate-500">Jatuh Tempo:</span>
                <span class="font-medium" x-text="asset.next_depreciation_date"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Nilai Penyusutan:</span>
                <span class="font-medium text-orange-600" x-text="formatCurrency(asset.monthly_depreciation)"></span>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    {{-- Assets Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <template x-for="asset in assetsData" :key="asset.id">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card hover:shadow-lg transition-shadow">
          {{-- Asset Header --}}
          <div class="flex items-start justify-between mb-4">
            <div>
              <div class="font-semibold text-slate-800 text-lg" x-text="asset.name"></div>
              <div class="text-sm text-slate-600" x-text="asset.category"></div>
            </div>
            <div>
              <span :class="getStatusBadgeClass(asset.status)" x-text="getStatusName(asset.status)" 
                    class="px-2 py-1 rounded-full text-xs"></span>
            </div>
          </div>

          {{-- Asset Details --}}
          <div class="space-y-3 mb-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-slate-500">Kode Aset</span>
              <span class="text-sm font-mono font-medium" x-text="asset.code"></span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-slate-500">Tanggal Perolehan</span>
              <span class="text-sm font-medium" x-text="asset.acquisition_date"></span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-slate-500">Masa Manfaat</span>
              <span class="text-sm font-medium" x-text="asset.useful_life + ' tahun'"></span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-slate-500">Metode Penyusutan</span>
              <span class="text-sm font-medium" x-text="asset.depreciation_method"></span>
            </div>
          </div>

          {{-- Financial Information --}}
          <div class="border-t border-slate-200 pt-4 space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm text-slate-500">Nilai Perolehan</span>
              <span class="text-sm font-semibold" x-text="formatCurrency(asset.acquisition_cost)"></span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-slate-500">Akumulasi Penyusutan</span>
              <span class="text-sm font-semibold text-red-600" x-text="formatCurrency(asset.accumulated_depreciation)"></span>
            </div>
            <div class="flex justify-between items-center border-t border-slate-100 pt-2">
              <span class="text-sm font-medium text-slate-700">Nilai Buku</span>
              <span class="text-lg font-bold text-blue-600" x-text="formatCurrency(asset.book_value)"></span>
            </div>
          </div>

          {{-- Progress Bar --}}
          <div class="mt-3">
            <div class="flex justify-between text-xs text-slate-500 mb-1">
              <span>Tersisa</span>
              <span x-text="asset.remaining_life + ' tahun'"></span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
              <div class="bg-green-600 h-2 rounded-full" :style="'width: ' + asset.depreciation_progress + '%'"></div>
            </div>
          </div>

          {{-- Actions --}}
          <div class="flex justify-between items-center mt-4">
            <div class="text-xs text-slate-500">
              <span x-text="asset.location"></span>
            </div>
            <div class="flex items-center gap-2">
              <button @click="viewAsset(asset.id)" class="text-blue-600 hover:text-blue-800" title="Lihat">
                <i class="bx bx-show"></i>
              </button>
              <button @click="editAsset(asset.id)" class="text-green-600 hover:text-green-800" title="Edit">
                <i class="bx bx-edit"></i>
              </button>
              <button @click="calculateAssetDepreciation(asset.id)" class="text-purple-600 hover:text-purple-800" title="Hitung Penyusutan">
                <i class="bx bx-calculator"></i>
              </button>
              <button @click="openDisposalModal(asset)" x-show="asset.status === 'active'" class="text-red-600 hover:text-red-800" title="Lepas Aset">
                <i class="bx bx-trash"></i>
              </button>
              <button @click="toggleAsset(asset.id, asset.status)" 
                      :class="asset.status === 'active' ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800'"
                      :title="asset.status === 'active' ? 'Nonaktifkan' : 'Aktifkan'">
                <i :class="asset.status === 'active' ? 'bx bx-power-off' : 'bx bx-check-circle'"></i>
              </button>
            </div>
          </div>
        </div>
      </template>
    </div>

    {{-- Depreciation History Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Riwayat Penyusutan</h2>
        <div class="flex flex-wrap gap-2">
          <select x-model="depreciationFilters.asset_id" @change="loadDepreciationHistory()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="all" selected>Semua Aset</option>
            <template x-for="asset in assetsData" :key="asset.id">
              <option :value="asset.id" x-text="asset.code + ' - ' + asset.name"></option>
            </template>
          </select>
          <input type="month" x-model="depreciationFilters.month" @change="loadDepreciationHistory()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Pilih bulan atau kosongkan untuk semua">
          <button @click="exportDepreciation()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
            <i class='bx bx-export'></i> Export
          </button>
        </div>
      </div>

      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left w-12">No</th>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3 text-left">Kode Aset</th>
            <th class="px-4 py-3 text-left">Nama Aset</th>
            <th class="px-4 py-3 text-right">Penyusutan</th>
            <th class="px-4 py-3 text-right">Akumulasi</th>
            <th class="px-4 py-3 text-right">Nilai Buku</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left w-24">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="(depreciation, index) in depreciationHistory" :key="depreciation.id">
            <tr class="border-t border-slate-100 hover:bg-slate-50">
              <td class="px-4 py-3" x-text="index + 1"></td>
              <td class="px-4 py-3">
                <div class="font-medium" x-text="depreciation.date"></div>
              </td>
              <td class="px-4 py-3">
                <div class="font-mono text-sm" x-text="depreciation.asset_code"></div>
              </td>
              <td class="px-4 py-3">
                <div class="font-medium text-slate-800" x-text="depreciation.asset_name"></div>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="font-semibold text-red-600" x-text="formatCurrency(depreciation.amount)"></div>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="font-semibold text-orange-600" x-text="formatCurrency(depreciation.accumulated)"></div>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="font-semibold text-blue-600" x-text="formatCurrency(depreciation.book_value)"></div>
              </td>
              <td class="px-4 py-3">
                <span :class="depreciation.status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" 
                      class="px-2 py-1 rounded-full text-xs" x-text="depreciation.status === 'posted' ? 'Diposting' : 'Draft'"></span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <button @click="postDepreciation(depreciation.id)" x-show="depreciation.status === 'draft'" 
                          class="text-green-600 hover:text-green-800" title="Posting">
                    <i class="bx bx-check"></i>
                  </button>
                  <button @click="reverseDepreciation(depreciation.id)" class="text-red-600 hover:text-red-800" title="Reverse">
                    <i class="bx bx-undo"></i>
                  </button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    {{-- Modal Disposal Asset --}}
    <div x-show="showDisposalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl w-full max-w-md">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800">Pelepasan Aset</h3>
          <p class="text-sm text-slate-600 mt-1" x-show="disposalAsset" x-text="disposalAsset ? disposalAsset.name : ''"></p>
        </div>
        
        <div class="p-6 space-y-4">
          <div x-show="disposalAsset" class="bg-slate-50 rounded-lg p-4 space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-slate-600">Nilai Perolehan:</span>
              <span class="font-semibold" x-text="disposalAsset ? formatCurrency(disposalAsset.acquisition_cost) : ''"></span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-600">Akumulasi Penyusutan:</span>
              <span class="font-semibold text-red-600" x-text="disposalAsset ? formatCurrency(disposalAsset.accumulated_depreciation) : ''"></span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-2">
              <span class="text-slate-700 font-medium">Nilai Buku Saat Ini:</span>
              <span class="font-bold text-blue-600" x-text="disposalAsset ? formatCurrency(disposalAsset.book_value) : ''"></span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pelepasan</label>
            <input type="date" x-model="disposalForm.disposal_date" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nilai Pelepasan</label>
            <input type="number" x-model="disposalForm.disposal_value" @input="calculateDisposalGainLoss()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="0">
          </div>

          <div x-show="disposalForm.disposal_value > 0" class="bg-blue-50 rounded-lg p-4">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium" x-text="calculateDisposalGainLoss() >= 0 ? 'Keuntungan Pelepasan:' : 'Kerugian Pelepasan:'"></span>
              <span class="font-bold" 
                    :class="calculateDisposalGainLoss() >= 0 ? 'text-green-600' : 'text-red-600'"
                    x-text="formatCurrency(Math.abs(calculateDisposalGainLoss()))"></span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
            <textarea x-model="disposalForm.disposal_notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" 
                      placeholder="Catatan pelepasan aset..."></textarea>
          </div>
        </div>

        <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
          <button @click="showDisposalModal = false" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
            Batal
          </button>
          <button @click="disposeAsset()" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
            Lepas Aset
          </button>
        </div>
      </div>
    </div>

    {{-- Modal View Asset (Read-Only) --}}
    <div x-show="showViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="showViewModal = false">
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-200 sticky top-0 bg-white">
          <h3 class="text-lg font-semibold text-slate-800">Detail Aktiva Tetap</h3>
        </div>
        
        <div class="p-6 space-y-6" x-show="viewingAsset">
          {{-- Basic Info --}}
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs text-slate-500">Kode Aset</label>
              <div class="font-semibold" x-text="viewingAsset?.code"></div>
            </div>
            <div>
              <label class="text-xs text-slate-500">Status</label>
              <div>
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="viewingAsset?.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                      x-text="viewingAsset?.status"></span>
              </div>
            </div>
            <div class="col-span-2">
              <label class="text-xs text-slate-500">Nama Aset</label>
              <div class="font-semibold text-lg" x-text="viewingAsset?.name"></div>
            </div>
            <div>
              <label class="text-xs text-slate-500">Kategori</label>
              <div x-text="viewingAsset?.category"></div>
            </div>
            <div>
              <label class="text-xs text-slate-500">Lokasi</label>
              <div x-text="viewingAsset?.location || '-'"></div>
            </div>
          </div>

          {{-- Acquisition Info --}}
          <div class="border-t pt-4">
            <h4 class="font-semibold mb-3">Informasi Perolehan</h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-xs text-slate-500">Tanggal Perolehan</label>
                <div x-text="viewingAsset?.acquisition_date"></div>
              </div>
              <div>
                <label class="text-xs text-slate-500">Nilai Perolehan</label>
                <div class="font-semibold text-blue-600" x-text="viewingAsset ? formatCurrency(viewingAsset.acquisition_cost) : ''"></div>
              </div>
              <div>
                <label class="text-xs text-slate-500">Nilai Residu</label>
                <div x-text="viewingAsset ? formatCurrency(viewingAsset.salvage_value) : ''"></div>
              </div>
              <div>
                <label class="text-xs text-slate-500">Masa Manfaat</label>
                <div x-text="viewingAsset ? viewingAsset.useful_life + ' tahun' : ''"></div>
              </div>
            </div>
          </div>

          {{-- Depreciation Info --}}
          <div class="border-t pt-4">
            <h4 class="font-semibold mb-3">Informasi Penyusutan</h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-xs text-slate-500">Metode Penyusutan</label>
                <div x-text="viewingAsset?.depreciation_method"></div>
              </div>
              <div>
                <label class="text-xs text-slate-500">Akumulasi Penyusutan</label>
                <div class="font-semibold text-red-600" x-text="viewingAsset ? formatCurrency(viewingAsset.accumulated_depreciation) : ''"></div>
              </div>
              <div class="col-span-2">
                <label class="text-xs text-slate-500">Nilai Buku Saat Ini</label>
                <div class="font-bold text-xl text-green-600" x-text="viewingAsset ? formatCurrency(viewingAsset.book_value) : ''"></div>
              </div>
            </div>
          </div>

          {{-- Description --}}
          <div class="border-t pt-4" x-show="viewingAsset?.description">
            <h4 class="font-semibold mb-2">Deskripsi</h4>
            <div class="text-sm text-slate-600" x-text="viewingAsset?.description"></div>
          </div>
        </div>

        <div class="p-6 border-t border-slate-200 flex justify-end">
          <button @click="showViewModal = false" class="px-4 py-2 bg-slate-600 text-white text-sm rounded-lg hover:bg-slate-700">
            Tutup
          </button>
        </div>
      </div>
    </div>

    {{-- Modal Calculate Depreciation --}}
    <div x-show="showDepreciationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl w-full max-w-md">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800">Hitung Penyusutan Batch</h3>
        </div>
        
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Bulan</label>
            <select x-model="depreciationForm.period_month" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="1">Januari</option>
              <option value="2">Februari</option>
              <option value="3">Maret</option>
              <option value="4">April</option>
              <option value="5">Mei</option>
              <option value="6">Juni</option>
              <option value="7">Juli</option>
              <option value="8">Agustus</option>
              <option value="9">September</option>
              <option value="10">Oktober</option>
              <option value="11">November</option>
              <option value="12">Desember</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun</label>
            <input type="number" x-model="depreciationForm.period_year" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" min="2020" max="2100">
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" x-model="depreciationForm.auto_post" id="autoPost" class="rounded border-slate-300">
            <label for="autoPost" class="text-sm text-slate-700">Posting otomatis setelah perhitungan</label>
          </div>

          <div x-show="depreciationProgress.show" class="bg-blue-50 rounded-lg p-4">
            <div class="text-sm text-blue-800 mb-2" x-text="depreciationProgress.message"></div>
            <div class="w-full bg-blue-200 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full transition-all" 
                   :style="'width: ' + (depreciationProgress.total > 0 ? (depreciationProgress.processed / depreciationProgress.total * 100) : 0) + '%'"></div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
          <button @click="showDepreciationModal = false" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
            Batal
          </button>
          <button @click="processBatchDepreciation()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
            Hitung Penyusutan
          </button>
        </div>
      </div>
    </div>

    {{-- Modal Create/Edit Asset --}}
    <div x-show="showAssetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800" x-text="editingAsset ? 'Edit Aset Tetap' : 'Tambah Aset Tetap'"></h3>
        </div>
        
        <div class="p-6 space-y-6">
          {{-- Basic Information --}}
          <div>
            <h4 class="font-semibold text-slate-800 mb-4">Informasi Dasar</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kode Aset</label>
                <input type="text" x-model="assetForm.code" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="AST-001">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Aset</label>
                <input type="text" x-model="assetForm.name" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Nama aset">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                <select x-model="assetForm.category" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  <option value="land">Tanah</option>
                  <option value="building">Bangunan</option>
                  <option value="vehicle">Kendaraan</option>
                  <option value="equipment">Peralatan</option>
                  <option value="furniture">Furniture</option>
                  <option value="computer">Komputer & IT</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Lokasi</label>
                <input type="text" x-model="assetForm.location" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Lokasi aset">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Outlet</label>
                <input type="text" 
                       :value="outlets.find(o => o.id_outlet == filters.outlet_id)?.nama_outlet || 'Outlet tidak ditemukan'" 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50" 
                       readonly>
                <p class="text-xs text-slate-500 mt-1">Outlet diambil dari filter yang dipilih</p>
              </div>
            </div>
          </div>

          {{-- Acquisition Information --}}
          <div>
            <h4 class="font-semibold text-slate-800 mb-4">Informasi Perolehan</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Perolehan</label>
                <input type="date" x-model="assetForm.acquisition_date" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nilai Perolehan</label>
                <input type="number" x-model="assetForm.acquisition_cost" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="0">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nilai Residu</label>
                <input type="number" x-model="assetForm.salvage_value" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="0">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Masa Manfaat (tahun)</label>
                <input type="number" x-model="assetForm.useful_life" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="0">
              </div>
            </div>
          </div>

          {{-- Depreciation Information --}}
          <div>
            <h4 class="font-semibold text-slate-800 mb-4">Informasi Penyusutan</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Metode Penyusutan</label>
                <select x-model="assetForm.depreciation_method" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  <option value="straight_line">Garis Lurus</option>
                  <option value="declining_balance">Saldo Menurun</option>
                  <option value="double_declining">Saldo Menurun Ganda</option>
                  <option value="units_of_production">Unit Produksi</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Akun Aset</label>
                <select x-model="assetForm.asset_account_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  <option value="">Pilih Akun Aset</option>
                  <template x-for="account in assetAccounts" :key="account.id">
                    <option :value="account.id" x-text="account.code + ' - ' + account.name"></option>
                  </template>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Akun Beban Penyusutan</label>
                <select x-model="assetForm.depreciation_expense_account_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  <option value="">Pilih Akun Beban Penyusutan</option>
                  <template x-for="account in depreciationAccounts" :key="account.id">
                    <option :value="account.id" x-text="account.code + ' - ' + account.name"></option>
                  </template>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Akun Akumulasi Penyusutan</label>
                <select x-model="assetForm.accumulated_depreciation_account_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  <option value="">Pilih Akun Akumulasi Penyusutan</option>
                  <template x-for="account in accumulatedDepreciationAccounts" :key="account.id">
                    <option :value="account.id" x-text="account.code + ' - ' + account.name"></option>
                  </template>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Akun Pembayaran (Kas/Bank) <span class="text-red-500">*</span></label>
                <select x-model="assetForm.payment_account_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                  <option value="">Pilih Akun Kas/Bank</option>
                  <template x-for="account in paymentAccounts" :key="account.id">
                    <option :value="account.id" x-text="account.code + ' - ' + account.name"></option>
                  </template>
                </select>
                <p class="text-xs text-slate-500 mt-1">Akun aset yang akan dikurangi saat pembelian</p>
              </div>
            </div>
          </div>

          {{-- Additional Information --}}
          <div>
            <h4 class="font-semibold text-slate-800 mb-4">Informasi Tambahan</h4>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
              <textarea x-model="assetForm.description" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" 
                        placeholder="Deskripsi aset..."></textarea>
            </div>
          </div>

          {{-- Depreciation Preview --}}
          <div x-show="assetForm.acquisition_cost && assetForm.useful_life" class="bg-slate-50 rounded-xl p-4">
            <h4 class="font-semibold text-slate-800 mb-3">Pratinjau Penyusutan</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
              <div>
                <div class="text-sm text-slate-600">Penyusutan Tahunan</div>
                <div class="text-lg font-bold text-blue-600" x-text="formatCurrency(calculateAnnualDepreciation())"></div>
              </div>
              <div>
                <div class="text-sm text-slate-600">Penyusutan Bulanan</div>
                <div class="text-lg font-bold text-green-600" x-text="formatCurrency(calculateMonthlyDepreciation())"></div>
              </div>
              <div>
                <div class="text-sm text-slate-600">Tingkat Penyusutan</div>
                <div class="text-lg font-bold text-purple-600" x-text="calculateDepreciationRate() + '%'"></div>
              </div>
              <div>
                <div class="text-sm text-slate-600">Nilai Buku Akhir</div>
                <div class="text-lg font-bold text-orange-600" x-text="formatCurrency(assetForm.salvage_value)"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
          <button @click="showAssetModal = false" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
            Batal
          </button>
          <button @click="saveAsset()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
            Simpan Aset
          </button>
        </div>
      </div>
    </div>

    {{-- Modal Import Assets --}}
    <div x-show="showImportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="showImportModal = false">
      <div class="bg-white rounded-2xl w-full max-w-2xl">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800">Import Aktiva Tetap</h3>
          <p class="text-sm text-slate-600 mt-1">Upload file Excel untuk mengimpor data aset tetap</p>
        </div>
        
        <div class="p-6 space-y-4">
          {{-- Download Template --}}
          <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="flex items-start gap-3">
              <i class='bx bx-info-circle text-blue-600 text-xl'></i>
              <div class="flex-1">
                <p class="text-sm text-blue-800 font-medium mb-2">Belum punya template?</p>
                <p class="text-sm text-blue-700 mb-3">Download template Excel untuk memastikan format data sudah sesuai</p>
                <button @click="downloadTemplate()" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                  <i class='bx bx-download'></i> Download Template
                </button>
              </div>
            </div>
          </div>

          {{-- File Upload --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File Excel</label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-blue-500 transition-colors"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="handleFileDrop($event)"
                 :class="{ 'border-blue-500 bg-blue-50': isDragging }">
              <input type="file" 
                     id="importFile" 
                     @change="handleFileSelect($event)" 
                     accept=".xlsx,.xls,.csv"
                     class="hidden">
              <label for="importFile" class="cursor-pointer">
                <i class='bx bx-cloud-upload text-4xl text-slate-400 mb-2'></i>
                <p class="text-sm text-slate-600 mb-1">
                  <span class="text-blue-600 font-medium">Klik untuk upload</span> atau drag & drop
                </p>
                <p class="text-xs text-slate-500">Excel (.xlsx, .xls) atau CSV</p>
              </label>
            </div>
            
            {{-- Selected File Info --}}
            <div x-show="importFile" class="mt-3 p-3 bg-slate-50 rounded-lg flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i class='bx bx-file text-green-600 text-xl'></i>
                <div>
                  <p class="text-sm font-medium text-slate-800" x-text="importFile?.name"></p>
                  <p class="text-xs text-slate-500" x-text="formatFileSize(importFile?.size)"></p>
                </div>
              </div>
              <button @click="importFile = null" class="text-red-600 hover:text-red-800">
                <i class='bx bx-x text-xl'></i>
              </button>
            </div>
          </div>

          {{-- Upload Progress --}}
          <div x-show="importProgress.show" class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-blue-800" x-text="importProgress.message"></span>
              <span class="text-sm text-blue-600" x-text="importProgress.percentage + '%'"></span>
            </div>
            <div class="w-full bg-blue-200 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full transition-all" 
                   :style="'width: ' + importProgress.percentage + '%'"></div>
            </div>
          </div>

          {{-- Import Results --}}
          <div x-show="importResult.show" class="rounded-lg p-4"
               :class="importResult.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
            <div class="flex items-start gap-3">
              <i :class="importResult.success ? 'bx bx-check-circle text-green-600' : 'bx bx-error-circle text-red-600'" 
                 class="text-xl"></i>
              <div class="flex-1">
                <p class="text-sm font-medium mb-2" 
                   :class="importResult.success ? 'text-green-800' : 'text-red-800'"
                   x-text="importResult.message"></p>
                <div x-show="importResult.errors && importResult.errors.length > 0" class="mt-2">
                  <p class="text-xs font-medium mb-1" 
                     :class="importResult.success ? 'text-green-700' : 'text-red-700'">
                    Error Details:
                  </p>
                  <ul class="text-xs space-y-1" 
                      :class="importResult.success ? 'text-green-600' : 'text-red-600'">
                    <template x-for="error in importResult.errors.slice(0, 5)" :key="error">
                      <li x-text="error"></li>
                    </template>
                    <li x-show="importResult.errors.length > 5" class="font-medium">
                      ... dan <span x-text="importResult.errors.length - 5"></span> error lainnya
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
          <button @click="closeImportModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
            Batal
          </button>
          <button @click="processImport()" 
                  :disabled="!importFile || importProgress.show"
                  class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="!importProgress.show">Import Data</span>
            <span x-show="importProgress.show">Mengimpor...</span>
          </button>
        </div>
      </div>
    </div>

  </div>

  <script>
    function fixedAssetsManagement() {
      return {
        chartPeriod: 'current',
        showAssetModal: false,
        showViewModal: false,
        showDepreciationModal: false,
        showDisposalModal: false,
        showImportModal: false,
        editingAsset: null,
        viewingAsset: null,
        disposalAsset: null,
        importFile: null,
        isDragging: false,
        importProgress: {
          show: false,
          message: '',
          percentage: 0
        },
        importResult: {
          show: false,
          success: false,
          message: '',
          errors: []
        },
        depreciationForm: {
          outlet_id: null,
          period_month: new Date().getMonth() + 1,
          period_year: new Date().getFullYear(),
          auto_post: false
        },
        disposalForm: {
          disposal_date: new Date().toISOString().split('T')[0],
          disposal_value: 0,
          disposal_notes: ''
        },
        depreciationProgress: {
          show: false,
          message: '',
          processed: 0,
          total: 0
        },
        assetForm: {
          code: '',
          name: '',
          category: 'equipment',
          location: '',
          outlet_id: '',
          book_id: '',
          acquisition_date: new Date().toISOString().split('T')[0],
          acquisition_cost: 0,
          salvage_value: 0,
          useful_life: 0,
          depreciation_method: 'straight_line',
          asset_account_id: '',
          depreciation_expense_account_id: '',
          accumulated_depreciation_account_id: '',
          payment_account_id: '',
          status: 'active',
          description: ''
        },
        depreciationFilters: {
          asset_id: 'all', // Default to show all assets
          month: '' // Empty = show all months
        },
        filters: {
          outlet_id: null, // Will be set after outlets loaded
          book_id: '', // Empty = all books
          status: 'all',
          category: 'all'
        },
        outlets: [],
        books: [],
        assetStats: {
          totalAssets: 24,
          activeAssets: 22,
          totalAcquisitionCost: 1250000000,
          totalDepreciation: 250000000,
          totalBookValue: 1000000000,
          depreciationRate: 20
        },
        assetsData: [],
        upcomingDepreciations: [],
        depreciationHistory: [],
        assetAccounts: [],
        depreciationAccounts: [],
        accumulatedDepreciationAccounts: [],
        paymentAccounts: [],
        valueChartData: null,
        distributionChartData: null,
        valueChart: null,
        distributionChart: null,
        chartsInitialized: false,
        chartReloadTimeout: null,

        async init() {
          // Load outlets first to set default outlet_id
          try {
            await this.loadOutlets();
            await this.loadBooks(); // Load books after outlet is set
            
            // Then load other data in parallel
            await Promise.all([
              this.loadAssets(),
              this.loadDepreciationHistory(),
              this.loadAccounts()
            ]);
            
            // Charts dimuat terakhir setelah data siap, hanya sekali
            if (!this.chartsInitialized) {
              this.$nextTick(() => {
                this.initCharts();
                this.chartsInitialized = true;
              });
            }
          } catch (error) {
            console.error('Error during initialization:', error);
          }
        },

        async loadBooks() {
          if (!this.filters.outlet_id) return;
          
          try {
            const response = await fetch(`{{ route('finance.active-books.data') }}?outlet_id=${this.filters.outlet_id}`);
            const result = await response.json();
            if (result.success) {
              this.books = result.data;
              // Set default to first book if available
              if (this.books.length > 0 && !this.filters.book_id) {
                this.filters.book_id = this.books[0].id;
              }
            }
          } catch (error) {
            console.error('Error loading books:', error);
          }
        },

        async onOutletChange() {
          // Reset depreciation filters to show all assets when outlet changes
          this.depreciationFilters.asset_id = 'all';
          this.depreciationFilters.month = '';
          
          console.log('Outlet changed to:', this.filters.outlet_id);
          console.log('Reset depreciation filters to show all assets');
          
          // Reload books for new outlet and wait for it to set default book_id
          await this.loadBooks();
          
          // Reload all data for new outlet
          this.loadAssets();
          this.reloadCharts();
          this.loadDepreciationHistory();
        },

        resetFilters() {
          // Reset to default outlet (user's outlet or first outlet)
          @if(auth()->user() && auth()->user()->id_outlet)
            const defaultOutlet = {{ auth()->user()->id_outlet }};
          @else
            const defaultOutlet = this.outlets.length > 0 ? this.outlets[0].id_outlet : null;
          @endif
          
          this.filters = {
            outlet_id: defaultOutlet,
            status: 'all',
            category: 'all'
          };
          
          // Also reset depreciation filters
          this.depreciationFilters.asset_id = 'all';
          this.depreciationFilters.month = '';
          
          this.loadAssets();
          this.loadDepreciationHistory();
        },

        async loadOutlets() {
          try {
            const response = await fetch('{{ route("finance.outlets.data") }}', {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const result = await response.json();
            
            if (result.success) {
              this.outlets = result.data;
              
              // Set default outlet: user's outlet or first outlet
              @if(auth()->user() && auth()->user()->id_outlet)
                this.filters.outlet_id = {{ auth()->user()->id_outlet }};
              @else
                if (this.outlets.length > 0) {
                  this.filters.outlet_id = this.outlets[0].id_outlet;
                }
              @endif
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
          }
        },

        async loadAssets() {
          try {
            const params = new URLSearchParams();
            
            if (this.filters.outlet_id !== 'all') {
              params.append('outlet_id', this.filters.outlet_id);
            }
            
            if (this.filters.status !== 'all') {
              params.append('status', this.filters.status);
            }
            
            if (this.filters.category !== 'all') {
              params.append('category', this.filters.category);
            }
            
            const response = await fetch(`{{ route("finance.fixed-assets.data") }}?${params.toString()}`, {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const result = await response.json();
            
            if (result.success) {
              this.assetsData = result.data;
              this.assetStats = result.stats;
              
              // Filter upcoming depreciations
              this.upcomingDepreciations = this.assetsData.filter(asset => 
                asset.status === 'active' && asset.remaining_life > 0
              );
            } else {
              console.error('Failed to load assets:', result.message);
              alert('Gagal memuat data aset: ' + result.message);
            }
          } catch (error) {
            console.error('Error loading assets:', error);
            alert('Terjadi kesalahan saat memuat data aset');
          }
        },

        async loadDepreciationHistory() {
          try {
            const params = new URLSearchParams();
            
            // Add pagination parameter to show all records (or large number)
            params.append('per_page', '1000'); // Show up to 1000 records
            
            // Add outlet_id filter
            if (this.filters.outlet_id && this.filters.outlet_id !== 'all') {
              params.append('outlet_id', this.filters.outlet_id);
            }
            
            // Log current filter state
            console.log('Depreciation filters:', {
              asset_id: this.depreciationFilters.asset_id,
              month: this.depreciationFilters.month,
              outlet_id: this.filters.outlet_id
            });
            
            if (this.depreciationFilters.asset_id && this.depreciationFilters.asset_id !== 'all') {
              params.append('asset_id', this.depreciationFilters.asset_id);
              console.log('Filtering by asset_id:', this.depreciationFilters.asset_id);
            } else {
              console.log('Showing all assets (no asset_id filter)');
            }
            
            if (this.depreciationFilters.month) {
              const [year, month] = this.depreciationFilters.month.split('-');
              params.append('month', month);
              params.append('year', year);
            }
            
            const url = `{{ route("finance.fixed-assets.depreciation.history") }}?${params.toString()}`;
            console.log('Loading depreciation history from:', url);
            
            const response = await fetch(url, {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            if (!response.ok) {
              throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            console.log('Depreciation history result:', result);
            console.log('Depreciation history meta:', result.meta);
            
            if (result.success) {
              this.depreciationHistory = result.data || [];
              console.log('Depreciation history loaded:', this.depreciationHistory.length, 'records');
              
              // Log pagination info
              if (result.meta) {
                console.log(`Showing ${this.depreciationHistory.length} of ${result.meta.total} total records`);
              }
            } else {
              console.error('Failed to load depreciation history:', result.message);
              this.depreciationHistory = [];
            }
          } catch (error) {
            console.error('Error loading depreciation history:', error);
            this.depreciationHistory = [];
          }
        },

        async loadAccounts() {
          try {
            const headers = {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            };
            
            // Parallel loading semua akun sekaligus untuk performa maksimal
            const [assetResult, expenseResult, contraResult, paymentResult] = await Promise.all([
              fetch('{{ route("finance.accounts.active") }}?type=asset&exclude_contra=true', { method: 'GET', headers }).then(r => r.json()),
              fetch('{{ route("finance.accounts.active") }}?type=expense', { method: 'GET', headers }).then(r => r.json()),
              fetch('{{ route("finance.accounts.active") }}?type=asset&contra=true', { method: 'GET', headers }).then(r => r.json()),
              fetch('{{ route("finance.accounts.active") }}?type=asset&exclude_contra=true', { method: 'GET', headers }).then(r => r.json())
            ]);

            // Process results
            if (assetResult.success) this.assetAccounts = assetResult.data;
            if (expenseResult.success) this.depreciationAccounts = expenseResult.data;
            if (contraResult.success) this.accumulatedDepreciationAccounts = contraResult.data;
            
            if (paymentResult.success) {
              const cashBankAccounts = paymentResult.data.filter(account => {
                const nameLower = account.name.toLowerCase();
                return nameLower.includes('kas') || nameLower.includes('bank') || 
                       nameLower.includes('cash') || nameLower.includes('petty');
              });
              this.paymentAccounts = cashBankAccounts.length > 0 ? cashBankAccounts : paymentResult.data;
            }
          } catch (error) {
            console.error('Error loading accounts:', error);
          }
        },

        async initCharts() {
          if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded');
            return;
          }

          // Destroy existing charts before creating new ones
          if (this.valueChart) {
            try {
              this.valueChart.destroy();
            } catch (e) {
              console.warn('Error destroying value chart:', e);
            }
            this.valueChart = null;
          }
          if (this.distributionChart) {
            try {
              this.distributionChart.destroy();
            } catch (e) {
              console.warn('Error destroying distribution chart:', e);
            }
            this.distributionChart = null;
          }

          // Load chart data from API
          await this.loadChartData();

          // Asset Value Chart
          const valueCtx = this.$refs.assetValueChart;
          if (valueCtx && this.valueChartData) {
            try {
              // Get 2D context to ensure canvas is ready
              const ctx = valueCtx.getContext('2d');
              this.valueChart = new Chart(ctx, {
                type: 'bar',
                data: this.valueChartData,
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      position: 'top',
                    }
                  },
                  scales: {
                    y: {
                      beginAtZero: true,
                      ticks: {
                        callback: function(value) {
                          if (value >= 1000000000) {
                            return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
                          } else if (value >= 1000000) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                          }
                          return 'Rp ' + value;
                        }
                      }
                    }
                  }
                }
              });
              console.log('Value chart created successfully');
            } catch (error) {
              console.error('Error creating value chart:', error);
            }
          } else {
            console.warn('Value chart context or data not available');
          }

          // Asset Distribution Chart
          const distributionCtx = this.$refs.assetDistributionChart;
          if (distributionCtx && this.distributionChartData) {
            try {
              // Get 2D context to ensure canvas is ready
              const ctx = distributionCtx.getContext('2d');
              this.distributionChart = new Chart(ctx, {
                type: 'pie',
                data: this.distributionChartData,
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      position: 'right'
                    }
                  }
                }
              });
              console.log('Distribution chart created successfully');
            } catch (error) {
              console.error('Error creating distribution chart:', error);
            }
          } else {
            console.warn('Distribution chart context or data not available');
          }
        },

        async loadChartData() {
          try {
            // Add outlet_id to chart requests
            const outletParam = this.filters.outlet_id ? `&outlet_id=${this.filters.outlet_id}` : '';
            
            // Load asset value chart data
            const valueResponse = await fetch(`{{ route("finance.fixed-assets.chart.value") }}?period=${this.chartPeriod}${outletParam}`, {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const valueResult = await valueResponse.json();
            if (valueResult.success) {
              this.valueChartData = valueResult.data;
            }

            // Load asset distribution chart data
            const distributionResponse = await fetch(`{{ route("finance.fixed-assets.chart.distribution") }}?${outletParam.substring(1)}`, {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const distributionResult = await distributionResponse.json();
            if (distributionResult.success) {
              // Use count_chart for distribution (jumlah aset per kategori)
              this.distributionChartData = distributionResult.data.count_chart || distributionResult.data;
              console.log('Distribution chart data loaded:', this.distributionChartData);
            }
          } catch (error) {
            console.error('Error loading chart data:', error);
          }
        },

        reloadCharts() {
          // Debounced chart reload to prevent infinite loop
          if (this.chartReloadTimeout) {
            clearTimeout(this.chartReloadTimeout);
          }
          
          this.chartReloadTimeout = setTimeout(async () => {
            try {
              await this.loadChartData();
              
              // Destroy and recreate charts to prevent memory leaks
              if (this.valueChart) {
                this.valueChart.destroy();
                this.valueChart = null;
              }
              if (this.distributionChart) {
                this.distributionChart.destroy();
                this.distributionChart = null;
              }
              
              this.$nextTick(() => {
                this.initCharts();
              });
            } catch (error) {
              console.error('Error reloading charts:', error);
            }
          }, 300);
        },

        async openCreateAsset() {
          this.editingAsset = null;
          
          // Get outlet_id from filter
          const outletId = this.filters.outlet_id === 'all' ? (this.outlets[0]?.id_outlet || 1) : this.filters.outlet_id;
          
          // Generate asset code from API
          try {
            const response = await fetch(`{{ route("finance.fixed-assets.generate-code") }}?outlet_id=${outletId}`, {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });
            
            const result = await response.json();
            const generatedCode = result.success ? result.data.code : this.generateAssetCode();
            
            this.assetForm = {
              code: generatedCode,
              outlet_id: outletId,
              book_id: this.filters.book_id || '', // Set from filter
              name: '',
              category: 'equipment',
              location: '',
              acquisition_date: new Date().toISOString().split('T')[0],
              acquisition_cost: 0,
              salvage_value: 0,
              useful_life: 0,
              depreciation_method: 'straight_line',
              asset_account_id: '',
              depreciation_expense_account_id: '',
              accumulated_depreciation_account_id: '',
              payment_account_id: '',
              status: 'active',
              description: ''
            };
          } catch (error) {
            console.error('Error generating asset code:', error);
            this.assetForm = {
              code: this.generateAssetCode(),
              outlet_id: outletId,
              name: '',
              category: 'equipment',
              location: '',
              acquisition_date: new Date().toISOString().split('T')[0],
              acquisition_cost: 0,
              salvage_value: 0,
              useful_life: 0,
              depreciation_method: 'straight_line',
              asset_account_id: '',
              depreciation_expense_account_id: '',
              accumulated_depreciation_account_id: '',
              payment_account_id: '',
              status: 'active',
              description: ''
            };
          }
          
          this.showAssetModal = true;
        },

        async editAsset(id) {
          try {
            // Fetch asset details from API
            const response = await fetch(`{{ route("finance.fixed-assets.show", ":id") }}`.replace(':id', id), {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const result = await response.json();
            console.log('Edit asset result:', result);
            
            if (result.success) {
              // API returns nested data.asset structure
              const asset = result.data.asset || result.data;
              this.editingAsset = id;
              
              // Format acquisition_date from ISO to YYYY-MM-DD
              let acquisitionDate = asset.acquisition_date;
              if (acquisitionDate && acquisitionDate.includes('T')) {
                acquisitionDate = acquisitionDate.split('T')[0];
              }
              
              this.assetForm = {
                outlet_id: asset.outlet_id || this.filters.outlet_id,
                code: asset.code || '',
                name: asset.name || '',
                category: asset.category || 'equipment',
                location: asset.location || '',
                acquisition_date: acquisitionDate || '',
                acquisition_cost: parseFloat(asset.acquisition_cost || 0),
                salvage_value: parseFloat(asset.salvage_value || 0),
                useful_life: parseInt(asset.useful_life || 0),
                depreciation_method: asset.depreciation_method || 'straight_line',
                asset_account_id: asset.asset_account_id || '',
                depreciation_expense_account_id: asset.depreciation_expense_account_id || '',
                accumulated_depreciation_account_id: asset.accumulated_depreciation_account_id || '',
                payment_account_id: asset.payment_account_id || '',
                status: asset.status || 'active',
                description: asset.description || ''
              };
              
              console.log('Asset form populated:', this.assetForm);
              
              // Force Alpine.js to update
              this.$nextTick(() => {
                this.showAssetModal = true;
              });
            } else {
              alert('Gagal memuat data aset: ' + result.message);
            }
          } catch (error) {
            console.error('Error loading asset:', error);
            alert('Terjadi kesalahan saat memuat data aset');
          }
        },

        generateAssetCode() {
          const now = new Date();
          const year = now.getFullYear();
          const month = String(now.getMonth() + 1).padStart(2, '0');
          const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
          return `AST-${year}${month}-${random}`;
        },

        calculateAnnualDepreciation() {
          if (!this.assetForm.acquisition_cost || !this.assetForm.useful_life) return 0;
          const depreciableBase = this.assetForm.acquisition_cost - this.assetForm.salvage_value;
          return depreciableBase / this.assetForm.useful_life;
        },

        calculateMonthlyDepreciation() {
          return this.calculateAnnualDepreciation() / 12;
        },

        calculateDepreciationRate() {
          if (!this.assetForm.acquisition_cost || !this.assetForm.useful_life) return 0;
          const annualDepreciation = this.calculateAnnualDepreciation();
          return (annualDepreciation / this.assetForm.acquisition_cost) * 100;
        },

        async saveAsset() {
          // Validate form data
          if (!this.assetForm.code || !this.assetForm.name) {
            alert('Kode dan nama aset harus diisi');
            return;
          }
          
          if (!this.assetForm.outlet_id) {
            alert('Outlet harus dipilih');
            return;
          }
          
          if (!this.assetForm.acquisition_cost || this.assetForm.acquisition_cost <= 0) {
            alert('Nilai perolehan harus lebih besar dari 0');
            return;
          }
          
          if (this.assetForm.salvage_value >= this.assetForm.acquisition_cost) {
            alert('Nilai residu harus lebih kecil dari nilai perolehan');
            return;
          }
          
          if (!this.assetForm.useful_life || this.assetForm.useful_life < 1) {
            alert('Masa manfaat minimal 1 tahun');
            return;
          }
          
          if (!this.assetForm.asset_account_id || !this.assetForm.depreciation_expense_account_id || 
              !this.assetForm.accumulated_depreciation_account_id || !this.assetForm.payment_account_id) {
            alert('Semua akun harus dipilih');
            return;
          }
          
          try {
            const url = this.editingAsset 
              ? `{{ route('finance.fixed-assets.update', ':id') }}`.replace(':id', this.editingAsset)
              : '{{ route('finance.fixed-assets.store') }}';
            
            // Prepare data
            const data = {...this.assetForm};
            
            // Add _method for PUT request
            if (this.editingAsset) {
              data._method = 'PUT';
            }
            
            const response = await fetch(url, {
              method: 'POST', // Always use POST, Laravel will handle _method
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              },
              body: JSON.stringify(data)
            });

            const result = await response.json();
            
            if (result.success) {
              alert(this.editingAsset ? 'Aset berhasil diperbarui' : 'Aset berhasil ditambahkan');
              this.showAssetModal = false;
              await this.loadAssets();
            } else {
              alert('Gagal menyimpan aset: ' + result.message);
            }
          } catch (error) {
            console.error('Error saving asset:', error);
            alert('Terjadi kesalahan saat menyimpan aset');
          }
        },

        async viewAsset(id) {
          try {
            // Fetch asset details from API
            const response = await fetch(`{{ route("finance.fixed-assets.show", ":id") }}`.replace(':id', id), {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const result = await response.json();
            console.log('View asset result:', result);
            
            if (result.success) {
              // API returns nested data.asset structure
              const asset = result.data.asset || result.data;
              
              // Format data for display with safe fallbacks
              this.viewingAsset = {
                id: asset.id || '',
                code: asset.code || '',
                name: asset.name || '',
                category: asset.category || '',
                location: asset.location || '',
                status: asset.status || '',
                acquisition_date: asset.acquisition_date ? asset.acquisition_date.split('T')[0] : '',
                acquisition_cost: parseFloat(asset.acquisition_cost || 0),
                salvage_value: parseFloat(asset.salvage_value || 0),
                useful_life: parseInt(asset.useful_life || 0),
                depreciation_method: asset.depreciation_method || '',
                accumulated_depreciation: parseFloat(asset.accumulated_depreciation || 0),
                book_value: parseFloat(asset.book_value || 0),
                description: asset.description || '',
                asset_account: asset.asset_account || null,
                depreciation_expense_account: asset.depreciation_expense_account || null,
                accumulated_depreciation_account: asset.accumulated_depreciation_account || null,
                payment_account: asset.payment_account || null
              };
              
              console.log('Viewing asset formatted:', this.viewingAsset);
              this.showViewModal = true;
            } else {
              alert('Gagal memuat data aset: ' + result.message);
            }
          } catch (error) {
            console.error('Error loading asset:', error);
            alert('Terjadi kesalahan saat memuat data aset');
          }
        },

        calculateAssetDepreciation(id) {
          // Open depreciation modal for specific asset
          const asset = this.assetsData.find(a => a.id === id);
          if (!asset) {
            alert('Aset tidak ditemukan');
            return;
          }
          
          this.depreciationForm = {
            outlet_id: asset.outlet_id,
            asset_ids: [id], // Array of single asset ID
            period_month: new Date().getMonth() + 1,
            period_year: new Date().getFullYear(),
            auto_post: true // Auto post for single asset
          };
          this.showDepreciationModal = true;
        },

        calculateDepreciation() {
          // Show modal for period selection
          this.depreciationForm = {
            outlet_id: this.filters.outlet_id,
            period_month: new Date().getMonth() + 1,
            period_year: new Date().getFullYear(),
            auto_post: false
          };
          this.showDepreciationModal = true;
        },

        async processBatchDepreciation() {
          if (!confirm('Apakah Anda yakin ingin menghitung penyusutan untuk periode ini?')) {
            return;
          }

          try {
            this.depreciationProgress.show = true;
            this.depreciationProgress.message = 'Menghitung penyusutan...';
            this.depreciationProgress.processed = 0;
            this.depreciationProgress.total = 0;

            const response = await fetch('{{ route("finance.fixed-assets.depreciation.batch") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify(this.depreciationForm)
            });

            const result = await response.json();
            
            if (result.success) {
              this.depreciationProgress.message = 'Selesai!';
              this.depreciationProgress.processed = result.data.total_assets_processed;
              this.depreciationProgress.total = result.data.total_assets_processed;
              
              // Show summary
              const summary = `
Penyusutan berhasil dihitung:
- Total aset diproses: ${result.data.total_assets_processed}
- Total jurnal dibuat: ${result.data.total_journals_created}
- Total nilai penyusutan: ${this.formatCurrency(result.data.total_depreciation_amount)}
${result.data.errors && result.data.errors.length > 0 ? '\n\nError:\n' + result.data.errors.map(e => `- ${e.asset_code}: ${e.error}`).join('\n') : ''}
              `;
              
              alert(summary);
              
              this.showDepreciationModal = false;
              this.depreciationProgress.show = false;
              
              // Reload data
              await this.loadAssets();
              await this.loadDepreciationHistory();
            } else {
              alert('Gagal menghitung penyusutan: ' + result.message);
              this.depreciationProgress.show = false;
            }
          } catch (error) {
            console.error('Error calculating depreciation:', error);
            alert('Terjadi kesalahan saat menghitung penyusutan');
            this.depreciationProgress.show = false;
          }
        },

        async toggleAsset(id, status) {
          const newStatus = status === 'active' ? 'inactive' : 'active';
          if (!confirm(`Apakah Anda yakin ingin ${newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan'} aset ini?`)) {
            return;
          }

          try {
            const response = await fetch(`{{ route("finance.fixed-assets.toggle", ":id") }}`.replace(':id', id), {
              method: 'PATCH',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const result = await response.json();
            
            if (result.success) {
              alert(`Aset berhasil ${newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan'}`);
              await this.loadAssets();
            } else {
              alert('Gagal mengubah status aset: ' + result.message);
            }
          } catch (error) {
            console.error('Error toggling asset:', error);
            alert('Terjadi kesalahan saat mengubah status aset');
          }
        },

        openDisposalModal(asset) {
          this.disposalAsset = asset;
          this.disposalForm = {
            disposal_date: new Date().toISOString().split('T')[0],
            disposal_value: 0,
            disposal_notes: ''
          };
          this.showDisposalModal = true;
        },

        calculateDisposalGainLoss() {
          if (!this.disposalAsset) return 0;
          return this.disposalForm.disposal_value - this.disposalAsset.book_value;
        },

        async disposeAsset() {
          if (!this.disposalAsset) return;

          if (!confirm('Apakah Anda yakin ingin melepas aset ini? Jurnal pelepasan akan dibuat secara otomatis.')) {
            return;
          }

          try {
            const response = await fetch(`{{ route("finance.fixed-assets.dispose", ":id") }}`.replace(':id', this.disposalAsset.id), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify(this.disposalForm)
            });

            const result = await response.json();
            
            if (result.success) {
              const gainLoss = this.calculateDisposalGainLoss();
              const message = gainLoss >= 0 
                ? `Aset berhasil dilepas dengan keuntungan ${this.formatCurrency(gainLoss)}`
                : `Aset berhasil dilepas dengan kerugian ${this.formatCurrency(Math.abs(gainLoss))}`;
              
              alert(message);
              this.showDisposalModal = false;
              await this.loadAssets();
            } else {
              alert('Gagal melepas aset: ' + result.message);
            }
          } catch (error) {
            console.error('Error disposing asset:', error);
            alert('Terjadi kesalahan saat melepas aset');
          }
        },

        async postDepreciation(id) {
          if (!confirm('Apakah Anda yakin ingin memposting penyusutan ini? Jurnal akan dibuat secara otomatis.')) {
            return;
          }

          try {
            const response = await fetch(`{{ route("finance.fixed-assets.depreciation.post", ":id") }}`.replace(':id', id), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const result = await response.json();
            
            if (result.success) {
              alert('Penyusutan berhasil diposting dan jurnal telah dibuat');
              await this.loadDepreciationHistory();
              await this.loadAssets();
            } else {
              alert('Gagal memposting penyusutan: ' + result.message);
            }
          } catch (error) {
            console.error('Error posting depreciation:', error);
            alert('Terjadi kesalahan saat memposting penyusutan');
          }
        },

        async reverseDepreciation(id) {
          if (!confirm('PERINGATAN: Apakah Anda yakin ingin membalikkan penyusutan ini? Jurnal pembalik akan dibuat.')) {
            return;
          }

          try {
            const response = await fetch(`{{ route("finance.fixed-assets.depreciation.reverse", ":id") }}`.replace(':id', id), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const result = await response.json();
            
            if (result.success) {
              alert('Penyusutan berhasil dibalikkan dan jurnal pembalik telah dibuat');
              await this.loadDepreciationHistory();
              await this.loadAssets();
            } else {
              alert('Gagal membalikkan penyusutan: ' + result.message);
            }
          } catch (error) {
            console.error('Error reversing depreciation:', error);
            alert('Terjadi kesalahan saat membalikkan penyusutan');
          }
        },

        getStatusBadgeClass(status) {
          const classes = {
            active: 'bg-green-100 text-green-800',
            inactive: 'bg-red-100 text-red-800',
            sold: 'bg-orange-100 text-orange-800',
            disposed: 'bg-gray-100 text-gray-800'
          };
          return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusName(status) {
          const names = {
            active: 'Aktif',
            inactive: 'Nonaktif',
            sold: 'Terjual',
            disposed: 'Dibuang'
          };
          return names[status] || status;
        },

        getCategoryValue(category) {
          const mapping = {
            'Bangunan': 'building',
            'Kendaraan': 'vehicle',
            'Komputer & IT': 'computer'
          };
          return mapping[category] || category;
        },

        getMethodValue(method) {
          const mapping = {
            'Garis Lurus': 'straight_line',
            'Saldo Menurun': 'declining_balance'
          };
          return mapping[method] || method;
        },

        formatDateForInput(dateString) {
          // Convert "15 Jan 2020" to "2020-01-15"
          const date = new Date(dateString);
          return date.toISOString().split('T')[0];
        },

        formatCurrency(amount) {
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
          }).format(amount);
        },

        formatFileSize(bytes) {
          if (!bytes) return '0 Bytes';
          const k = 1024;
          const sizes = ['Bytes', 'KB', 'MB', 'GB'];
          const i = Math.floor(Math.log(bytes) / Math.log(k));
          return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },

        // Export Functions
        exportToXLSX() {
          const params = new URLSearchParams();
          if (this.filters.outlet_id) params.append('outlet_id', this.filters.outlet_id);
          if (this.filters.status !== 'all') params.append('status', this.filters.status);
          if (this.filters.category !== 'all') params.append('category', this.filters.category);
          
          window.location.href = `{{ route("finance.fixed-assets.export.xlsx") }}?${params.toString()}`;
        },

        exportToPDF() {
          const params = new URLSearchParams();
          if (this.filters.outlet_id) params.append('outlet_id', this.filters.outlet_id);
          if (this.filters.status !== 'all') params.append('status', this.filters.status);
          if (this.filters.category !== 'all') params.append('category', this.filters.category);
          params.append('group_by_category', 'true'); // Group by category in PDF
          
          window.open(`{{ route("finance.fixed-assets.export.pdf") }}?${params.toString()}`, '_blank');
        },

        // Import Functions
        openImportModal() {
          this.showImportModal = true;
          this.importFile = null;
          this.importProgress = { show: false, message: '', percentage: 0 };
          this.importResult = { show: false, success: false, message: '', errors: [] };
        },

        closeImportModal() {
          this.showImportModal = false;
          this.importFile = null;
          this.importProgress = { show: false, message: '', percentage: 0 };
          this.importResult = { show: false, success: false, message: '', errors: [] };
        },

        handleFileSelect(event) {
          const file = event.target.files[0];
          if (file) {
            this.importFile = file;
            this.importResult.show = false;
          }
        },

        handleFileDrop(event) {
          this.isDragging = false;
          const file = event.dataTransfer.files[0];
          if (file && (file.name.endsWith('.xlsx') || file.name.endsWith('.xls') || file.name.endsWith('.csv'))) {
            this.importFile = file;
            this.importResult.show = false;
          } else {
            alert('File harus berformat Excel (.xlsx, .xls) atau CSV');
          }
        },

        async downloadTemplate() {
          try {
            window.location.href = '{{ route("finance.fixed-assets.template") }}';
          } catch (error) {
            console.error('Error downloading template:', error);
            alert('Gagal mengunduh template');
          }
        },

        async processImport() {
          if (!this.importFile) {
            alert('Pilih file terlebih dahulu');
            return;
          }

          this.importProgress.show = true;
          this.importProgress.message = 'Mengupload file...';
          this.importProgress.percentage = 0;
          this.importResult.show = false;

          const formData = new FormData();
          formData.append('file', this.importFile);
          formData.append('outlet_id', this.filters.outlet_id);

          try {
            // Simulate progress
            const progressInterval = setInterval(() => {
              if (this.importProgress.percentage < 90) {
                this.importProgress.percentage += 10;
              }
            }, 200);

            const response = await fetch('{{ route("finance.fixed-assets.import") }}', {
              method: 'POST',
              body: formData,
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            clearInterval(progressInterval);
            this.importProgress.percentage = 100;

            const result = await response.json();
            
            this.importProgress.show = false;
            this.importResult.show = true;
            this.importResult.success = result.success;
            this.importResult.message = result.message;
            this.importResult.errors = result.errors || [];

            if (result.success) {
              // Reload assets after successful import
              setTimeout(async () => {
                await this.loadAssets();
                this.closeImportModal();
              }, 2000);
            }
          } catch (error) {
            console.error('Error importing assets:', error);
            this.importProgress.show = false;
            this.importResult.show = true;
            this.importResult.success = false;
            this.importResult.message = 'Terjadi kesalahan saat mengimpor data: ' + error.message;
          }
        },

        exportDepreciation() {
          alert('Export depreciation - Fitur akan segera tersedia');
        }
      };
    }
  </script>
</x-layouts.admin>
