@php
    use Illuminate\Support\Facades\Route;
    $current = Route::currentRouteName();
    $user = auth()->user();
    
    // Redirect to login if user is not authenticated
    if (!$user) {
        header('Location: ' . route('login'));
        exit;
    }

    // Define menus with permission requirements
    $menusWithPermissions = [
        'Master/Inventaris' => [
            'module' => 'inventaris',
            'items' => [
                ['Outlet', route('admin.inventaris.outlet.index'), ['inventaris.outlet.view']],
                ['Kategori Umum', route('admin.inventaris.kategori.index'), ['inventaris.kategori.view']],
                ['Satuan', route('admin.inventaris.satuan.index'), ['inventaris.satuan.view']],
                ['Produk', route('admin.inventaris.produk.index'), ['inventaris.produk.view']],
                ['Bahan', route('admin.inventaris.bahan.index'), ['inventaris.bahan.view']],
                ['Inventori', route('admin.inventaris.inventori.index'), ['inventaris.inventori.view']],
                ['Transfer Gudang', route('admin.inventaris.transfer-gudang.index'), ['inventaris.transfer-gudang.view']],
            ]
        ],

        'Pelanggan (CRM)' => [
            'module' => 'crm',
            'items' => [
                ['Tipe & Diskon Customer', route('admin.crm.tipe.index'), ['crm.tipe.view']],
                ['Manajemen Pelanggan', route('admin.crm.pelanggan.index'), ['crm.pelanggan.view']],
                ['Manajemen Prospek & Lead', '#', ['crm.leads.view']],
            ]
        ],

        'Keuangan (F&A)' => [
            'module' => 'finance',
            'items' => [
                ['Manajemen RAB', route('admin.finance.rab.index'), ['finance.rab.view']],
                ['Biaya', route('finance.biaya.index'), ['finance.biaya.view']],
                ['Hutang', route('finance.hutang.index'), ['finance.hutang.view']],
                ['Piutang', route('finance.piutang.index'), ['finance.piutang.view']],
                ['Rekonsiliasi Bank', route('finance.rekonsiliasi.index'), ['finance.rekonsiliasi.view']],
                ['Daftar Akun', route('finance.akun.index'), ['finance.buku.view']],
                ['Buku Akuntansi', route('finance.buku.index'), ['finance.buku.view']],
                ['Saldo Awal', route('finance.saldo-awal.index'), ['finance.buku.view']],
                ['Jurnal', route('finance.jurnal.index'), ['finance.jurnal.view']],
                ['Aktiva Tetap', route('finance.aktiva.index'), ['finance.aktiva.view']],
                ['Buku Besar', route('finance.buku-besar.index'), ['finance.ledger.view']],
                ['Neraca', route('finance.neraca.index'), ['finance.neraca.view']],
                ['Neraca Saldo', route('finance.neraca-saldo.index'), ['finance.neraca.view']],
                ['Laporan Laba Rugi', route('finance.profit-loss.index'), ['finance.laba-rugi.view']],
                ['Arus Kas', route('finance.cashflow.index'), ['finance.arus-kas.view']],
            ]
        ],

        'Penjualan (S&M)' => [
            'module' => 'sales',
            'items' => [
                ['Point of Sales', route('admin.penjualan.pos.index'), ['pos.kasir.view']],
                ['Invoice Penjualan', route('admin.penjualan.invoice.index'), ['sales.invoice.view']],
                ['Laporan Penjualan', route('admin.penjualan.laporan.index'), ['sales.invoice.view']],
                ['Laporan Margin', route('admin.penjualan.margin.index'), ['sales.invoice.view']],
                ['Agen & Gerobak', route('admin.penjualan.agen_gerobak.index'), ['sales.invoice.view']],
                ['Halaman Agen', route('admin.penjualan.agen.index'), ['sales.invoice.view']],
            ]
        ],

        'Pembelian (PM)' => [
            'module' => 'procurement',
            'items' => [
                ['Purchase Order', route('pembelian.purchase-order.index'), ['procurement.purchase-order.view']],
            ]
        ],

        'Produksi (MRP)' => [
            'module' => 'production',
            'items' => [
                ['Data Produksi', route('admin.produksi.produksi.index'), ['production.work-order.view']],
            ]
        ],

        'Rantai Pasok (SCM)' => [
            'module' => 'inventaris',
            'items' => [
                ['Transfer Gudang', route('admin.inventaris.transfer-gudang.index'), ['inventaris.transfer-gudang.view']],
            ]
        ],

        'SDM' => [
            'module' => 'hrm',
            'items' => [
                ['Kepegawaian & Rekrutmen', route('sdm.kepegawaian.index'), ['hrm.karyawan.view']],
                ['Penggajian / Payroll', route('sdm.payroll.index'), ['hrm.payroll.view']],
                ['Manajemen Kinerja', '#', ['hrm.karyawan.view']],
                ['Pelatihan & Pengembangan', '#', ['hrm.karyawan.view']],
                ['Manajemen Absensi & Waktu Kerja', '#', ['hrm.absensi.view']],
            ]
        ],

        'Service' => [
            'module' => 'service',
            'items' => [
                ['Invoice Service', '#', ['service.invoice.view']],
                ['Histori Service', '#', ['service.invoice.view']],
                ['Ongkir Service', '#', ['service.invoice.view']],
                ['Mesin Service', '#', ['service.invoice.view']],
            ]
        ],

        'Investor' => [
            'module' => 'investor',
            'items' => [
                ['Profil Investor', route('admin.investor.profil.index'), ['investor.profil.view']],
                ['Bagi Hasil', '#', ['investor.bagi-hasil.view']],
                ['List Pencairan', '#', ['investor.pencairan.view']],
            ]
        ],

        'Analisis & Pelaporan' => [
            'module' => 'analytics',
            'items' => [
                ['Laporan Umum', '#', ['analytics.laporan.view']],
                ['Laporan Penjualan', '#', ['sales.invoice.view']],
            ]
        ],

        'Sistem' => [
            'module' => 'sistem',
            'items' => [
                ['User Management', route('admin.users.index'), ['sistem.users.view']],
                ['Role & Permission', route('admin.roles.index'), ['sistem.roles.view']],
                ['Pengaturan', '#', ['sistem.settings.view']],
            ]
        ],
    ];

    // Filter menus based on user permissions
    $menus = [];
    foreach ($menusWithPermissions as $menuName => $menuData) {
        $filteredItems = [];
        foreach ($menuData['items'] as $item) {
            $permissions = $item[2] ?? [];
            // Check if user has any of the required permissions
            if ($user->hasRole('super_admin') || empty($permissions)) {
                $filteredItems[] = [$item[0], $item[1]];
            } else {
                foreach ($permissions as $perm) {
                    if ($user->hasPermission($perm)) {
                        $filteredItems[] = [$item[0], $item[1]];
                        break;
                    }
                }
            }
        }
        // Only add menu if it has accessible items
        if (!empty($filteredItems)) {
            $menus[$menuName] = $filteredItems;
        }
    }

    // Define modules with their routes
    $allModules = [
        ['name'=>'Master/Inventaris','route'=>'admin.inventaris.index','icon'=>'boxes','module'=>'inventaris'],
        ['name'=>'Pelanggan (CRM)','route'=>'admin.crm.index','icon'=>'users','module'=>'crm'],
        ['name'=>'Penjualan (S&M)','route'=>'admin.penjualan.dashboard.index','icon'=>'receipt-text','module'=>'sales'],
        ['name'=>'Pembelian (PM)','route'=>'pembelian.purchase-order.index','icon'=>'truck','module'=>'procurement'],
        ['name'=>'Produksi (MRP)','route'=>'admin.produksi','icon'=>'factory','module'=>'production'],
        ['name'=>'Rantai Pasok (SCM)','route'=>'admin.rantai-pasok','icon'=>'git-branch','module'=>'inventaris'],
        ['name'=>'Keuangan (F&A)','route'=>'finance.accounting.index','icon'=>'wallet','module'=>'finance'],
        ['name'=>'SDM','route'=>'admin.sdm','icon'=>'id-card','module'=>'hrm'],
        ['name'=>'Service','route'=>'admin.service','icon'=>'wrench','module'=>'service'],
        ['name'=>'Investor','route'=>'admin.investor','icon'=>'hand-coins','module'=>'investor'],
        ['name'=>'Analisis & Pelaporan','route'=>'admin.analisis','icon'=>'line-chart','module'=>'analytics'],
        ['name'=>'Sistem','route'=>'admin.sistem','icon'=>'settings','module'=>'sistem'],
    ];

    // Filter modules - only show if user has access to at least one submenu
    $modules = [];
    foreach ($allModules as $module) {
        if (isset($menus[$module['name']])) {
            $modules[] = $module;
        }
    }
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 w-80 bg-white/90 backdrop-blur border-r border-slate-200 shadow-sm lg:translate-x-0 transform transition-transform"
    :class="{'-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen}"
    x-on:keydown.escape.window="sidebarOpen=false"
>
    {{-- ===== BRAND: logo besar & posisi center ===== --}}
    <div class="h-20 px-4 border-b border-slate-200 flex items-center">
        <div class="relative w-full">
            {{-- center --}}
            <a href="{{ route('admin.dashboard') }}" class="block w-fit mx-auto">
                <img
                    src="{{ url(asset('img/logo_xx.png')) }}"
                    alt="MORRA"
                    class="h-20 md:h-30 w-auto object-contain select-none"
                    loading="lazy"
                />
                <span class="sr-only">Beranda</span>
            </a>
            {{-- tombol close di mobile (kanan) --}}
            <button class="absolute right-0 top-1/2 -translate-y-1/2 p-2 -mr-1 rounded hover:bg-slate-100 lg:hidden"
                    @click="sidebarOpen=false" aria-label="Tutup Sidebar">
                <x-icon name="menu" class="w-5 h-5" />
            </button>
        </div>
    </div>

    {{-- ===== NAV ===== --}}
    {{-- h-20 = 80px, jadi tinggi scroll disesuaikan --}}
    <nav class="p-4 space-y-3 overflow-y-auto h-[calc(100vh-80px)]" x-data="sidebarState">
        @foreach ($modules as $m)
            @php 
                $active = $current === $m['route'];
                $menuId = str_replace(['/', ' ', '(', ')'], ['_', '_', '', ''], $m['name']);
            @endphp

            <div data-menu-parent="{{ $menuId }}"
                 class="border border-slate-200 rounded-2xl shadow-card overflow-hidden {{ $active ? 'ring-1 ring-primary-200 bg-primary-50/50' : 'bg-white' }}">
                <a href="{{ route($m['route']) }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 {{ $active ? 'text-primary-900' : 'text-ink-900' }}">
                    <x-icon :name="$m['icon']" class="w-5 h-5 {{ $active ? 'text-primary-700' : 'text-primary-600' }}" />
                    <span class="font-medium">{{ $m['name'] }}</span>
                    <span class="ml-auto text-slate-400 text-sm">Dashboard</span>
                </a>

                <button
                    @click="toggleMenu('{{ $menuId }}')"
                    class="w-full flex items-center justify-between px-4 py-2 text-left text-sm border-t border-slate-200 hover:bg-slate-50 {{ $active ? 'text-primary-800' : 'text-slate-600' }}"
                >
                    <span>Submenu</span>
                    <span :class="isExpanded('{{ $menuId }}') ? 'rotate-90' : ''" class="transition-transform">
                        <x-icon name="arrow-right" class="w-4 h-4" />
                    </span>
                </button>

                <div x-show="isExpanded('{{ $menuId }}')" x-collapse>
                    <ul class="py-2">
                        @foreach (($menus[$m['name']] ?? []) as $item)
                            @php
                                $isDemo = $item[1] === '#';
                            @endphp

                            <li>
                                @if ($isDemo)
                                    <button 
                                        type="button"
                                        class="block mx-2 px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 hover:text-red-600 w-full text-left cursor-pointer font-medium"
                                        onclick="showDemoModal('{{ $item[0] }}')"
                                    >
                                        {{ $item[0] }}
                                    </button>
                                @else
                                    <a href="{{ $item[1] }}"
                                    class="block mx-2 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 font-medium">
                                        {{ $item[0] }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </nav>

</aside>

<!-- Modal DEMO -->
<div 
    x-data
    x-show="$store.demoModal.open"
    x-transition.opacity.duration.200ms
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    @click.self="$store.demoModal.hide()"
>
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md text-center transform transition-all scale-100"
         x-transition.scale.origin.center>
        <h2 class="text-lg font-semibold mb-3 text-slate-800" x-text="$store.demoModal.title"></h2>
        <p class="text-sm text-slate-600 mb-5 leading-relaxed">
            Fitur ini tidak ditampilkan karena halaman ini versi <span class="font-semibold text-red-600">DEMO</span>.<br>
            Silahkan hubungi developer untuk akses penuh.
        </p>
        <div class="flex justify-center gap-3">
            <button 
                @click="$store.demoModal.hide()" 
                class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">
                Tutup
            </button>
            <a :href="'https://wa.me/6285795483498?text=' + encodeURIComponent('Halo developer, saya ingin mengakses fitur ' + $store.demoModal.title)"
               target="_blank"
               class="px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-medium">
                Hubungi via WhatsApp
            </a>
        </div>
    </div>
</div>

<script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('demoModal', {
                open: false,
                title: '',
                show(title) {
                    this.title = title;
                    this.open = true;
                },
                hide() {
                    this.open = false;
                }
            });

            // Sidebar state management component
            Alpine.data('sidebarState', () => ({
                expandedMenus: [],

                init() {
                    // Load saved state from localStorage
                    const saved = localStorage.getItem('sidebar_expanded_menus');
                    if (saved) {
                        try {
                            this.expandedMenus = JSON.parse(saved);
                        } catch (e) {
                            this.expandedMenus = [];
                        }
                    }

                    // Auto-expand menu containing active route
                    this.expandActiveMenu();
                },

                expandActiveMenu() {
                    const currentPath = window.location.pathname;
                    const activeMenuItem = document.querySelector(`a[href="${currentPath}"]`);

                    if (activeMenuItem) {
                        const parentMenu = activeMenuItem.closest('[data-menu-parent]');
                        if (parentMenu) {
                            const menuId = parentMenu.dataset.menuParent;
                            if (!this.expandedMenus.includes(menuId)) {
                                this.expandedMenus.push(menuId);
                                this.saveState();
                            }
                        }
                    }
                },

                toggleMenu(menuId) {
                    const index = this.expandedMenus.indexOf(menuId);
                    if (index > -1) {
                        this.expandedMenus.splice(index, 1);
                    } else {
                        this.expandedMenus.push(menuId);
                    }
                    this.saveState();
                },

                isExpanded(menuId) {
                    return this.expandedMenus.includes(menuId);
                },

                saveState() {
                    localStorage.setItem('sidebar_expanded_menus', JSON.stringify(this.expandedMenus));
                }
            }));
        });

        function showDemoModal(title) {
            Alpine.store('demoModal').show(title);
        }
    </script>

