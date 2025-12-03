<div class="modal fade" id="roleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded-xl border-0 shadow-xl">
            <div class="modal-header border-b border-slate-200 bg-slate-50">
                <h5 class="modal-title font-semibold" id="modalTitle">Tambah Role</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="roleForm">
                @csrf
                <input type="hidden" id="roleId" name="id">
                
                <div class="modal-body p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Nama Role <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="roleName" 
                                   name="name" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Display Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="roleDisplayName" 
                                   name="display_name" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="roleDescription" 
                                  name="description" 
                                  rows="2" 
                                  class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">
                            Permissions (Modul > Submenu > CRUD)
                        </label>
                        <div class="border border-slate-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                            @php
                                // Group by module, then by menu
                                $groupedPermissions = $permissions->groupBy('module')->map(function($modulePerms) {
                                    return $modulePerms->groupBy('menu');
                                });
                                
                                $moduleNames = [
                                    'inventory' => 'Master/Inventaris',
                                    'crm' => 'Pelanggan (CRM)',
                                    'finance' => 'Keuangan (F&A)',
                                    'sales' => 'Penjualan (S&M)',
                                    'procurement' => 'Pembelian (PM)',
                                    'production' => 'Produksi (MRP)',
                                    'hrm' => 'SDM',
                                    'pos' => 'Point of Sales',
                                    'project' => 'Project Management',
                                    'sistem' => 'Sistem',
                                ];
                            @endphp
                            
                            @foreach($groupedPermissions as $module => $menuGroups)
                            <div class="mb-4 pb-4 border-b border-slate-200 last:border-0">
                                {{-- Module Header --}}
                                <div class="flex items-center mb-3 bg-slate-50 p-2 rounded-lg">
                                    <input type="checkbox" 
                                           class="select-module mr-2" 
                                           data-module="{{ $module }}" 
                                           id="module_{{ $loop->index }}">
                                    <label for="module_{{ $loop->index }}" class="font-bold text-slate-900 text-sm">
                                        📦 {{ $moduleNames[$module] ?? ucfirst($module) }}
                                    </label>
                                </div>
                                
                                {{-- Submenus --}}
                                @foreach($menuGroups as $menu => $menuPerms)
                                <div class="ml-6 mb-3">
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" 
                                               class="select-menu mr-2" 
                                               data-module="{{ $module }}"
                                               data-menu="{{ $menu }}" 
                                               id="menu_{{ $module }}_{{ $menu }}">
                                        <label for="menu_{{ $module }}_{{ $menu }}" class="font-semibold text-slate-700 text-sm">
                                            📄 {{ ucwords(str_replace('-', ' ', $menu)) }}
                                        </label>
                                    </div>
                                    
                                    {{-- CRUD Actions --}}
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 ml-6">
                                        @foreach($menuPerms as $permission)
                                        <div class="flex items-start">
                                            <input type="checkbox" 
                                                   class="permission-checkbox mt-0.5 mr-2" 
                                                   id="perm_{{ $permission->id }}" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   data-module="{{ $module }}"
                                                   data-menu="{{ $menu }}">
                                            <label for="perm_{{ $permission->id }}" class="text-xs text-slate-600">
                                                {{ ucfirst($permission->action) }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-t border-slate-200 bg-slate-50">
                    <button type="button" 
                            class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50" 
                            data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Select all permissions in a module
$(document).on('click', '.select-module', function() {
    const module = $(this).data('module');
    const checked = $(this).prop('checked');
    $(`.permission-checkbox[data-module="${module}"]`).prop('checked', checked);
    $(`.select-menu[data-module="${module}"]`).prop('checked', checked);
});

// Select all permissions in a menu
$(document).on('click', '.select-menu', function() {
    const module = $(this).data('module');
    const menu = $(this).data('menu');
    const checked = $(this).prop('checked');
    $(`.permission-checkbox[data-module="${module}"][data-menu="${menu}"]`).prop('checked', checked);
});

// Form submit handler
$('#roleForm').on('submit', function(e) {
    e.preventDefault();
    
    const roleId = $('#roleId').val();
    const url = roleId ? `{{ route('admin.roles.update', '') }}/${roleId}` : '{{ route('admin.roles.store') }}';
    const method = roleId ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        type: method,
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                let errorMsg = '';
                Object.keys(errors).forEach(key => {
                    errorMsg += errors[key][0] + '\n';
                });
                alert(errorMsg);
            } else {
                alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            }
        }
    });
});
</script>
