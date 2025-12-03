{{-- resources/views/admin/produksi/index.blade.php --}}
<x-layouts.admin :title="'Data Produksi'">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Data Produksi</h1>
                <p class="text-slate-600">Kelola rencana & realisasi produksi</p>
            </div>
            <button id="createProductionBtn" 
                    class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-lg transition">
                <i class='bx bx-plus'></i>
                <span>Buat Produksi Baru</span>
            </button>
        </div>
    </div>

    {{-- ====== FILTER & STATS ====== --}}
    <section class="mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            {{-- Filter Section --}}
            <div class="lg:col-span-3">
                <div class="flex flex-wrap gap-3">
                    <select class="border border-slate-200 rounded-lg px-3 py-2 text-sm min-w-32">
                        <option>Semua Status</option>
                        <option>Draft</option>
                        <option>Disetujui</option>
                        <option>Berjalan</option>
                        <option>Selesai</option>
                        <option>Dibatalkan</option>
                    </select>
                    <select class="border border-slate-200 rounded-lg px-3 py-2 text-sm min-w-32">
                        <option>Semua Lini</option>
                        <option>Lini A</option>
                        <option>Lini B</option>
                        <option>Lini C</option>
                        <option>Lini D</option>
                    </select>
                    <input type="date" class="border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="Dari Tanggal">
                    <input type="date" class="border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="Sampai Tanggal">
                    <button class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm transition">
                        <i class='bx bx-filter-alt'></i>
                        Filter
                    </button>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-primary-50 rounded-xl p-4 border border-primary-100">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-700">24</div>
                    <div class="text-sm text-primary-600">Produksi Aktif</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====== DATA TABLE ====== --}}
    <section class="mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">ID Produksi</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Produk</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Lini</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Target</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Realisasi</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Progress</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Tanggal</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
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

                        @foreach($productionData as $production)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">
                                {{ $production['id'] }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-900">
                                {{ $production['product'] }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $production['line'] }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-900">
                                {{ number_format($production['target']) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-900">
                                {{ number_format($production['realization']) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-slate-100 rounded-full h-2">
                                        <div class="bg-primary-500 h-2 rounded-full" 
                                             style="width: {{ $production['progress'] }}%"></div>
                                    </div>
                                    <span class="text-sm text-slate-600">{{ $production['progress'] }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-slate-100 text-slate-700',
                                        'disetujui' => 'bg-blue-100 text-blue-700', 
                                        'berjalan' => 'bg-green-100 text-green-700',
                                        'selesai' => 'bg-emerald-100 text-emerald-700',
                                        'dibatalkan' => 'bg-red-100 text-red-700'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$production['status']] }}">
                                    {{ ucfirst($production['status']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($production['start_date'])->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button onclick="openEditModal('{{ $production['id'] }}')" 
                                            class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded transition">
                                        <i class='bx bx-edit text-lg'></i>
                                    </button>
                                    <button class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                    <button class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded transition">
                                        <i class='bx bx-show text-lg'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        Menampilkan 1-8 dari 24 data
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-2 text-slate-400 hover:text-slate-600 rounded">
                            <i class='bx bx-chevron-left'></i>
                        </button>
                        <button class="w-8 h-8 bg-primary-600 text-white rounded text-sm">1</button>
                        <button class="w-8 h-8 text-slate-600 hover:bg-slate-100 rounded text-sm">2</button>
                        <button class="w-8 h-8 text-slate-600 hover:bg-slate-100 rounded text-sm">3</button>
                        <button class="p-2 text-slate-400 hover:text-slate-600 rounded">
                            <i class='bx bx-chevron-right'></i>
                        </button>
                    </div>
                </div>
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
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Produk *</label>
                                <select name="product_id" required 
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih Produk</option>
                                    <option value="1">Smartphone X1</option>
                                    <option value="2">Tablet Pro 12</option>
                                    <option value="3">Laptop Ultra</option>
                                    <option value="4">Smart Watch S2</option>
                                    <option value="5">Wireless Earbuds</option>
                                    <option value="6">Power Bank 20k</option>
                                </select>
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
                                    <select name="materials[0][material_id]" 
                                            class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Material</option>
                                        <option value="1">Plastic ABS</option>
                                        <option value="2">Plastic PP</option>
                                        <option value="3">Steel Sheet</option>
                                        <option value="4">Electronic Parts</option>
                                        <option value="5">Packaging Box</option>
                                    </select>
                                    <input type="number" name="materials[0][quantity]" min="1" 
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

    <script>
        // Modal Functions
        function openCreateModal() {
            console.log('Opening create modal...');
            document.getElementById('createModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateModal() {
            console.log('Closing create modal...');
            document.getElementById('createModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(productionId) {
            console.log('Edit production:', productionId);
            openCreateModal(); // Reuse create modal for edit for now
        }

        // Material Management
        let materialCount = 1;

        function addMaterial() {
            const container = document.getElementById('materialRequirements');
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center gap-3 material-row';
            newRow.innerHTML = `
                <select name="materials[${materialCount}][material_id]" 
                        class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Pilih Material</option>
                    <option value="1">Plastic ABS</option>
                    <option value="2">Plastic PP</option>
                    <option value="3">Steel Sheet</option>
                    <option value="4">Electronic Parts</option>
                    <option value="5">Packaging Box</option>
                </select>
                <input type="number" name="materials[${materialCount}][quantity]" min="1" 
                       class="w-32 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Qty">
                <select name="materials[${materialCount}][unit]"
                        class="w-24 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="kg">kg</option>
                    <option value="pcs">pcs</option>
                    <option value="roll">roll</option>
                    <option value="unit">unit</option>
                </select>
                <button type="button" onclick="removeMaterial(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                    <i class='bx bx-trash'></i>
                </button>
            `;
            container.appendChild(newRow);
            materialCount++;
        }

        function removeMaterial(button) {
            if (document.querySelectorAll('.material-row').length > 1) {
                button.closest('.material-row').remove();
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Create button event listener
            const createBtn = document.getElementById('createProductionBtn');
            if (createBtn) {
                createBtn.addEventListener('click', openCreateModal);
            }

            // Form submission
            const productionForm = document.getElementById('productionForm');
            if (productionForm) {
                productionForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData);
                    
                    // Simulate API call
                    console.log('Production data:', data);
                    
                    // Show success message
                    alert('Produksi berhasil dibuat!');
                    closeCreateModal();
                    
                    // In real application, you would refresh the table data here
                });
            }

            // Close modal on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeCreateModal();
                }
            });
        });
    </script>
</x-layouts.admin>
