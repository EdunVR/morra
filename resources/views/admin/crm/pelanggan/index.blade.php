<x-layouts.admin :title="'Manajemen Pelanggan'">
  <div x-data="customerManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Manajemen Pelanggan</h1>
        <p class="text-slate-600 text-sm">Kelola data pelanggan dan informasi kontak</p>
      </div>

      <div class="flex flex-wrap gap-2">
        {{-- View Toggle --}}
        <div class="inline-flex rounded-xl border border-slate-200 bg-white p-1">
          <button @click="viewMode = 'grid'" 
                  :class="viewMode === 'grid' ? 'bg-primary-100 text-primary-700' : 'text-slate-600 hover:bg-slate-50'"
                  class="px-3 py-1.5 rounded-lg transition-colors">
            <i class='bx bx-grid-alt'></i>
          </button>
          <button @click="viewMode = 'table'" 
                  :class="viewMode === 'table' ? 'bg-primary-100 text-primary-700' : 'text-slate-600 hover:bg-slate-50'"
                  class="px-3 py-1.5 rounded-lg transition-colors">
            <i class='bx bx-list-ul'></i>
          </button>
        </div>

        {{-- Filter Outlet --}}
        <select x-model="filters.outlet" @change="loadData()" 
                class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <option value="all">Semua Outlet</option>
          @foreach($outlets as $outlet)
            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
          @endforeach
        </select>

        {{-- Filter Tipe --}}
        <select x-model="filters.tipe" @change="loadData()" 
                class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <option value="all">Semua Tipe</option>
          @foreach($tipes as $tipe)
            <option value="{{ $tipe->id_tipe }}">{{ $tipe->nama_tipe }}</option>
          @endforeach
        </select>

        @hasPermission('crm.pelanggan.create')
        <button @click="openCreateModal()" 
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 h-10 hover:bg-primary-700">
          <i class='bx bx-plus'></i> Tambah Pelanggan
        </button>
        @endhasPermission

        @hasPermission('crm.pelanggan.import')
        {{-- Import Button --}}
        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 cursor-pointer">
          <i class='bx bx-import'></i>
          <span>Import</span>
          <input type="file" class="hidden" accept=".xlsx,.xls" @change="importExcel($event)">
        </label>
        @endhasPermission
        
        @hasPermission('crm.pelanggan.export')
        {{-- Export Dropdown --}}
        <div x-data="{ exportOpen: false }" class="relative">
          <button @click="exportOpen = !exportOpen" 
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
            <i class='bx bx-export'></i>
            <span>Export</span>
            <i class='bx bx-chevron-down text-sm'></i>
          </button>

          <div x-show="exportOpen" 
               @click.away="exportOpen = false"
               x-transition
               class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg z-10">
            <button @click="exportExcel(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-t-xl">
              <i class='bx bx-file text-green-600'></i>
              <span>Export ke XLSX</span>
            </button>
            <button @click="exportPdf(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 border-t border-slate-100">
              <i class='bx bxs-file-pdf text-red-600'></i>
              <span>Export ke PDF</span>
            </button>
            <button @click="downloadTemplate(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-b-xl border-t border-slate-100">
              <i class='bx bx-download text-blue-600'></i>
              <span>Download Template</span>
            </button>
          </div>
        </div>
        @endhasPermission
      </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-user text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="statistics.total_customers">0</div>
            <div class="text-sm text-slate-600">Total Pelanggan</div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
            <i class='bx bx-money text-2xl text-red-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold text-red-600" x-text="formatRupiah(statistics.total_piutang)">Rp 0</div>
            <div class="text-sm text-slate-600">Total Piutang</div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-store text-2xl text-green-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold text-green-600">{{ $outlets->count() }}</div>
            <div class="text-sm text-slate-600">Outlet Aktif</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Search Bar --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="relative">
        <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
        <input type="text" 
               x-model="filters.search" 
               @input.debounce.500ms="loadData()" 
               placeholder="Cari nama, telepon, alamat, atau kode member..."
               class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
      </div>
    </div>

    {{-- Grid View --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <template x-for="customer in customers" :key="customer.id_member">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-lg transition-shadow">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-primary-50 text-primary-700" 
                      x-text="customer.kode_display"></span>
              </div>
              <h3 class="font-semibold text-lg" x-text="customer.nama"></h3>
              <p class="text-sm text-slate-600" x-text="customer.tipe_nama"></p>
            </div>
          </div>

          <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-phone text-slate-400'></i>
              <span x-text="customer.telepon"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-map text-slate-400'></i>
              <span class="line-clamp-1" x-text="customer.alamat || '-'"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-store text-slate-400'></i>
              <span x-text="customer.outlet_nama"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-money text-red-400'></i>
              <span class="font-medium text-red-600" x-text="customer.total_piutang_formatted"></span>
            </div>
          </div>

          <div class="flex gap-2 pt-3 border-t border-slate-100">
            <button @click="viewCustomer(customer.id_member)" 
                    class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-center gap-1">
              <i class='bx bx-show'></i> Detail
            </button>
            @hasPermission('crm.pelanggan.update')
            <button @click="editCustomer(customer.id_member)" 
                    class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-amber-200 text-amber-700 hover:bg-amber-50 flex items-center justify-center gap-1">
              <i class='bx bx-edit'></i> Edit
            </button>
            @endhasPermission
            @hasPermission('crm.pelanggan.delete')
            <button @click="deleteCustomer(customer.id_member)" 
                    class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
              <i class='bx bx-trash'></i>
            </button>
            @endhasPermission
          </div>
        </div>
      </template>

      <div x-show="customers.length === 0" class="col-span-full text-center py-12 text-slate-500">
        <i class='bx bx-user-x text-5xl mb-2'></i>
        <p>Belum ada data pelanggan</p>
      </div>
    </div>

    {{-- Table View --}}
    <div x-show="viewMode === 'table'" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">No</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Kode</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Nama</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Telepon</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Alamat</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Tipe</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Outlet</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Piutang</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="(customer, index) in customers" :key="customer.id_member">
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3" x-text="index + 1"></td>
                <td class="px-4 py-3">
                  <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-primary-50 text-primary-700" 
                        x-text="customer.kode_display"></span>
                </td>
                <td class="px-4 py-3 font-medium" x-text="customer.nama"></td>
                <td class="px-4 py-3" x-text="customer.telepon"></td>
                <td class="px-4 py-3 max-w-xs truncate" x-text="customer.alamat || '-'"></td>
                <td class="px-4 py-3" x-text="customer.tipe_nama"></td>
                <td class="px-4 py-3" x-text="customer.outlet_nama"></td>
                <td class="px-4 py-3 font-medium text-red-600" x-text="customer.total_piutang_formatted"></td>
                <td class="px-4 py-3">
                  <div class="flex gap-1">
                    <button @click="viewCustomer(customer.id_member)" 
                            class="px-2 py-1 text-xs rounded-lg border border-slate-200 hover:bg-slate-50">
                      <i class='bx bx-show'></i>
                    </button>
                    @hasPermission('crm.pelanggan.update')
                    <button @click="editCustomer(customer.id_member)" 
                            class="px-2 py-1 text-xs rounded-lg border border-amber-200 text-amber-700 hover:bg-amber-50">
                      <i class='bx bx-edit'></i>
                    </button>
                    @endhasPermission
                    @hasPermission('crm.pelanggan.delete')
                    <button @click="deleteCustomer(customer.id_member)" 
                            class="px-2 py-1 text-xs rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
                      <i class='bx bx-trash'></i>
                    </button>
                    @endhasPermission
                  </div>
                </td>
              </tr>
            </template>

            <tr x-show="customers.length === 0">
              <td colspan="9" class="px-4 py-12 text-center text-slate-500">
                <i class='bx bx-user-x text-5xl mb-2'></i>
                <p>Belum ada data pelanggan</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>


    {{-- Create/Edit Modal --}}
    <div x-show="showModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" @click="closeModal()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 z-10">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-900" x-text="modalTitle">Tambah Pelanggan</h3>
            <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <form @submit.prevent="submitForm()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Pelanggan *</label>
                <input type="text" x-model="formData.nama" required
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Telepon *</label>
                <input type="text" x-model="formData.telepon" required
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Customer *</label>
                <select x-model="formData.id_tipe" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                  <option value="">Pilih Tipe</option>
                  @foreach($tipes as $tipe)
                    <option value="{{ $tipe->id_tipe }}">{{ $tipe->nama_tipe }}</option>
                  @endforeach
                </select>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Outlet *</label>
                <select x-model="formData.id_outlet" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                  <option value="">Pilih Outlet</option>
                  @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                  @endforeach
                </select>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                <textarea x-model="formData.alamat" rows="3"
                          class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
              </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
              <button type="button" @click="closeModal()" 
                      class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                Batal
              </button>
              <button type="submit" :disabled="loading"
                      class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                <span x-show="!loading">Simpan</span>
                <span x-show="loading">Menyimpan...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Detail Modal --}}
    <div x-show="showDetailModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" @click="closeDetailModal()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full p-6 z-10">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-900">Detail Pelanggan</h3>
            <button @click="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <div x-show="detailData" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-slate-600">Kode Member</p>
                <p class="font-semibold" x-text="detailData?.kode_member || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Nama</p>
                <p class="font-semibold" x-text="detailData?.nama || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Telepon</p>
                <p class="font-semibold" x-text="detailData?.telepon || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Tipe Customer</p>
                <p class="font-semibold" x-text="detailData?.tipe?.nama_tipe || '-'"></p>
              </div>
              <div class="col-span-2">
                <p class="text-sm text-slate-600">Alamat</p>
                <p class="font-semibold" x-text="detailData?.alamat || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Outlet</p>
                <p class="font-semibold" x-text="detailData?.outlet?.nama || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Total Piutang</p>
                <p class="font-semibold text-red-600" x-text="formatRupiah(detailData?.total_piutang || 0)"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    function customerManagement() {
      return {
        viewMode: 'grid', // default grid
        customers: [],
        showModal: false,
        showDetailModal: false,
        modalTitle: 'Tambah Pelanggan',
        loading: false,
        editMode: false,
        editId: null,
        filters: {
          outlet: 'all',
          tipe: 'all',
          search: ''
        },
        formData: {
          nama: '',
          telepon: '',
          alamat: '',
          id_tipe: '',
          id_outlet: ''
        },
        detailData: null,
        statistics: {
          total_customers: 0,
          total_piutang: 0,
          customers_by_tipe: []
        },

        init() {
          this.loadData();
          this.loadStatistics();
        },

        loadData() {
          const params = new URLSearchParams({
            outlet_filter: this.filters.outlet,
            tipe_filter: this.filters.tipe,
            search: this.filters.search
          });

          fetch(`{{ route("admin.crm.pelanggan.data") }}?${params}`)
            .then(res => res.json())
            .then(data => {
              if (data.data) {
                this.customers = data.data;
              }
            })
            .catch(err => console.error('Error loading data:', err));
        },

        loadStatistics() {
          fetch(`{{ route('admin.crm.pelanggan.statistics') }}?outlet_filter=${this.filters.outlet}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.statistics = data.data;
              }
            })
            .catch(err => console.error('Error loading statistics:', err));
        },

        openCreateModal() {
          this.editMode = false;
          this.modalTitle = 'Tambah Pelanggan';
          this.resetForm();
          this.showModal = true;
        },

        editCustomer(id) {
          this.editMode = true;
          this.editId = id;
          this.modalTitle = 'Edit Pelanggan';
          this.loadCustomerData(id);
          this.showModal = true;
        },

        loadCustomerData(id) {
          fetch(`{{ url('admin/crm/pelanggan') }}/${id}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.formData = {
                  nama: data.data.nama,
                  telepon: data.data.telepon,
                  alamat: data.data.alamat,
                  id_tipe: data.data.id_tipe,
                  id_outlet: data.data.id_outlet
                };
              }
            })
            .catch(err => console.error('Error loading customer:', err));
        },

        submitForm() {
          this.loading = true;
          const url = this.editMode 
            ? `{{ url('admin/crm/pelanggan') }}/${this.editId}`
            : '{{ route("admin.crm.pelanggan.store") }}';
          
          const method = this.editMode ? 'PUT' : 'POST';

          fetch(url, {
            method: method,
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(this.formData)
          })
          .then(res => res.json())
          .then(data => {
            this.loading = false;
            if (data.success) {
              alert(data.message);
              this.closeModal();
              this.loadData();
              this.loadStatistics();
            } else {
              alert(data.message || 'Terjadi kesalahan');
            }
          })
          .catch(error => {
            this.loading = false;
            alert('Terjadi kesalahan');
            console.error('Error:', error);
          });
        },

        viewCustomer(id) {
          fetch(`{{ url('admin/crm/pelanggan') }}/${id}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.detailData = data.data;
                this.showDetailModal = true;
              }
            })
            .catch(err => console.error('Error viewing customer:', err));
        },

        deleteCustomer(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) return;

          fetch(`{{ url('admin/crm/pelanggan') }}/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert(data.message);
              this.loadData();
              this.loadStatistics();
            } else {
              alert(data.message || 'Gagal menghapus pelanggan');
            }
          })
          .catch(err => {
            alert('Terjadi kesalahan');
            console.error('Error:', err);
          });
        },

        closeModal() {
          this.showModal = false;
          this.resetForm();
        },

        closeDetailModal() {
          this.showDetailModal = false;
          this.detailData = null;
        },

        resetForm() {
          this.formData = {
            nama: '',
            telepon: '',
            alamat: '',
            id_tipe: '',
            id_outlet: ''
          };
        },

        exportExcel() {
          window.location.href = `{{ route('admin.crm.pelanggan.export.excel') }}?outlet_filter=${this.filters.outlet}&tipe_filter=${this.filters.tipe}`;
        },

        exportPdf() {
          window.location.href = `{{ route('admin.crm.pelanggan.export.pdf') }}?outlet_filter=${this.filters.outlet}&tipe_filter=${this.filters.tipe}`;
        },

        downloadTemplate() {
          window.location.href = '{{ route("admin.crm.pelanggan.download-template") }}';
        },

        importExcel(event) {
          const file = event.target.files[0];
          if (!file) return;

          const formData = new FormData();
          formData.append('file', file);

          // Show loading
          const originalText = event.target.parentElement.querySelector('span').textContent;
          event.target.parentElement.querySelector('span').textContent = 'Mengimport...';
          event.target.parentElement.style.pointerEvents = 'none';

          fetch('{{ route("admin.crm.pelanggan.import.excel") }}', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            event.target.parentElement.querySelector('span').textContent = originalText;
            event.target.parentElement.style.pointerEvents = 'auto';
            event.target.value = ''; // Reset file input

            if (data.success) {
              let message = data.message;
              if (data.errors && data.errors.length > 0) {
                message += '\n\nError:\n' + data.errors.slice(0, 5).join('\n');
                if (data.errors.length > 5) {
                  message += '\n... dan ' + (data.errors.length - 5) + ' error lainnya';
                }
              }
              alert(message);
              this.loadData();
              this.loadStatistics();
            } else {
              alert(data.message || 'Gagal import data');
            }
          })
          .catch(error => {
            event.target.parentElement.querySelector('span').textContent = originalText;
            event.target.parentElement.style.pointerEvents = 'auto';
            event.target.value = '';
            alert('Terjadi kesalahan saat import');
            console.error('Error:', error);
          });
        },

        formatRupiah(amount) {
          return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        }
      }
    }
  </script>
</x-layouts.admin>
