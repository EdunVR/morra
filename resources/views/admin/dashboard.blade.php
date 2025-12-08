{{-- resources/views/admin/dashboard.blade.php --}}
@php
    $user = auth()->user();
    $userOutlets = $user->outlets ?? collect();
    $defaultOutlet = session('outlet_id') ?? $userOutlets->first()->id_outlet ?? null;
@endphp

<x-layouts.admin :title="'Dashboard'">
    <div x-data="dashboardData({{ $defaultOutlet }})" x-init="init()">
        {{-- Header --}}
        <section class="mb-6">
            <div class="mb-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
                    <p class="text-slate-600">Analisis bisnis & insight real-time</p>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    {{-- Outlet Filter --}}
                    @if($userOutlets->count() > 0)
                    <select x-model="selectedOutlet" @change="changeOutlet()" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 flex-1 sm:flex-initial">
                        @foreach($userOutlets as $outlet)
                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                        @endforeach
                    </select>
                    @endif
                    
                    <button @click="refreshData()" class="px-4 py-2 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 transition flex items-center gap-2 whitespace-nowrap">
                        <i class='bx bx-refresh' :class="{'bx-spin': loading}"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500">
                        <i class='bx bx-line-chart text-primary-600 text-base sm:text-lg'></i>
                        <span>Total Penjualan (bulan ini)</span>
                    </div>
                    <div class="mt-2 flex items-end gap-2">
                        <div class="text-xl sm:text-2xl font-bold tracking-tight break-words" x-text="formatCurrency(stats.sales.value)">Loading...</div>
                        <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full" 
                              :class="stats.sales.growth >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                              x-text="(stats.sales.growth >= 0 ? '+' : '') + stats.sales.growth + '%'"></span>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500">
                        <i class='bx bx-task text-primary-600 text-base sm:text-lg'></i>
                        <span>Pesanan Diproses</span>
                    </div>
                    <div class="mt-2 flex items-end gap-2">
                        <div class="text-xl sm:text-2xl font-bold tracking-tight" x-text="stats.orders.value">Loading...</div>
                        <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full" 
                              :class="stats.orders.growth >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                              x-text="(stats.orders.growth >= 0 ? '+' : '') + stats.orders.growth + '%'"></span>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500">
                        <i class='bx bx-error-circle text-primary-600 text-base sm:text-lg'></i>
                        <span>Retur &amp; Cancel</span>
                    </div>
                    <div class="mt-2 flex items-end gap-2">
                        <div class="text-xl sm:text-2xl font-bold tracking-tight" x-text="stats.returns.value">Loading...</div>
                        <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full" 
                              :class="stats.returns.growth <= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                              x-text="(stats.returns.growth >= 0 ? '+' : '') + stats.returns.growth + '%'"></span>
                    </div>
                </div>
            </div>

            {{-- Mini Charts --}}
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2 mb-3">
                        <i class='bx bx-trending-up text-primary-600'></i>
                        <span>Tren Penjualan 7 Hari</span>
                    </div>
                    <div class="h-24 flex items-end justify-between px-2 pb-2">
                        <template x-for="(item, index) in salesTrend" :key="index">
                            <div class="flex flex-col items-center flex-1">
                                <div class="text-xs text-slate-500 mb-1" x-text="item.day"></div>
                                <div class="w-3/4 bg-primary-200 rounded-t transition-all" 
                                     :style="`height: ${Math.max(4, (item.value / salesTrendMax) * 60)}px`"></div>
                            </div>
                        </template>
                        <div x-show="salesTrend.length === 0" class="w-full text-center text-xs text-slate-500 py-4">
                            Tidak ada data
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2 mb-3">
                        <i class='bx bx-bulb text-primary-600'></i>
                        <span>Insight & Rekomendasi</span>
                    </div>
                    <div class="space-y-2 max-h-24 overflow-y-auto">
                        <template x-for="insight in insights.slice(0, 2)" :key="insight.title">
                            <div class="p-2 rounded-lg text-xs" 
                                 :class="{
                                     'bg-green-50 text-green-700': insight.type === 'success',
                                     'bg-blue-50 text-blue-700': insight.type === 'info',
                                     'bg-amber-50 text-amber-700': insight.type === 'warning',
                                     'bg-red-50 text-red-700': insight.type === 'danger'
                                 }">
                                <div class="font-semibold" x-text="insight.title"></div>
                                <div class="text-[10px] mt-1" x-text="insight.message"></div>
                            </div>
                        </template>
                        <div x-show="insights.length === 0" class="text-xs text-slate-500 text-center py-2">
                            Tidak ada insight saat ini
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Charts Section --}}
        <section class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Analisis Data</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                {{-- Inventory Status --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                            <i class='bx bx-package text-primary-600'></i>
                            <span>Status Stok Inventaris</span>
                        </div>
                        <div class="text-xs text-slate-500">Real-time</div>
                    </div>
                    <div class="h-64 overflow-y-auto">
                        <div class="space-y-3">
                            <template x-for="item in inventory.items" :key="item.name">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm w-24 truncate" x-text="item.name"></span>
                                    <div class="flex-1 mx-2">
                                        <div class="w-full bg-slate-100 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all" 
                                                 :class="{
                                                     'bg-green-500': item.status === 'safe',
                                                     'bg-amber-500': item.status === 'low',
                                                     'bg-red-500': item.status === 'critical'
                                                 }"
                                                 :style="`width: ${item.percentage}%`"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm w-16 text-right" x-text="`${item.stock}/${item.max}`"></span>
                                </div>
                            </template>
                        </div>
                        <div class="mt-6 grid grid-cols-3 gap-2 text-center">
                            <div class="p-3 rounded-lg bg-green-50 border border-green-100">
                                <div class="text-lg font-bold text-green-700" x-text="inventory.stats.safe">0</div>
                                <div class="text-xs text-green-600">Stok Aman</div>
                            </div>
                            <div class="p-3 rounded-lg bg-amber-50 border border-amber-100">
                                <div class="text-lg font-bold text-amber-700" x-text="inventory.stats.low">0</div>
                                <div class="text-xs text-amber-600">Stok Menipis</div>
                            </div>
                            <div class="p-3 rounded-lg bg-red-50 border border-red-100">
                                <div class="text-lg font-bold text-red-700" x-text="inventory.stats.critical">0</div>
                                <div class="text-xs text-red-600">Stok Kritis</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Production Efficiency --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                            <i class='bx bx-cube text-primary-600'></i>
                            <span>Efisiensi Produksi</span>
                        </div>
                        <div class="text-xs text-slate-500">Bulan Ini</div>
                    </div>
                    <div class="h-64 flex flex-col justify-center items-center">
                        <div class="grid grid-cols-2 gap-4 mb-6 w-full">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600" x-text="production.target_achievement + '%'">0%</div>
                                <div class="text-xs text-slate-500">Target Tercapai</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600" x-text="production.efficiency + '%'">0%</div>
                                <div class="text-xs text-slate-500">Efisiensi</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-slate-600">
                            <p>Monitoring produksi bulan berjalan</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Employee Performance --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                        <i class='bx bx-id-card text-primary-600'></i>
                        <span>Kinerja Karyawan</span>
                    </div>
                    <div class="text-xs text-slate-500">Triwulan</div>
                </div>
                <div class="overflow-y-auto" style="max-height: 300px;">
                    <div class="space-y-4">
                        <template x-for="emp in employees" :key="emp.name">
                            <div class="flex items-center">
                                <div class="w-20 text-sm truncate" x-text="emp.name"></div>
                                <div class="flex-1 mx-2">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span>Kinerja</span>
                                        <span x-text="emp.performance + '%'"></span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all" :style="`width: ${emp.performance}%`"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs mt-1">
                                        <span>Kehadiran</span>
                                        <span x-text="emp.attendance + '%'"></span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all" :style="`width: ${emp.attendance}%`"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="employees.length === 0" class="text-center text-sm text-slate-500 py-4">
                            Tidak ada data karyawan
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Module Grid --}}
        <section>
            <h2 class="text-lg font-semibold mb-3">Modul</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <a href="{{ route('admin.inventaris.index') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="rounded-2xl bg-primary-50 p-3">
                            <i class='bx bx-package text-primary-700 text-2xl'></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900">Inventaris</h3>
                            <p class="mt-1 text-sm text-slate-600">Outlet, Produk, Stok</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.crm.index') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="rounded-2xl bg-primary-50 p-3">
                            <i class='bx bx-group text-primary-700 text-2xl'></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900">Pelanggan</h3>
                            <p class="mt-1 text-sm text-slate-600">Tipe & Manajemen</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.penjualan.dashboard.index') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="rounded-2xl bg-primary-50 p-3">
                            <i class='bx bx-receipt text-primary-700 text-2xl'></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900">Penjualan</h3>
                            <p class="mt-1 text-sm text-slate-600">Invoice & Laporan</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('finance.dashboard.index') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="rounded-2xl bg-primary-50 p-3">
                            <i class='bx bx-wallet text-primary-700 text-2xl'></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900">Keuangan</h3>
                            <p class="mt-1 text-sm text-slate-600">RAB, Jurnal, Laporan</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.sdm') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="rounded-2xl bg-primary-50 p-3">
                            <i class='bx bx-id-card text-primary-700 text-2xl'></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900">SDM</h3>
                            <p class="mt-1 text-sm text-slate-600">Payroll, Absensi</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.produksi.produksi.index') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="rounded-2xl bg-primary-50 p-3">
                            <i class='bx bx-cube text-primary-700 text-2xl'></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900">Produksi</h3>
                            <p class="mt-1 text-sm text-slate-600">Perencanaan & Proses</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.service') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="rounded-2xl bg-primary-50 p-3">
                            <i class='bx bx-wrench text-primary-700 text-2xl'></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900">Service</h3>
                            <p class="mt-1 text-sm text-slate-600">Invoice & Histori</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.sistem') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="rounded-2xl bg-primary-50 p-3">
                            <i class='bx bx-cog text-primary-700 text-2xl'></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-slate-900">Sistem</h3>
                            <p class="mt-1 text-sm text-slate-600">User & Pengaturan</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        function dashboardData(defaultOutlet) {
            return {
                loading: false,
                initialized: false,
                selectedOutlet: defaultOutlet || null,
                stats: {
                    sales: { value: 0, growth: 0 },
                    orders: { value: 0, growth: 0 },
                    returns: { value: 0, growth: 0 }
                },
                inventory: {
                    items: [],
                    stats: { safe: 0, low: 0, critical: 0 }
                },
                production: {
                    target_achievement: 0,
                    efficiency: 0
                },
                employees: [],
                insights: [],
                salesTrend: [],
                salesTrendMax: 1,

                init() {
                    if (this.initialized) return;
                    this.initialized = true;
                    
                    // Wait for DOM to be ready
                    this.$nextTick(() => {
                        this.loadAllData();
                    });
                },

                async loadAllData() {
                    if (this.loading) return;
                    
                    this.loading = true;
                    try {
                        await this.loadOverviewStats();
                        await this.loadSalesTrend();
                        await this.loadInventoryStatus();
                        await this.loadProductionEfficiency();
                        await this.loadEmployeePerformance();
                        await this.loadInsights();
                    } catch (error) {
                        console.error('Error loading dashboard data:', error);
                        alert('Gagal memuat data dashboard. Silakan refresh halaman.');
                    } finally {
                        this.loading = false;
                    }
                },

                async loadOverviewStats() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.overview") }}' + this.getOutletParam());
                        if (!response.ok) throw new Error('Failed to fetch overview');
                        const data = await response.json();
                        this.stats = data;
                    } catch (error) {
                        console.error('Error loading overview:', error);
                    }
                },

                async loadSalesTrend() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.sales-trend") }}' + this.getOutletParam());
                        if (!response.ok) throw new Error('Failed to fetch sales trend');
                        const data = await response.json();
                        this.updateSalesTrend(data);
                    } catch (error) {
                        console.error('Error loading sales trend:', error);
                        this.salesTrend = [];
                    }
                },

                async loadInventoryStatus() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.inventory-status") }}' + this.getOutletParam());
                        if (!response.ok) throw new Error('Failed to fetch inventory');
                        const data = await response.json();
                        this.inventory = data;
                    } catch (error) {
                        console.error('Error loading inventory:', error);
                    }
                },

                async loadProductionEfficiency() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.production-efficiency") }}' + this.getOutletParam());
                        if (!response.ok) throw new Error('Failed to fetch production');
                        const data = await response.json();
                        this.production = data;
                    } catch (error) {
                        console.error('Error loading production:', error);
                    }
                },

                async loadEmployeePerformance() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.employee-performance") }}' + this.getOutletParam());
                        if (!response.ok) throw new Error('Failed to fetch employees');
                        const data = await response.json();
                        this.employees = Array.isArray(data) ? data : [];
                    } catch (error) {
                        console.error('Error loading employees:', error);
                        this.employees = [];
                    }
                },

                async loadInsights() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.insights") }}' + this.getOutletParam());
                        if (!response.ok) throw new Error('Failed to fetch insights');
                        const data = await response.json();
                        this.insights = Array.isArray(data) ? data : [];
                    } catch (error) {
                        console.error('Error loading insights:', error);
                        this.insights = [];
                    }
                },

                async refreshData() {
                    await this.loadAllData();
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value);
                },

                updateSalesTrend(data) {
                    if (!data || !Array.isArray(data) || data.length === 0) {
                        this.salesTrend = [];
                        this.salesTrendMax = 1;
                        return;
                    }
                    
                    try {
                        const processedData = [];
                        let maxValue = 1;
                        
                        for (let i = 0; i < data.length; i++) {
                            const item = data[i];
                            if (item && typeof item === 'object') {
                                const val = parseFloat(item.value) || 0;
                                processedData.push({
                                    day: String(item.day || ''),
                                    value: val
                                });
                                if (val > maxValue) maxValue = val;
                            }
                        }
                        
                        this.salesTrend = processedData;
                        this.salesTrendMax = maxValue;
                    } catch (error) {
                        console.error('Error processing sales trend:', error);
                        this.salesTrend = [];
                        this.salesTrendMax = 1;
                    }
                },

                changeOutlet() {
                    // Reload all data with new outlet
                    this.loadAllData();
                },

                getOutletParam() {
                    return this.selectedOutlet ? `?outlet_id=${this.selectedOutlet}` : '';
                }
            };
        }
    </script>
    @endpush
</x-layouts.admin>
