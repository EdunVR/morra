{{-- resources/views/admin/dashboard.blade.php --}}
<x-layouts.admin :title="'Dashboard'">
    {{-- ====== ANALITIK (Responsif & Anti-Overflow) ====== --}}
    <section class="mb-6 overflow-x-hidden">
        <div class="mb-3">
            <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
            <p class="text-slate-600">Ringkasan singkat & navigasi modul.</p>
        </div>

        {{-- KPI Cards (mobile:1, md:3) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500">
                    <i class='bx bx-line-chart text-primary-600 text-base sm:text-lg'></i>
                    <span>Total Penjualan (bulan ini)</span>
                </div>
                <div class="mt-2 flex items-end gap-2">
                    <div class="text-xl sm:text-2xl font-bold tracking-tight break-words">Rp 128.450.000</div>
                    <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700">+12,4%</span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500">
                    <i class='bx bx-task text-primary-600 text-base sm:text-lg'></i>
                    <span>Pesanan Diproses</span>
                </div>
                <div class="mt-2 flex items-end gap-2">
                    <div class="text-xl sm:text-2xl font-bold tracking-tight">842</div>
                    <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700">+4,1%</span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500">
                    <i class='bx bx-error-circle text-primary-600 text-base sm:text-lg'></i>
                    <span>Retur &amp; Cancel</span>
                </div>
                <div class="mt-2 flex items-end gap-2">
                    <div class="text-xl sm:text-2xl font-bold tracking-tight">17</div>
                    <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-700">-1,8%</span>
                </div>
            </div>
        </div>

        {{-- Mini Charts (mobile:1, md:2) --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Sparkline --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2 min-w-0">
                        <i class='bx bx-trending-up text-primary-600'></i>
                        <span class="truncate">Tren Penjualan 7 Hari</span>
                    </div>
                    <div class="text-[10px] sm:text-xs text-slate-400 truncate">Sen,Sel,Rab,Kam,Jum,Sab,Min</div>
                </div>
                <div class="mt-3 overflow-hidden">
                    <svg viewBox="0 0 210 40" preserveAspectRatio="none" class="w-full h-20 sm:h-24">
                        <path d="M0,30 L30,28 L60,26 L90,22 L120,24 L150,18 L180,14 L210,12 L210,40 L0,40 Z" fill="rgba(47,134,255,0.12)"></path>
                        <path d="M0,30 L30,28 L60,26 L90,22 L120,24 L150,18 L180,14 L210,12" fill="none" stroke="rgb(47,134,255)" stroke-width="2"></path>
                    </svg>
                </div>
            </div>

            {{-- Bar Chart --}}
            @php $vals = [6,9,8,12,10,14,11]; $max = max($vals) ?: 1; @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card overflow-hidden">
                <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                    <i class='bx bx-bar-chart-alt-2 text-primary-600'></i>
                    <span>Top Channel Mingguan</span>
                </div>
                <div class="mt-3 h-20 sm:h-24 flex items-end gap-1.5 min-w-0 overflow-hidden">
                    @foreach($vals as $v)
                        @php $h = max(6, round(($v / $max) * 96)); @endphp
                        <div class="flex-1 basis-0 min-w-0 rounded-md bg-primary-200/80 hover:bg-primary-300 transition" style="height: {{ $h }}px"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ====== CHARTS & ANALYTICS ====== --}}
    <section class="mb-6 overflow-x-hidden">
        <h2 class="text-lg font-semibold mb-4">Analisis & Visualisasi Data</h2>
        
        {{-- Row 1: Penjualan & Keuangan --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            {{-- Chart Penjualan Per Kategori --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                        <i class='bx bx-pie-chart-alt-2 text-primary-600'></i>
                        <span>Penjualan Per Kategori</span>
                    </div>
                    <div class="text-xs text-slate-500">Bulan Ini</div>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <div class="relative w-48 h-48">
                        {{-- Pie Chart Dummy --}}
                        <svg viewBox="0 0 42 42" class="w-full h-full">
                            <circle cx="21" cy="21" r="15.9155" class="fill-transparent stroke-[4] stroke-slate-100" />
                            <circle cx="21" cy="21" r="15.9155" class="fill-transparent stroke-[4] stroke-primary-500" stroke-dasharray="25 75" stroke-dashoffset="25" />
                            <circle cx="21" cy="21" r="15.9155" class="fill-transparent stroke-[4] stroke-green-500" stroke-dasharray="20 80" stroke-dashoffset="5" />
                            <circle cx="21" cy="21" r="15.9155" class="fill-transparent stroke-[4] stroke-amber-500" stroke-dasharray="15 85" stroke-dashoffset="-10" />
                            <circle cx="21" cy="21" r="15.9155" class="fill-transparent stroke-[4] stroke-red-500" stroke-dasharray="40 60" stroke-dashoffset="-25" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <div class="text-lg font-bold">128.4JT</div>
                            <div class="text-xs text-slate-500">Total</div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                        <span>Elektronik (25%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span>Fashion (20%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <span>Makanan (15%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span>Lainnya (40%)</span>
                    </div>
                </div>
            </div>

            {{-- Chart Arus Kas --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                        <i class='bx bx-trending-up text-primary-600'></i>
                        <span>Arus Kas 6 Bulan</span>
                    </div>
                    <div class="text-xs text-slate-500">Dalam Juta</div>
                </div>
                <div class="h-64">
                    {{-- Line Chart Dummy --}}
                    <div class="h-full flex items-end justify-between px-2 pb-2">
                        @php
                            $cashFlow = [
                                ['month' => 'Jan', 'income' => 85, 'expense' => 65],
                                ['month' => 'Feb', 'income' => 92, 'expense' => 70],
                                ['month' => 'Mar', 'income' => 78, 'expense' => 58],
                                ['month' => 'Apr', 'income' => 110, 'expense' => 85],
                                ['month' => 'Mei', 'income' => 95, 'expense' => 72],
                                ['month' => 'Jun', 'income' => 128, 'expense' => 95]
                            ];
                        @endphp
                        @foreach($cashFlow as $month)
                        <div class="flex flex-col items-center flex-1">
                            <div class="text-xs text-slate-500 mb-1">{{ $month['month'] }}</div>
                            <div class="w-full flex flex-col items-center justify-end h-40">
                                <div class="w-3/4 flex justify-center gap-1 mb-1">
                                    <div class="w-1/2 bg-green-100 rounded-t" style="height: {{ ($month['income'] / 130) * 100 }}%"></div>
                                    <div class="w-1/2 bg-red-100 rounded-t" style="height: {{ ($month['expense'] / 130) * 100 }}%"></div>
                                </div>
                                <div class="w-3/4 h-1 bg-gradient-to-r from-green-500 to-red-500 rounded-full"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4 flex justify-center gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span>Pemasukan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span>Pengeluaran</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Inventaris & Produksi --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            {{-- Chart Stok Inventaris --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                        <i class='bx bx-package text-primary-600'></i>
                        <span>Status Stok Inventaris</span>
                    </div>
                    <div class="text-xs text-slate-500">Real-time</div>
                </div>
                <div class="h-64">
                    @php
                        $inventory = [
                            ['name' => 'Produk A', 'stock' => 85, 'max' => 100, 'color' => 'bg-green-500'],
                            ['name' => 'Produk B', 'stock' => 45, 'max' => 100, 'color' => 'bg-amber-500'],
                            ['name' => 'Produk C', 'stock' => 15, 'max' => 100, 'color' => 'bg-red-500'],
                            ['name' => 'Produk D', 'stock' => 92, 'max' => 100, 'color' => 'bg-green-500'],
                            ['name' => 'Produk E', 'stock' => 28, 'max' => 100, 'color' => 'bg-red-500'],
                        ];
                    @endphp
                    <div class="space-y-3">
                        @foreach($inventory as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-sm w-20 truncate">{{ $item['name'] }}</span>
                            <div class="flex-1 mx-2">
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="{{ $item['color'] }} h-2 rounded-full" style="width: {{ ($item['stock'] / $item['max']) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="text-sm w-10 text-right">{{ $item['stock'] }}/100</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-2 text-center">
                        <div class="p-3 rounded-lg bg-green-50 border border-green-100">
                            <div class="text-lg font-bold text-green-700">12</div>
                            <div class="text-xs text-green-600">Stok Aman</div>
                        </div>
                        <div class="p-3 rounded-lg bg-amber-50 border border-amber-100">
                            <div class="text-lg font-bold text-amber-700">5</div>
                            <div class="text-xs text-amber-600">Stok Menipis</div>
                        </div>
                        <div class="p-3 rounded-lg bg-red-50 border border-red-100">
                            <div class="text-lg font-bold text-red-700">3</div>
                            <div class="text-xs text-red-600">Stok Habis</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart Efisiensi Produksi --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                        <i class='bx bx-cube text-primary-600'></i>
                        <span>Efisiensi Produksi</span>
                    </div>
                    <div class="text-xs text-slate-500">Bulan Ini</div>
                </div>
                <div class="h-64 flex flex-col justify-center">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary-600">94%</div>
                            <div class="text-xs text-slate-500">Target Capai</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">88%</div>
                            <div class="text-xs text-slate-500">Efisiensi</div>
                        </div>
                    </div>
                    
                    {{-- Gauge Chart Dummy --}}
                    <div class="relative h-32 flex items-center justify-center">
                        <svg viewBox="0 0 120 60" class="w-64">
                            {{-- Background Arc --}}
                            <path d="M20,55 A40,40 0 1,1 100,55" fill="none" stroke="#e2e8f0" stroke-width="12" />
                            {{-- Value Arc --}}
                            <path d="M20,55 A40,40 0 1,1 100,55" fill="none" stroke="#3b82f6" stroke-width="12" 
                                  stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - (125.6 * 0.88) }}" />
                            {{-- Needle --}}
                            <line x1="60" y1="55" x2="60" y2="25" stroke="#1e293b" stroke-width="2" 
                                  transform="rotate({{ (0.88 * 180) - 90 }}, 60, 55)" />
                            <circle cx="60" cy="55" r="4" fill="#1e293b" />
                        </svg>
                        <div class="absolute bottom-8 text-center">
                            <div class="text-lg font-bold">88%</div>
                            <div class="text-xs text-slate-500">Efisiensi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: SDM & Pelanggan --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Chart Kinerja SDM --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                        <i class='bx bx-id-card text-primary-600'></i>
                        <span>Kinerja Karyawan</span>
                    </div>
                    <div class="text-xs text-slate-500">Triwulan</div>
                </div>
                <div class="h-64">
                    @php
                        $employees = [
                            ['name' => 'Ahmad', 'performance' => 88, 'attendance' => 95],
                            ['name' => 'Sari', 'performance' => 92, 'attendance' => 98],
                            ['name' => 'Budi', 'performance' => 76, 'attendance' => 89],
                            ['name' => 'Dewi', 'performance' => 85, 'attendance' => 92],
                            ['name' => 'Rudi', 'performance' => 94, 'attendance' => 96],
                        ];
                    @endphp
                    <div class="space-y-4">
                        @foreach($employees as $emp)
                        <div class="flex items-center">
                            <div class="w-16 text-sm truncate">{{ $emp['name'] }}</div>
                            <div class="flex-1 mx-2">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span>Kinerja</span>
                                    <span>{{ $emp['performance'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $emp['performance'] }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-xs mt-1">
                                    <span>Kehadiran</span>
                                    <span>{{ $emp['attendance'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $emp['attendance'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Chart Demografi Pelanggan --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-medium text-sm sm:text-base flex items-center gap-2">
                        <i class='bx bx-group text-primary-600'></i>
                        <span>Demografi Pelanggan</span>
                    </div>
                    <div class="text-xs text-slate-500">Berdasarkan Usia</div>
                </div>
                <div class="h-64">
                    {{-- Pyramid Chart Dummy --}}
                    <div class="h-full flex items-end justify-center gap-2 pb-4">
                        @php
                            $ageGroups = [
                                ['group' => '18-25', 'male' => 25, 'female' => 30],
                                ['group' => '26-35', 'male' => 40, 'female' => 45],
                                ['group' => '36-45', 'male' => 35, 'female' => 32],
                                ['group' => '46-55', 'male' => 20, 'female' => 18],
                                ['group' => '55+', 'male' => 10, 'female' => 8],
                            ];
                            $maxValue = 45;
                        @endphp
                        @foreach($ageGroups as $group)
                        <div class="flex flex-col items-center flex-1">
                            <div class="text-xs text-slate-500 mb-1">{{ $group['group'] }}</div>
                            <div class="w-full flex justify-center gap-1">
                                <div class="bg-blue-400 rounded-t" style="height: {{ ($group['male'] / $maxValue) * 120 }}px; width: 45%"></div>
                                <div class="bg-pink-400 rounded-t" style="height: {{ ($group['female'] / $maxValue) * 120 }}px; width: 45%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex justify-center gap-4 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                            <span>Pria</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-pink-400"></div>
                            <span>Wanita</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====== GRID MODUL (Anti-Overflow) ====== --}}
    <section class="overflow-x-hidden">
        <h2 class="text-lg font-semibold mb-3">Modul</h2>

        {{-- Grid: mobile:1, sm:2, xl:4 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- Inventaris (FIX route name) --}}
            <a href="{{ route('admin.inventaris.index') }}"
               class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-package text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Inventaris</h3>
                        <p class="mt-1 text-sm text-slate-600">Outlet, Produk, Stok, Transfer Gudang</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Investor --}}
            <a href="{{ route('admin.investor') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-coin text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Investor</h3>
                        <p class="mt-1 text-sm text-slate-600">Profil, Bagi Hasil, Pencairan</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Pelanggan --}}
            <a href="{{ route('admin.pelanggan') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-group text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Pelanggan</h3>
                        <p class="mt-1 text-sm text-slate-600">Tipe &amp; Diskon, Manajemen Pelanggan</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- POS --}}
            <a href="{{ route('admin.pos') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-cart text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Point of Sales</h3>
                        <p class="mt-1 text-sm text-slate-600">Transaksi, Kontra Bon</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Keuangan --}}
            <a href="#" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-wallet text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Keuangan</h3>
                        <p class="mt-1 text-sm text-slate-600">RAB, Jurnal, Laporan Keuangan</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- SDM --}}
            <a href="{{ route('admin.sdm') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-id-card text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">SDM</h3>
                        <p class="mt-1 text-sm text-slate-600">Payroll, Rekrutmen, Absensi</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Penjualan --}}
            <a href="{{ route('admin.penjualan.dashboard.index') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-receipt text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Penjualan</h3>
                        <p class="mt-1 text-sm text-slate-600">Invoice, Laporan Penjualan</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Pembelian (FIX: pakai bxs-truck) --}}
            <a href="{{ route('pembelian.index') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bxs-truck text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Pembelian</h3>
                        <p class="mt-1 text-sm text-slate-600">PO, Supplier</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Produksi --}}
            <a href="#" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-cube text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Produksi</h3>
                        <p class="mt-1 text-sm text-slate-600">Perencanaan &amp; Proses</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Rantai Pasok --}}
            <a href="{{ route('admin.rantai-pasok') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-git-branch text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Rantai Pasok</h3>
                        <p class="mt-1 text-sm text-slate-600">Distribusi &amp; Transfer</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Service --}}
            <a href="{{ route('admin.service') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-wrench text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Service</h3>
                        <p class="mt-1 text-sm text-slate-600">Invoice, Histori, Ongkir, Mesin</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Analisis --}}
            <a href="{{ route('admin.analisis') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-analyse text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Analisis &amp; Pelaporan</h3>
                        <p class="mt-1 text-sm text-slate-600">Laporan Umum &amp; Penjualan</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>

            {{-- Sistem --}}
            <a href="{{ route('admin.sistem') }}" class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition transform hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-start gap-3 relative">
                    <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <i class='bx bx-cog text-primary-700 text-2xl'></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-slate-900">Sistem</h3>
                        <p class="mt-1 text-sm text-slate-600">User &amp; Pengaturan</p>
                    </div>
                </div>
                <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
                    <i class='bx bx-right-arrow-alt text-xl'></i>
                </div>
            </a>
        </div>
    </section>
</x-layouts.admin>
