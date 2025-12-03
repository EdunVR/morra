<x-layouts.admin :title="'Tipe & Diskon Customer'">
  <div x-data="customerTypeManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Tipe & Diskon Customer</h1>
        <p class="text-slate-600 text-sm">Kelola tipe pelanggan dan pengaturan diskon</p>
      </div>

      <div class="flex flex-wrap gap-2">
        <button @click="openCreateModal()" 
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 h-10 hover:bg-primary-700">
          <i class='bx bx-plus'></i> Tambah Tipe
        </button>
      </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
            <i class='bx bx-category text-2xl text-purple-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="statistics.total_types">0</div>
            <div class="text-sm text-slate-600">Total Tipe</div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-user text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="statistics.total_members">0</div>
            <div class="text-sm text-slate-600">Total Pelanggan</div>
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
               placeholder="Cari nama tipe atau keterangan..."
               class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
      </div>
    </div>

    {{-- Grid View --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <template x-for="type in types" :key="type.id_tipe">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-lg transition-shadow">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <h3 class="font-semibold text-lg mb-1" x-text="type.nama_tipe"></h3>
              <p class="text-sm text-slate-600 line-clamp-2" x-text="type.keterangan || 'Tidak ada keterangan'"></p>
            </div>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
              <i class='bx bx-user text-sm mr-1'></i>
              <span x-text="type.member_count"></span>
            </span>
          </div>

          <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-calendar text-slate-400'></i>
              <span x-text="type.created_at"></span>
            </div>
          </div>

          <div class="flex gap-2 pt-3 border-t border-slate-100">
            <button @click="editType(type.id_tipe)" 
                    class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-amber-200 text-amber-700 hover:bg-amber-50 flex items-center justify-center gap-1">
              <i class='bx bx-edit'></i> Edit
            </button>
            <button @click="deleteType(type.id_tipe)" 
                    class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
              <i class='bx bx-trash'></i>
            </button>
          </div>
        </div>
      </template>

      <div x-show="types.length === 0" class="col-span-full text-center py-12 text-slate-500">
        <i class='bx bx-category text-5xl mb-2'></i>
        <p>Belum ada tipe customer</p>
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
            <h3 class="text-xl font-bold text-slate-900" x-text="modalTitle">Tambah Tipe</h3>
            <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <form @submit.prevent="submitForm()">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Tipe *</label>
                <input type="text" x-model="formData.nama_tipe" required
                       placeholder="Contoh: Member Gold, Member Silver, Reseller"
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan</label>
                <textarea x-model="formData.keterangan" rows="3"
                          placeholder="Deskripsi tipe customer, benefit, atau catatan lainnya..."
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

  </div>

  <script>
    function customerTypeManagement() {
      return {
        types: [],
        showModal: false,
        modalTitle: 'Tambah Tipe',
        loading: false,
        editMode: false,
        editId: null,
        filters: {
          search: ''
        },
        formData: {
          nama_tipe: '',
          keterangan: ''
        },
        statistics: {
          total_types: 0,
          total_members: 0,
          type_usage: []
        },

        init() {
          this.loadData();
          this.loadStatistics();
        },

        loadData() {
          const params = new URLSearchParams({
            search: this.filters.search
          });

          fetch(`{{ route("admin.crm.tipe.data") }}?${params}`)
            .then(res => res.json())
            .then(data => {
              if (data.data) {
                this.types = data.data;
              }
            })
            .catch(err => console.error('Error loading data:', err));
        },

        loadStatistics() {
          fetch(`{{ route('admin.crm.tipe.statistics') }}`)
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
          this.modalTitle = 'Tambah Tipe Customer';
          this.resetForm();
          this.showModal = true;
        },

        editType(id) {
          this.editMode = true;
          this.editId = id;
          this.modalTitle = 'Edit Tipe Customer';
          this.loadTypeData(id);
          this.showModal = true;
        },

        loadTypeData(id) {
          fetch(`{{ url('admin/crm/tipe') }}/${id}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.formData = {
                  nama_tipe: data.data.nama_tipe,
                  keterangan: data.data.keterangan
                };
              }
            })
            .catch(err => console.error('Error loading type:', err));
        },

        submitForm() {
          this.loading = true;
          const url = this.editMode 
            ? `{{ url('admin/crm/tipe') }}/${this.editId}`
            : '{{ route("admin.crm.tipe.store") }}';
          
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

        deleteType(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus tipe customer ini?')) return;

          fetch(`{{ url('admin/crm/tipe') }}/${id}`, {
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
              alert(data.message || 'Gagal menghapus tipe customer');
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

        resetForm() {
          this.formData = {
            nama_tipe: '',
            keterangan: ''
          };
        }
      }
    }
  </script>
</x-layouts.admin>
