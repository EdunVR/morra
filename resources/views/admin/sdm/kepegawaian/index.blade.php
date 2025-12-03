<x-layouts.admin title="Kepegawaian & Rekrutmen">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Kepegawaian & Rekrutmen</h1>
                <p class="text-sm text-slate-600 mt-1">Kelola data karyawan dan rekrutmen</p>
            </div>
            <button onclick="openAddModal()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center gap-2">
                <i class='bx bx-plus'></i>
                <span>Tambah Karyawan</span>
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="statsCards">
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class='bx bx-user-check text-2xl text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Karyawan Aktif</p>
                        <p class="text-2xl font-bold text-slate-900" id="activeCount">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <i class='bx bx-user-minus text-2xl text-yellow-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Tidak Aktif</p>
                        <p class="text-2xl font-bold text-slate-900" id="inactiveCount">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-user-x text-2xl text-red-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Resign</p>
                        <p class="text-2xl font-bold text-slate-900" id="resignedCount">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-group text-2xl text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Total Karyawan</p>
                        <p class="text-2xl font-bold text-slate-900" id="totalCount">0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Outlet</label>
                    <select id="outletFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="all">Semua Outlet</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="all">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                        <option value="resigned">Resign</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Departemen</label>
                    <select id="departmentFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="all">Semua Departemen</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cari</label>
                    <input type="text" id="searchInput" placeholder="Cari nama, posisi, telepon..." class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div class="flex items-end gap-2">
                    <button onclick="loadData()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex-1">
                        <i class='bx bx-search'></i> Filter
                    </button>
                    <button onclick="exportPdf()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class='bx bxs-file-pdf'></i>
                    </button>
                    <button onclick="exportExcel()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class='bx bxs-file'></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Data Grid --}}
        <div class="bg-white rounded-xl shadow-card border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="employeeTable">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Outlet</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Nama</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Posisi</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Departemen</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Telepon</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Gaji</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Tgl Bergabung</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                <i class='bx bx-loader-alt bx-spin text-3xl'></i>
                                <p class="mt-2">Memuat data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal fade" id="employeeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-xl border-0 shadow-xl">
                <div class="modal-header border-b border-slate-200 bg-slate-50">
                    <h5 class="modal-title font-semibold" id="modalTitle">Tambah Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="employeeForm">
                    <div class="modal-body p-6">
                        <input type="hidden" id="employeeId">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Outlet <span class="text-red-500">*</span></label>
                                <select id="outlet_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    <option value="">Pilih Outlet</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Posisi <span class="text-red-500">*</span></label>
                                <input type="text" id="position" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Departemen</label>
                                <input type="text" id="department" class="w-full px-3 py-2 border border-slate-300 rounded-lg" list="departmentList">
                                <datalist id="departmentList"></datalist>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                                <select id="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                    <option value="resigned">Resign</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Telepon</label>
                                <input type="text" id="phone" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                                <input type="email" id="email" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Gaji (Rp)</label>
                                <input type="number" id="salary" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tarif Per Jam (Rp)</label>
                                <input type="number" id="hourly_rate" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Bergabung</label>
                                <input type="date" id="join_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">ID Fingerprint</label>
                                <input type="text" id="fingerprint_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                                <textarea id="address" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Job Description</label>
                                <div id="jobdeskContainer" class="space-y-2">
                                    <div class="flex gap-2">
                                        <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg jobdesk-item" placeholder="Tugas dan tanggung jawab...">
                                        <button type="button" onclick="addJobdesk()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                            <i class='bx bx-plus'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-slate-200 bg-slate-50">
                        <button type="button" class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50" data-dismiss="modal">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let employees = [];

        $(document).ready(function() {
            loadData();
            loadDepartments();

            $('#outletFilter, #statusFilter, #departmentFilter').on('change', loadData);
            $('#searchInput').on('keyup', debounce(loadData, 500));
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        async function loadData() {
            try {
                const params = new URLSearchParams({
                    outlet_filter: $('#outletFilter').val(),
                    status_filter: $('#statusFilter').val(),
                    department_filter: $('#departmentFilter').val(),
                    search: $('#searchInput').val()
                });

                const response = await fetch(`{{ route('sdm.kepegawaian.data') }}?${params}`);
                const result = await response.json();

                if (result.success) {
                    employees = result.data;
                    renderTable();
                    updateStats();
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Gagal memuat data');
            }
        }

        async function loadDepartments() {
            try {
                const response = await fetch(`{{ route('sdm.kepegawaian.departments') }}`);
                const result = await response.json();

                if (result.success) {
                    const select = $('#departmentFilter');
                    const datalist = $('#departmentList');
                    
                    result.data.forEach(dept => {
                        select.append(`<option value="${dept}">${dept}</option>`);
                        datalist.append(`<option value="${dept}">`);
                    });
                }
            } catch (error) {
                console.error('Error loading departments:', error);
            }
        }

        function renderTable() {
            const tbody = $('#employeeTableBody');
            
            if (employees.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                            <i class='bx bx-info-circle text-3xl'></i>
                            <p class="mt-2">Tidak ada data karyawan</p>
                        </td>
                    </tr>
                `);
                return;
            }

            const rows = employees.map(emp => {
                const statusColors = {
                    active: 'bg-green-100 text-green-700',
                    inactive: 'bg-yellow-100 text-yellow-700',
                    resigned: 'bg-red-100 text-red-700'
                };

                return `
                    <tr class="border-b border-slate-200 hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-700">${emp.outlet_name}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">${emp.name}</div>
                            <div class="text-sm text-slate-500">${emp.email}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-700">${emp.position}</td>
                        <td class="px-4 py-3 text-slate-700">${emp.department}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${statusColors[emp.status]}">
                                ${emp.status_label}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">${emp.phone}</td>
                        <td class="px-4 py-3 text-slate-700">${emp.salary_formatted}</td>
                        <td class="px-4 py-3 text-slate-700">${emp.join_date}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="editEmployee(${emp.id})" class="px-2 py-1 text-blue-600 hover:bg-blue-50 rounded">
                                <i class='bx bx-edit'></i>
                            </button>
                            <button onclick="deleteEmployee(${emp.id})" class="px-2 py-1 text-red-600 hover:bg-red-50 rounded">
                                <i class='bx bx-trash'></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.html(rows);
        }

        function updateStats() {
            const stats = {
                active: employees.filter(e => e.status === 'active').length,
                inactive: employees.filter(e => e.status === 'inactive').length,
                resigned: employees.filter(e => e.status === 'resigned').length,
                total: employees.length
            };

            $('#activeCount').text(stats.active);
            $('#inactiveCount').text(stats.inactive);
            $('#resignedCount').text(stats.resigned);
            $('#totalCount').text(stats.total);
        }

        function openAddModal() {
            $('#modalTitle').text('Tambah Karyawan');
            $('#employeeForm')[0].reset();
            $('#employeeId').val('');
            resetJobdesk();
            $('#employeeModal').modal('show');
        }

        async function editEmployee(id) {
            try {
                const response = await fetch(`{{ route('sdm.kepegawaian.index') }}/${id}`);
                const result = await response.json();

                if (result.success) {
                    const emp = result.data;
                    
                    $('#modalTitle').text('Edit Karyawan');
                    $('#employeeId').val(emp.id);
                    $('#outlet_id').val(emp.outlet_id);
                    $('#name').val(emp.name);
                    $('#position').val(emp.position);
                    $('#department').val(emp.department);
                    $('#status').val(emp.status);
                    $('#phone').val(emp.phone);
                    $('#email').val(emp.email);
                    $('#address').val(emp.address);
                    $('#salary').val(emp.salary);
                    $('#hourly_rate').val(emp.hourly_rate);
                    $('#join_date').val(emp.join_date);
                    $('#fingerprint_id').val(emp.fingerprint_id);
                    
                    loadJobdesk(emp.jobdesk);
                    
                    $('#employeeModal').modal('show');
                }
            } catch (error) {
                console.error('Error loading employee:', error);
                alert('Gagal memuat data karyawan');
            }
        }

        $('#employeeForm').on('submit', async function(e) {
            e.preventDefault();

            const id = $('#employeeId').val();
            const jobdesk = [];
            $('.jobdesk-item').each(function() {
                const val = $(this).val().trim();
                if (val) jobdesk.push(val);
            });

            const data = {
                outlet_id: $('#outlet_id').val(),
                name: $('#name').val(),
                position: $('#position').val(),
                department: $('#department').val(),
                status: $('#status').val(),
                phone: $('#phone').val(),
                email: $('#email').val(),
                address: $('#address').val(),
                salary: $('#salary').val(),
                hourly_rate: $('#hourly_rate').val(),
                join_date: $('#join_date').val(),
                fingerprint_id: $('#fingerprint_id').val(),
                jobdesk: jobdesk,
                _token: '{{ csrf_token() }}'
            };

            try {
                const url = id 
                    ? `{{ route('sdm.kepegawaian.index') }}/${id}`
                    : `{{ route('sdm.kepegawaian.store') }}`;
                
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    $('#employeeModal').modal('hide');
                    loadData();
                    alert(result.message);
                } else {
                    alert(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error saving employee:', error);
                alert('Gagal menyimpan data');
            }
        });

        async function deleteEmployee(id) {
            if (!confirm('Yakin ingin menghapus karyawan ini?')) return;

            try {
                const response = await fetch(`{{ route('sdm.kepegawaian.index') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    loadData();
                    alert(result.message);
                } else {
                    alert(result.message || 'Gagal menghapus data');
                }
            } catch (error) {
                console.error('Error deleting employee:', error);
                alert('Gagal menghapus data');
            }
        }

        function addJobdesk() {
            const container = $('#jobdeskContainer');
            const newItem = `
                <div class="flex gap-2">
                    <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg jobdesk-item" placeholder="Tugas dan tanggung jawab...">
                    <button type="button" onclick="$(this).parent().remove()" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class='bx bx-minus'></i>
                    </button>
                </div>
            `;
            container.append(newItem);
        }

        function resetJobdesk() {
            $('#jobdeskContainer').html(`
                <div class="flex gap-2">
                    <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg jobdesk-item" placeholder="Tugas dan tanggung jawab...">
                    <button type="button" onclick="addJobdesk()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class='bx bx-plus'></i>
                    </button>
                </div>
            `);
        }

        function loadJobdesk(jobdesk) {
            const container = $('#jobdeskContainer');
            container.empty();

            if (!jobdesk || jobdesk.length === 0) {
                resetJobdesk();
                return;
            }

            jobdesk.forEach((item, index) => {
                const html = `
                    <div class="flex gap-2">
                        <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg jobdesk-item" value="${item}" placeholder="Tugas dan tanggung jawab...">
                        <button type="button" onclick="${index === 0 ? 'addJobdesk()' : '$(this).parent().remove()'}" class="px-3 py-2 ${index === 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'} text-white rounded-lg">
                            <i class='bx ${index === 0 ? 'bx-plus' : 'bx-minus'}'></i>
                        </button>
                    </div>
                `;
                container.append(html);
            });
        }

        function exportPdf() {
            const params = new URLSearchParams({
                outlet_filter: $('#outletFilter').val(),
                status_filter: $('#statusFilter').val(),
                department_filter: $('#departmentFilter').val()
            });
            window.open(`{{ route('sdm.kepegawaian.export.pdf') }}?${params}`, '_blank');
        }

        function exportExcel() {
            const params = new URLSearchParams({
                outlet_filter: $('#outletFilter').val(),
                status_filter: $('#statusFilter').val(),
                department_filter: $('#departmentFilter').val()
            });
            window.location.href = `{{ route('sdm.kepegawaian.export.excel') }}?${params}`;
        }
    </script>
    @endpush
</x-layouts.admin>
