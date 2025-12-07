{{-- resources/views/admin/produksi/produksi/index.blade.php --}}
<x-layouts.admin :title="'Data Produksi'">
    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    @endpush

    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Data Produksi</h1>
                <p class="text-slate-600">Kelola rencana & realisasi produksi</p>
            </div>
            <div class="flex items-center gap-3">
                <select id="outletSelect" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}" {{ $outlet->id == $selectedOutlet ? 'selected' : '' }}>
                            {{ $outlet->nama_outlet }}
                        </option>
                    @endforeach
                </select>
                <button id="createProductionBtn" 
                        class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-lg transition">
                    <i class='bx bx-plus'></i>
                    <span>Buat Produksi Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ====== FILTER & STATS ====== --}}
    <section class="mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            {{-- Filter Section --}}
            <div class="lg:col-span-3">
                <div class="flex flex-wrap gap-3">
                    <select id="filterStatus" class="border border-slate-200 rounded-lg px-3 py-2 text-sm min-w-32">
                        <option value="all">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Disetujui</option>
                        <option value="in_progress">Berjalan</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    <select id="filterLine" class="border border-slate-200 rounded-lg px-3 py-2 text-sm min-w-32">
                        <option value="all">Semua Lini</option>
                        <option value="Lini A">Lini A</option>
                        <option value="Lini B">Lini B</option>
                        <option value="Lini C">Lini C</option>
                        <option value="Lini D">Lini D</option>
                    </select>
                    <input type="date" id="filterStartDate" class="border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="Dari Tanggal">
                    <input type="date" id="filterEndDate" class="border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="Sampai Tanggal">
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-primary-50 rounded-xl p-4 border border-primary-100">
                <div class="text-center">
                    <div id="activeCount" class="text-2xl font-bold text-primary-700">0</div>
                    <div class="text-sm text-primary-600">Produksi Aktif</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====== DATA TABLE ====== --}}
    <section class="mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
            <div class="overflow-x-auto p-4">
                <table id="productionTable" class="w-full display">
                    <thead>
                        <tr>
                            <th>ID Produksi</th>
                            <th>Produk</th>
                            <th>Lini</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $productionData = [
                                [
                                    'id' => 'PRD-2024-001',
                                    'product' => 'Smartphone X1',
                                    'line' => 'Lini A',
                                    'target' => 5000,
                                    'realization' => 4250,
                                    'progress' => 85,
                                    'status' => 'berjalan',
                                    'start_date' => '2024-01-01',
                                    'end_date' => '2024-01-15'
                                ],
                                [
                                    'id' => 'PRD-2024-002', 
                                    'product' => 'Tablet Pro 12',
                                    'line' => 'Lini B',
                                    'target' => 3000,
                                    'realization' => 3000,
                                    'progress' => 100,
                                    'status' => 'selesai',
                                    'start_date' => '2024-01-05',
                                    'end_date' => '2024-01-12'
                                ],
                                [
                                    'id' => 'PRD-2024-003',
                                    'product' => 'Laptop Ultra',
                                    'line' => 'Lini C',
                                    'target' => 2000,
                                    'realization' => 1500,
                                    'progress' => 75,
                                    'status' => 'berjalan',
                                    'start_date' => '2024-01-08',
                                    'end_date' => '2024-01-20'
                                ],
                                [
                                    'id' => 'PRD-2024-004',
                                    'product' => 'Smart Watch S2',
                                    'line' => 'Lini D',
                                    'target' => 8000,
                                    'realization' => 0,
                                    'progress' => 0,
                                    'status' => 'draft',
                                    'start_date' => '2024-01-15',
                                    'end_date' => '2024-01-25'
                                ],
                                [
                                    'id' => 'PRD-2024-005',
                                    'product' => 'Wireless Earbuds',
                                    'line' => 'Lini A',
                                    'target' => 10000,
                                    'realization' => 6200,
                                    'progress' => 62,
                                    'status' => 'berjalan',
                                    'start_date' => '2024-01-10',
                                    'end_date' => '2024-01-30'
                                ],
                                [
                                    'id' => 'PRD-2024-006',
                                    'product' => 'Power Bank 20k',
                                    'line' => 'Lini B',
                                    'target' => 15000,
                                    'realization' => 0,
                                    'progress' => 0,
                                    'status' => 'disetujui',
                                    'start_date' => '2024-01-18',
                                    'end_date' => '2024-02-05'
                                ],
                                [
                                    'id' => 'PRD-2024-007',
                                    'product' => 'Monitor 24"',
                                    'line' => 'Lini C',
                                    'target' => 2500,
                                    'realization' => 2500,
                                    'progress' => 100,
                                    'status' => 'selesai',
                                    'start_date' => '2024-01-03',
                                    'end_date' => '2024-01-10'
                                ],
                                [
                                    'id' => 'PRD-2024-008',
                                    'product' => 'Keyboard Mech',
                                    'line' => 'Lini D',
                                    'target' => 6000,
                                    'realization' => 0,
                                    'progress' => 0,
                                    'status' => 'dibatalkan',
                                    'start_date' => '2024-01-12',
                                    'end_date' => '2024-01-22'
                                ]
                            ];
                        @endphp

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ====== MODAL CREATE PRODUCTION ====== --}}
    <div id="createModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeCreateModal()"></div>
        
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-4xl transform rounded-2xl bg-white shadow-xl transition-all">
                    {{-- Header --}}
                    <div class="flex items-center justify-between p-6 border-b border-slate-200">
                        <h3 class="text-xl font-semibold">Buat Produksi Baru</h3>
                        <button onclick="closeCreateModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form id="productionForm" class="p-6 space-y-6">
                        {{-- Basic Information --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Product Selection --}}
                            <div class="relative">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Produk *</label>
                                <input type="hidden" name="product_id" id="product_id" required>
                                <input type="text" id="product_search" 
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Ketik untuk mencari produk..." 
                                       autocomplete="off" required>
                                <div id="product_results" class="hidden absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                                <p class="text-xs text-slate-500 mt-1">Ketik minimal 2 karakter untuk mencari</p>
                            </div>

                            {{-- Production Line --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Lini Produksi *</label>
                                <select name="production_line" required
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih Lini</option>
                                    <option value="Lini A">Lini A</option>
                                    <option value="Lini B">Lini B</option>
                                    <option value="Lini C">Lini C</option>
                                    <option value="Lini D">Lini D</option>
                                </select>
                            </div>
                        </div>

                        {{-- Quantity & Dates --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {{-- Target Quantity --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Target Produksi *</label>
                                <input type="number" name="target_quantity" required min="1"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="0">
                            </div>

                            {{-- Start Date --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Mulai *</label>
                                <input type="date" name="start_date" required
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            {{-- End Date --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Selesai *</label>
                                <input type="date" name="end_date" required
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        {{-- Material Requirements --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-3">Kebutuhan Material</label>
                            <div class="space-y-3" id="materialRequirements">
                                {{-- Material row template --}}
                                <div class="flex items-center gap-3 material-row">
                                    <input type="hidden" name="materials[0][material_type]" value="">
                                    <select name="materials[0][material_id]" 
                                            class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                            onchange="updateMaterialUnit(this, 0)">
                                        <option value="">Pilih Material</option>
                                    </select>
                                    <input type="number" name="materials[0][quantity]" min="1" step="0.01"
                                           class="w-32 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                           placeholder="Qty">
                                    <select name="materials[0][unit]"
                                            class="w-24 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="kg">kg</option>
                                        <option value="pcs">pcs</option>
                                        <option value="roll">roll</option>
                                        <option value="unit">unit</option>
                                    </select>
                                    <button type="button" onclick="removeMaterial(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addMaterial()" 
                                    class="mt-3 inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 text-sm">
                                <i class='bx bx-plus'></i>
                                Tambah Material
                            </button>
                        </div>

                        {{-- Additional Information --}}
                        <div class="grid grid-cols-1 gap-6">
                            {{-- Priority --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Prioritas</label>
                                <select name="priority"
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="normal">Normal</option>
                                    <option value="high">Tinggi</option>
                                    <option value="urgent">Mendesak</option>
                                </select>
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Catatan</label>
                                <textarea name="notes" rows="3"
                                          class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                          placeholder="Catatan tambahan untuk produksi..."></textarea>
                            </div>
                        </div>

                        {{-- Quality Standards --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-3">Standar Kualitas</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                                    <span class="text-sm text-slate-700">Tingkat Reject Maksimal</span>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="max_reject_rate" min="0" max="100" step="0.1"
                                               class="w-20 border border-slate-200 rounded px-2 py-1 text-sm"
                                               value="3.0">
                                        <span class="text-sm text-slate-500">%</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                                    <span class="text-sm text-slate-700">Efisiensi Minimal</span>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="min_efficiency" min="0" max="100" step="0.1"
                                               class="w-20 border border-slate-200 rounded px-2 py-1 text-sm"
                                               value="85.0">
                                        <span class="text-sm text-slate-500">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-200">
                        <button onclick="closeCreateModal()" 
                                class="px-4 py-2.5 text-slate-700 hover:bg-slate-100 rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" form="productionForm"
                                class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                            Simpan Produksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div id="detailModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl transform transition-all">
                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-slate-200">
                    <h3 class="text-xl font-semibold text-slate-800">Detail Produksi</h3>
                    <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 transition">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Content --}}
                <div class="p-6 space-y-6" id="detailContent">
                    <div class="text-center py-8">
                        <i class='bx bx-loader-alt bx-spin text-4xl text-slate-400'></i>
                        <p class="text-slate-500 mt-2">Memuat data...</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-200">
                    <button onclick="closeDetailModal()" 
                            class="px-4 py-2.5 text-slate-700 hover:bg-slate-100 rounded-lg transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Realization Modal --}}
    <div id="realizationModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all">
                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-slate-200">
                    <h3 class="text-xl font-semibold text-slate-800">Tambah Realisasi Produksi</h3>
                    <button onclick="closeRealizationModal()" class="text-slate-400 hover:text-slate-600 transition">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Form --}}
                <form id="realizationForm" onsubmit="handleRealizationSubmit(event)">
                    <div class="p-6 space-y-4">
                        <input type="hidden" id="realization_production_id" name="production_id">
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Diproduksi *</label>
                            <input type="number" name="quantity_produced" min="1" required
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Masukkan jumlah yang berhasil diproduksi">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Reject</label>
                            <input type="number" name="quantity_rejected" min="0" value="0"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Masukkan jumlah yang reject (opsional)">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Catatan</label>
                            <textarea name="notes" rows="3"
                                      class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-200">
                        <button type="button" onclick="closeRealizationModal()" 
                                class="px-4 py-2.5 text-slate-700 hover:bg-slate-100 rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                            Simpan Realisasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        // Define URLs for JavaScript
        const productionDataUrl = "{{ route('admin.produksi.produksi.data') }}";
        const storeUrl = "{{ route('admin.produksi.produksi.store') }}";
        const showUrl = "{{ route('admin.produksi.produksi.show', ':id') }}";
        const deleteUrl = "{{ route('admin.produksi.produksi.destroy', ':id') }}";
        const approveUrl = "{{ route('admin.produksi.produksi.approve', ':id') }}";
        const startUrl = "{{ route('admin.produksi.produksi.start', ':id') }}";
        const productsUrl = "{{ route('admin.produksi.produksi.products') }}";
        const materialsUrl = "{{ route('admin.produksi.produksi.materials') }}";
        const statisticsUrl = "{{ route('admin.produksi.produksi.statistics') }}";
        const addRealizationUrl = "{{ route('admin.produksi.produksi.realization', ':id') }}";
    </script>
    <script src="{{ asset('js/production.js') }}?v={{ time() }}"></script>
    @endpush

    <script>
        // Additional inline scripts if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Any page-specific initialization
        });
    </script>
</x-layouts.admin>
