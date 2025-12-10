<x-layouts.admin>
    <x-slot name="title">Master Sparepart</x-slot>

    <div x-data="sparepartData()" x-init="init()" class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold">Master Sparepart</h1>
                <p class="text-slate-600 text-sm">Kelola data sparepart dan stok</p>
            </div>
            @hasPermission('inventaris.sparepart.create')
            
            <button x-on:click="openAddModal()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                <i class='bx bx-plus-circle text-lg'></i> Tambah Sparepart
            </button>
           @endhasPermission
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-card">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <i class='bx bx-package text-2xl text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Total Sparepart</p>
                        <p class="text-2xl font-bold" x-text="stats.total">0</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-card">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-green-100 rounded-xl">
                        <i class='bx bx-check-circle text-2xl text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Tersedia</p>
                        <p class="text-2xl font-bold" x-text="stats.tersedia">0</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-card">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-yellow-100 rounded-xl">
                        <i class='bx bx-error text-2xl text-yellow-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Stok Minimum</p>
                        <p class="text-2xl font-bold" x-text="stats.minimum">0</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-card">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-red-100 rounded-xl">
                        <i class='bx bx-x-circle text-2xl text-red-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Habis</p>
                        <p class="text-2xl font-bold" x-text="stats.habis">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table id="sparepart-table" class="w-full text-sm display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Kode</th>
                                <th>Nama Sparepart</th>
                                <th>Merk</th>
                                <th class="text-right">Harga</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Min</th>
                                <th class="text-center">Status Stok</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah/Edit Sparepart -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal()"></div>

                <!-- Modal panel -->
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-slate-900" x-text="modalTitle">Tambah Sparepart</h3>
                            <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
                                <i class='bx bx-x text-2xl'></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="saveSparepart()" class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Kode Sparepart -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Kode Sparepart <span class="text-red-500">*</span></label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="form.kode_sparepart" required :readonly="editMode" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" :class="{'bg-slate-100': editMode}" placeholder="Contoh: SP001">
                                    <button type="button" x-show="!editMode" @click="generateKodeSparepart()" class="px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200" title="Generate Kode">
                                        <i class='bx bx-refresh'></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Nama Sparepart -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Sparepart <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.nama_sparepart" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="Nama sparepart">
                            </div>

                            <!-- Merk -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Merk</label>
                                <input type="text" x-model="form.merk" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="Merk sparepart">
                            </div>

                            <!-- Satuan -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                <select x-model="form.satuan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500">
                                    <option value="">Pilih Satuan</option>
                                    <option value="pcs">Pcs</option>
                                    <option value="unit">Unit</option>
                                    <option value="set">Set</option>
                                    <option value="box">Box</option>
                                    <option value="pack">Pack</option>
                                    <option value="meter">Meter</option>
                                    <option value="liter">Liter</option>
                                </select>
                            </div>

                            <!-- Harga -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Harga <span class="text-red-500">*</span></label>
                                <input type="number" x-model="form.harga" required min="0" step="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="0">
                            </div>

                            <!-- Stok Awal (hanya untuk tambah) -->
                            <div x-show="!editMode">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Stok Awal <span class="text-red-500">*</span></label>
                                <input type="number" x-model="form.stok" :required="!editMode" min="0" step="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="0">
                                <p class="text-xs text-slate-500 mt-1">Stok awal saat menambah sparepart baru</p>
                            </div>

                            <!-- Info Stok (hanya untuk edit) -->
                            <div x-show="editMode" class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-xs text-blue-600 mb-1">Stok Saat Ini</p>
                                <p class="text-lg font-bold text-blue-700" x-text="(form.stok || 0) + ' ' + (form.satuan || '')"></p>
                                <p class="text-xs text-slate-500 mt-1">Gunakan tombol "Adjust Stok" untuk mengubah stok</p>
                            </div>

                            <!-- Stok Minimum -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Stok Minimum <span class="text-red-500">*</span></label>
                                <input type="number" x-model="form.stok_minimum" required min="0" step="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="0">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                                <select x-model="form.is_active" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>

                            <!-- Spesifikasi -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Spesifikasi</label>
                                <textarea x-model="form.spesifikasi" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="Spesifikasi teknis sparepart"></textarea>
                            </div>

                            <!-- Keterangan -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                                <textarea x-model="form.keterangan" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="Keterangan tambahan"></textarea>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                            <button type="button" @click="closeModal()" class="px-4 py-2 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200">
                                Batal
                            </button>
                            <button type="submit" :disabled="loading" class="px-4 py-2 text-white bg-primary-600 rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!loading">Simpan</span>
                                <span x-show="loading">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Detail Sparepart -->
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeDetailModal()"></div>

                <!-- Modal panel -->
                <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-blue-50 to-blue-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-500 rounded-lg">
                                    <i class='bx bx-package text-2xl text-white'></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Detail Sparepart</h3>
                                    <p class="text-sm text-slate-600" x-text="detailData ? detailData.kode_sparepart : ''"></p>
                                </div>
                            </div>
                            <button @click="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
                                <i class='bx bx-x text-2xl'></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4" x-show="detailData">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Kode Sparepart -->
                            <div class="p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Kode Sparepart</p>
                                <p class="text-sm font-semibold text-slate-900" x-text="detailData?.kode_sparepart || '-'"></p>
                            </div>

                            <!-- Nama Sparepart -->
                            <div class="p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Nama Sparepart</p>
                                <p class="text-sm font-semibold text-slate-900" x-text="detailData?.nama_sparepart || '-'"></p>
                            </div>

                            <!-- Merk -->
                            <div class="p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Merk</p>
                                <p class="text-sm font-semibold text-slate-900" x-text="detailData?.merk || '-'"></p>
                            </div>

                            <!-- Satuan -->
                            <div class="p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Satuan</p>
                                <p class="text-sm font-semibold text-slate-900 uppercase" x-text="detailData?.satuan || '-'"></p>
                            </div>

                            <!-- Harga -->
                            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                                <p class="text-xs text-green-600 mb-1">Harga</p>
                                <p class="text-lg font-bold text-green-700" x-text="detailData ? 'Rp ' + new Intl.NumberFormat('id-ID').format(detailData.harga) : '-'"></p>
                            </div>

                            <!-- Stok -->
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-xs text-blue-600 mb-1">Stok Tersedia</p>
                                <p class="text-lg font-bold text-blue-700" x-text="(detailData?.stok || 0) + ' ' + (detailData?.satuan || '')"></p>
                            </div>

                            <!-- Stok Minimum -->
                            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-xs text-yellow-600 mb-1">Stok Minimum</p>
                                <p class="text-lg font-bold text-yellow-700" x-text="(detailData?.stok_minimum || 0) + ' ' + (detailData?.satuan || '')"></p>
                            </div>

                            <!-- Status -->
                            <div class="p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Status</p>
                                <span x-show="detailData?.is_active" class="inline-block px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Aktif</span>
                                <span x-show="!detailData?.is_active" class="inline-block px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Nonaktif</span>
                            </div>

                            <!-- Spesifikasi -->
                            <div class="md:col-span-2 p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Spesifikasi</p>
                                <p class="text-sm text-slate-900" x-text="detailData?.spesifikasi || '-'"></p>
                            </div>

                            <!-- Keterangan -->
                            <div class="md:col-span-2 p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Keterangan</p>
                                <p class="text-sm text-slate-900" x-text="detailData?.keterangan || '-'"></p>
                            </div>

                            <!-- Created At -->
                            <div class="p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Dibuat Pada</p>
                                <p class="text-sm text-slate-900" x-text="detailData?.created_at ? new Date(detailData.created_at).toLocaleString('id-ID') : '-'"></p>
                            </div>

                            <!-- Updated At -->
                            <div class="p-4 bg-slate-50 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">Diupdate Pada</p>
                                <p class="text-sm text-slate-900" x-text="detailData?.updated_at ? new Date(detailData.updated_at).toLocaleString('id-ID') : '-'"></p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                            <button type="button" @click="closeDetailModal()" class="px-4 py-2 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200">
                                Tutup
                            </button>
                            <button type="button" @click="closeDetailModal(); openEditModal(detailData.id_sparepart)" class="px-4 py-2 text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                                <i class='bx bx-edit'></i> Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Adjust Stok -->
        <div x-show="showAdjustModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showAdjustModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeAdjustModal()"></div>

                <!-- Modal panel -->
                <div x-show="showAdjustModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-purple-50 to-purple-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-purple-500 rounded-lg">
                                    <i class='bx bx-package text-2xl text-white'></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Penyesuaian Stok</h3>
                                    <p class="text-sm text-slate-600" x-text="adjustData ? adjustData.kode_sparepart + ' - ' + adjustData.nama_sparepart : ''"></p>
                                </div>
                            </div>
                            <button @click="closeAdjustModal()" class="text-slate-400 hover:text-slate-600">
                                <i class='bx bx-x text-2xl'></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4" x-show="adjustData">
                        <!-- Current Stock Info -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-xs text-blue-600 mb-1">Stok Saat Ini</p>
                                <p class="text-2xl font-bold text-blue-700" x-text="(adjustData?.stok || 0) + ' ' + (adjustData?.satuan || '')"></p>
                            </div>
                            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-xs text-yellow-600 mb-1">Stok Minimum</p>
                                <p class="text-2xl font-bold text-yellow-700" x-text="(adjustData?.stok_minimum || 0) + ' ' + (adjustData?.satuan || '')"></p>
                            </div>
                            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                                <p class="text-xs text-green-600 mb-1">Harga Satuan</p>
                                <p class="text-2xl font-bold text-green-700" x-text="adjustData ? 'Rp ' + new Intl.NumberFormat('id-ID').format(adjustData.harga) : '-'"></p>
                            </div>
                        </div>

                        <!-- Adjustment Form -->
                        <form @submit.prevent="saveAdjustment()" class="mb-6 p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <h4 class="font-semibold text-slate-900 mb-4">Form Penyesuaian Stok</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Tipe -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                                    <select x-model="adjustForm.tipe" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500">
                                        <option value="tambah">Tambah Stok</option>
                                        <option value="kurang">Kurangi Stok</option>
                                    </select>
                                </div>

                                <!-- Jumlah -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                                    <input type="number" x-model="adjustForm.jumlah" required min="1" step="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="0">
                                </div>

                                <!-- Keterangan -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="adjustForm.keterangan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-200 focus:border-primary-500" placeholder="Alasan penyesuaian">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-4">
                                <button type="submit" :disabled="loading" class="w-full px-4 py-2 text-white bg-purple-600 rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!loading">
                                        <i class='bx bx-save'></i> Simpan Penyesuaian
                                    </span>
                                    <span x-show="loading">Menyimpan...</span>
                                </button>
                            </div>
                        </form>

                        <!-- Stock Logs Table -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-slate-900">Riwayat Perubahan Stok</h4>
                                <span class="text-xs text-slate-500" x-text="'Total: ' + adjustLogs.length + ' log'"></span>
                            </div>
                            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tanggal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tipe</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Stok Lama</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Perubahan</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Stok Baru</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Keterangan</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">User</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        <template x-if="!adjustLogs || adjustLogs.length === 0">
                                            <tr>
                                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                                    <i class='bx bx-info-circle text-3xl mb-2'></i>
                                                    <p>Belum ada riwayat perubahan stok</p>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="adjustLogs && adjustLogs.length > 0">
                                            <template x-for="(log, index) in adjustLogs" :key="log.id_log || index">
                                                <tr class="hover:bg-slate-50">
                                                    <td class="px-4 py-3 text-xs text-slate-600" x-text="new Date(log.created_at).toLocaleString('id-ID')"></td>
                                                    <td class="px-4 py-3">
                                                        <span x-show="log.tipe_perubahan === 'stok'" class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full">Stok</span>
                                                        <span x-show="log.tipe_perubahan === 'harga'" class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Harga</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-slate-900" x-text="log.nilai_lama"></td>
                                                    <td class="px-4 py-3 text-center font-semibold" :class="log.selisih > 0 ? 'text-green-600' : 'text-red-600'">
                                                        <span x-text="(log.selisih > 0 ? '+' : '') + log.selisih"></span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center font-semibold text-slate-900" x-text="log.nilai_baru"></td>
                                                    <td class="px-4 py-3 text-xs text-slate-600" x-text="log.keterangan"></td>
                                                    <td class="px-4 py-3 text-xs text-slate-600" x-text="log.user ? log.user.name : '-'"></td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                            <button type="button" @click="closeAdjustModal()" class="px-4 py-2 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.sparepartRoutes = {
            data: '{{ route('admin.inventaris.sparepart.data') }}',
            store: '{{ route('admin.inventaris.sparepart.store') }}',
            show: '{{ route('admin.inventaris.sparepart.show', ':id') }}',
            update: '{{ route('admin.inventaris.sparepart.update', ':id') }}',
            destroy: '{{ route('admin.inventaris.sparepart.destroy', ':id') }}',
            adjust: '{{ route('admin.inventaris.sparepart.adjust', ':id') }}',
            logs: '{{ route('admin.inventaris.sparepart.logs', ':id') }}',
            search: '{{ route('admin.inventaris.sparepart.search') }}',
            generateKode: '{{ route('admin.inventaris.sparepart.generate-kode') }}'
        };
    </script>
    <script src="{{ asset('js/sparepart.js') }}?v={{ time() }}"></script>
    @endpush

    @push('styles')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Alpine.js Cloak */
        [x-cloak] {
            display: none !important;
        }

        /* DataTables Custom Styling */
        #sparepart-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        #sparepart-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        
        #sparepart-table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        /* DataTables Controls */
        .dataTables_wrapper .dataTables_length select {
            padding: 6px 32px 6px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: white;
            font-size: 14px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-left: 8px;
            font-size: 14px;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Pagination */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            margin: 0 2px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            color: #475569;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #1e293b;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Info Text */
        .dataTables_wrapper .dataTables_info {
            color: #64748b;
            font-size: 14px;
        }
        
        /* Processing */
        .dataTables_wrapper .dataTables_processing {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
    @endpush
</x-layouts.admin>
