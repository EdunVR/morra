<x-layouts.admin :title="'Inventaris / Bahan'">
  <div x-data="bahanCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Bahan</h1>
        <p class="text-slate-600 text-sm">Kelola daftar bahan/material.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @hasPermission('inventaris.bahan.create')
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Bahan
        </button>
        @endhasPermission
        
        @hasPermission('inventaris.bahan.export')
        <button x-on:click="exportPdf()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export PDF
        </button>
        <button x-on:click="exportExcel()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export Excel
        </button>
        @endhasPermission
        
        @hasPermission('inventaris.bahan.import')
        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50 cursor-pointer">
          <i class='bx bx-import text-lg'></i><span>Import Excel</span>
          <input type="file" class="hidden" accept=".xlsx,.xls,.csv" x-on:change="importExcel($event)">
        </label>
        <button x-on:click="downloadTemplate()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-download text-lg'></i> Template
        </button>
        @endhasPermission
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <!-- Search -->
        <div class="lg:col-span-5">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari nama bahan, kode, outlet, merk…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <!-- Filter Outlet -->
        <div class="lg:col-span-4">
          <select x-model="outletFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Outlet: Semua</option>
            <template x-for="o in uniqueOutlets()" :key="o"><option :value="o" x-text="o"></option></template>
          </select>
        </div>
        <!-- Filter Satuan -->
        <div class="lg:col-span-3">
          <select x-model="unitFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Satuan: Semua</option>
            <template x-for="u in uniqueUnits()" :key="u"><option :value="u" x-text="u"></option></template>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <!-- Sort -->
        <div class="grid grid-cols-2 gap-2 lg:col-span-6">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="name">Nama</option>
            <option value="outlet">Outlet</option>
            <option value="brand">Merk</option>
            <option value="stock">Stok</option>
            <option value="unit">Satuan</option>
          </select>
          <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="asc">Naik</option><option value="desc">Turun</option>
          </select>
        </div>

        <!-- Toggle View -->
        <div class="lg:col-span-2 lg:col-start-11">
          <div class="flex rounded-xl border border-slate-200 overflow-hidden">
            <button x-on:click="view='grid'"  :class="view==='grid'  ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="flex-1 px-3 py-2 text-sm">Grid</button>
            <button x-on:click="view='table'" :class="view==='table' ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="flex-1 px-3 py-2 text-sm">Tabel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data...</span>
      </div>
    </div>

    <!-- GRID -->
    <div x-show="view==='grid' && !loading">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <template x-for="m in bahan" :key="m.id">
          <div class="rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition p-4">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100 shrink-0">
                <i class='bx bx-package text-2xl'></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-semibold truncate" x-text="m.name"></div>
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="m.stock>0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-600 border border-slate-200' "
                        x-text="m.stock>0 ? 'Ready' : 'Habis'"></span>
                </div>
                <div class="text-[12px] text-slate-500 mt-0.5">
                  <span x-text="m.outlet"></span> • <span class="font-mono" x-text="m.code"></span>
                </div>
                <div class="mt-2 text-sm">
                  <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 border border-emerald-200">
                    <i class='bx bx-cube'></i><span x-text="m.stock"></span><span x-text="m.unit"></span>
                  </span>
                  <span class="ml-2 text-slate-600">Merk: <span class="font-medium" x-text="m.brand || '-'"></span></span>
                </div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <button x-on:click="showHargaBeli(m)" class="flex-1 rounded-lg bg-emerald-600 text-white px-3 py-2 hover:bg-emerald-700 text-sm">
                <i class='bx bx-show'></i> Harga Beli
              </button>
              @hasPermission('inventaris.bahan.update')
              <button x-on:click="openEdit(m)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm">
                <i class='bx bx-edit-alt'></i> Edit
              </button>
              @endhasPermission
              @hasPermission('inventaris.bahan.delete')
              <button x-on:click="confirmDelete(m)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3py-2 hover:bg-red-50 text-sm">
                <i class='bx bx-trash'></i> Hapus
              </button>
              @endhasPermission
            </div>
          </div>
        </template>
      </div>
      <div x-show="bahan.length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- TABLE -->
    <div x-show="view==='table' && !loading">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3 w-12">No</th>
              <th class="text-left px-4 py-3">Outlet</th>
              <th class="text-left px-4 py-3">Nama Bahan</th>
              <th class="text-left px-4 py-3">Merk</th>
              <th class="text-left px-4 py-3">Stok Total</th>
              <th class="text-left px-4 py-3">Satuan</th>
              <th class="text-left px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(m,i) in bahan" :key="m.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3" x-text="i+1"></td>
                <td class="px-4 py-3" x-text="m.outlet"></td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-emerald-600 text-white text-xs" x-text="m.code"></span>
                    <span x-text="m.name"></span>
                  </div>
                </td>
                <td class="px-4 py-3" x-text="m.brand || '-'"></td>
                <td class="px-4 py-3">
                  <span :class="m.stock>0 ? 'text-green-600' : 'text-red-600'" x-text="m.stock"></span>
                </td>
                <td class="px-4 py-3" x-text="m.unit"></td>
                <td class="px-4 py-3">
                  <div class="flex gap-2">
                    <button x-on:click="showHargaBeli(m)" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 text-white px-3 py-1.5 hover:bg-emerald-700 text-sm">
                      <i class='bx bx-show'></i> Harga Beli
                    </button>
                    @hasPermission('inventaris.bahan.update')
                    <button x-on:click="openEdit(m)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                      <i class='bx bx-edit-alt'></i>
                    </button>
                    @endhasPermission
                    @hasPermission('inventaris.bahan.delete')
                    <button x-on:click="confirmDelete(m)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                      <i class='bx bx-trash'></i>
                    </button>
                    @endhasPermission
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="bahan.length===0"><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL: Tambah/Edit -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="closeForm()" class="w-full max-w-3xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Bahan' : 'Tambah Bahan'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Kode Bahan <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.code" placeholder="MAT-001" 
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50" readonly>
              <div class="text-xs text-slate-500 mt-1">Kode bahan digenerate otomatis</div>
              <div x-show="errors.kode_bahan" class="text-red-500 text-xs mt-1" x-text="errors.kode_bahan"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Bahan <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.name" placeholder="Nama bahan" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.nama_bahan" class="text-red-500 text-xs mt-1" x-text="errors.nama_bahan"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.outlet" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Outlet —</option>
                <template x-for="o in outlets" :key="o.id">
                  <option :value="o.id" x-text="o.name"></option>
                </template>
              </select>
              <div x-show="errors.id_outlet" class="text-red-500 text-xs mt-1" x-text="errors.id_outlet"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Merk</label>
              <input type="text" x-model.trim="form.brand" placeholder="-" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.merk" class="text-red-500 text-xs mt-1" x-text="errors.merk"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Satuan <span class="text-red-500">*</span></label>
              <select x-model="form.unit" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Satuan —</option>
                <template x-for="s in satuanList" :key="s.id">
                  <option :value="s.id" x-text="s.name"></option>
                </template>
              </select>
              <div x-show="errors.id_satuan" class="text-red-500 text-xs mt-1" x-text="errors.id_satuan"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Stok Total</label>
              <input type="number" min="0" x-model.number="form.stock" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" readonly>
              <div class="text-xs text-slate-500 mt-1">Stok dihitung otomatis dari detail harga beli</div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Catatan (opsional)</label>
              <textarea x-model.trim="form.note" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
              <div x-show="errors.catatan" class="text-red-500 text-xs mt-1" x-text="errors.catatan"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" x-model="form.is_active" class="rounded border-slate-300">
                <span class="text-sm text-slate-700">Aktif</span>
              </label>
            </div>
          </div>
        </div>

        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeForm()">Batal</button>
          <button x-on:click="submitForm()" :disabled="saving" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="saving" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
            <span x-show="!saving">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Bahan?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm"><span class="font-medium" x-text="toDelete?.name"></span> • <span class="font-mono text-slate-600" x-text="toDelete?.code"></span></div>
            <div class="text-xs text-slate-500 mt-1" x-text="'Outlet: ' + (toDelete?.outlet || '-') + ' • Stok: ' + (toDelete?.stock || 0) + ' ' + (toDelete?.unit || '') "></div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="toDelete=null">Batal</button>
          <button x-on:click="deleteNow()" :disabled="deleting" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
            <span x-show="deleting" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menghapus...
            </span>
            <span x-show="!deleting">Hapus</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Harga Beli -->
    <div x-show="showHargaModal" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="showHargaModal=false" class="w-full max-w-4xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate">Detail Harga Beli - <span x-text="selectedBahan?.name"></span></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="showHargaModal=false">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>
        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="text-center text-slate-500 py-8">
            <i class='bx bx-info-circle text-4xl mb-2'></i>
            <p>Fitur detail harga beli akan diimplementasikan selanjutnya</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition.opacity class="fixed top-4 right-4 z-50">
      <div :class="toastType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" 
           class="px-4 py-3 rounded-xl border shadow-lg max-w-sm">
        <div class="flex items-center gap-2">
          <i :class="toastType === 'success' ? 'bx bx-check-circle text-green-600' : 'bx bx-error-circle text-red-600'"></i>
          <span x-text="toastMessage"></span>
        </div>
      </div>
    </div>
  </div>

  <script>
    function bahanCrud(){
      return {
        // State management
        bahan: [],
        outlets: [],
        satuanList: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Filters and search
        search: '',
        outletFilter: 'ALL',
        unitFilter: 'ALL',
        sortKey: 'name',
        sortDir: 'asc',
        view: 'table',
        
        // Form state
        showForm: false,
        form: { 
          id: null, 
          code: '', 
          name: '', 
          outlet: '', 
          brand: '', 
          stock: 0, 
          unit: '', 
          note: '', 
          is_active: true 
        },
        errors: {},
        
        // Delete confirmation
        toDelete: null,

        // Harga beli modal
        showHargaModal: false,
        selectedBahan: null,
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init(){
          try {
            await Promise.all([
              this.fetchData(),
              this.fetchOutlets(),
              this.fetchSatuan()
            ]);
          } catch (error) {
            console.error('Error during initialization:', error);
          }
        },

        async fetchData(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              outlet_filter: this.outletFilter,
              unit_filter: this.unitFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            const response = await fetch(`{{ route('admin.inventaris.bahan.data') }}?${params}`);
            const data = await response.json();
            
            this.bahan = data.data.map(item => ({
              id: item.id_bahan || item.id,
              code: item.code || item.kode_bahan,
              name: item.name || item.nama_bahan,
              outlet: item.outlet || item.nama_outlet,
              brand: item.brand || item.merk,
              stock: item.stock || item.harga_bahan_sum_stok || 0,
              unit: item.unit || item.nama_satuan,
              note: item.note || item.catatan || '',
              is_active: item.is_active !== undefined ? item.is_active : true
            }));
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchOutlets(){
          try {
            const response = await fetch('{{ route("admin.inventaris.bahan.outlets") }}');
            const data = await response.json();
            this.outlets = Object.entries(data).map(([id, name]) => ({ id, name }));
          } catch (error) {
            console.error('Error fetching outlets:', error);
          }
        },

        async fetchSatuan(){
          try {
            const response = await fetch('{{ route("admin.inventaris.bahan.satuan") }}');
            const data = await response.json();
            this.satuanList = Object.entries(data).map(([id, name]) => ({ id, name }));
          } catch (error) {
            console.error('Error fetching satuan:', error);
          }
        },

        async openCreate(){ 
          try {
            ModalLoader.show();
            const response = await fetch('{{ route("admin.inventaris.bahan.generate-kode") }}');
            const data = await response.json();
            
            this.form = { 
              id: null, 
              code: data.kode_bahan,
              name: '', 
              outlet: '', 
              brand: '', 
              stock: 0, 
              unit: '', 
              note: '', 
              is_active: true 
            }; 
          } catch (error) {
            console.error('Error generating code:', error);
            this.form = { 
              id: null, 
              code: '', 
              name: '', 
              outlet: '', 
              brand: '', 
              stock: 0, 
              unit: '', 
              note: '', 
              is_active: true 
            }; 
          } finally {
              ModalLoader.hide();
          }
          
          this.errors = {};
          this.showForm = true; 
        },

        async openEdit(item){ 
          try {
            ModalLoader.show();
            const response = await fetch(`{{ route('admin.inventaris.bahan.edit', '') }}/${item.id}`);
            const data = await response.json();
            
            this.form = { 
              id: data.id,
              code: data.code, 
              name: data.name, 
              outlet: data.outlet, 
              brand: data.brand, 
              stock: data.stock, 
              unit: data.unit, 
              note: data.note, 
              is_active: data.is_active 
            }; 
          } catch (error) {
            console.error('Error fetching item:', error);
            this.showToastMessage('Gagal memuat data bahan', 'error');
            return;
          } finally {
              ModalLoader.hide();
          }
          
          this.errors = {};
          this.showForm = true; 
        },

        closeForm(){ 
          this.showForm = false; 
          this.errors = {};
        },

        async submitForm(){
          this.saving = true;
          this.errors = {};

          try {
            const url = this.form.id 
              ? `{{ route('admin.inventaris.bahan.update', '') }}/${this.form.id}`
              : '{{ route("admin.inventaris.bahan.store") }}';

            const method = this.form.id ? 'PUT' : 'POST';

            const formData = {
              kode_bahan: this.form.code,
              nama_bahan: this.form.name,
              id_outlet: this.form.outlet,
              id_satuan: this.form.unit,
              merk: this.form.brand,
              catatan: this.form.note,
              is_active: this.form.is_active
            };

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
            } else {
              if (result.errors) {
                this.errors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving data:', error);
            this.showToastMessage('Gagal menyimpan data', 'error');
          } finally {
            this.saving = false;
          }
        },

        confirmDelete(item){ 
          this.toDelete = item; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`{{ route('admin.inventaris.bahan.destroy', '') }}/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
              this.toDelete = null;
              await this.fetchData();
            } else {
              this.showToastMessage(result.error || 'Gagal menghapus data', 'error');
            }
          } catch (error) {
            console.error('Error deleting data:', error);
            this.showToastMessage('Gagal menghapus data', 'error');
          } finally {
            this.deleting = false;
          }
        },

        showHargaBeli(bahan) {
          this.selectedBahan = bahan;
          this.showHargaModal = true;
        },

        exportPdf(){
          const params = new URLSearchParams({
            outlet: this.outletFilter,
            unit: this.unitFilter
          });
          window.open(`{{ route('admin.inventaris.bahan.export.pdf') }}?${params}`, '_blank');
        },

        exportExcel(){
          const params = new URLSearchParams({
            outlet: this.outletFilter,
            unit: this.unitFilter
          });
          window.open(`{{ route('admin.inventaris.bahan.export.excel') }}?${params}`, '_blank');
        },

        async importExcel(event){
          const file = event.target.files[0];
          if (!file) return;

          const formData = new FormData();
          formData.append('file', file);

          try {
            const response = await fetch('{{ route("admin.inventaris.bahan.import.excel") }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: formData
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil diimport', 'success');
              await this.fetchData();
            } else {
              this.showToastMessage(result.error || 'Gagal mengimport data', 'error');
            }
          } catch (error) {
            console.error('Error importing data:', error);
            this.showToastMessage('Gagal mengimport data', 'error');
          } finally {
            event.target.value = '';
          }
        },

        downloadTemplate(){
          window.open('{{ route("admin.inventaris.bahan.download-template") }}', '_blank');
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        },

        // Helper methods untuk filter
        uniqueOutlets() {
          return Array.from(new Set(this.bahan.map(m => m.outlet))).filter(Boolean).sort((a,b) => a.localeCompare(b));
        },

        uniqueUnits() {
          return Array.from(new Set(this.bahan.map(m => m.unit))).filter(Boolean).sort((a,b) => a.localeCompare(b));
        }
      };
    }
  </script>
</x-layouts.admin>
