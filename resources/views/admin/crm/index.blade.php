<x-layouts.admin :title="'CRM Dashboard'">
    <div x-data="crmDashboard()" x-init="init()" class="space-y-6 overflow-x-hidden">
        {{-- Header & Filter --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">CRM Dashboard</h1>
                    <p class="text-slate-600 text-sm">Analisis Customer, Prediksi & Strategi Bisnis</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 w-full lg:w-auto">
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Outlet</label>
                        <select x-model="filter.outlet" @change="loadData()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
                            <option value="all">Semua Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Periode</label>
                        <select x-model="filter.period" @change="loadData()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
                            <option value="7">7 Hari</option>
                            <option value="30">30 Hari</option>
                            <option value="90">90 Hari</option>
                            <option value="365">1 Tahun</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>
            
            <!-- Customer Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Pelanggan</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="formatNumber(stats.total)">0</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <x-icon name="users" class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pelanggan Aktif</p>
                            <p class="text-2xl font-bold text-green-600" x-text="formatNumber(stats.active)">0</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <x-icon name="user-check" class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Baru Bulan Ini</p>
                            <p class="text-2xl font-bold text-purple-600" x-text="formatNumber(stats.new_this_month)">0</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <x-icon name="user-plus" class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Tidak Aktif</p>
                            <p class="text-2xl font-bold text-red-600" x-text="formatNumber(stats.inactive)">0</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <x-icon name="user-x" class="w-6 h-6 text-red-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Analytics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600 mb-2">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(salesAnalytics.total_revenue)">Rp 0</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600 mb-2">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatNumber(salesAnalytics.total_transactions)">0</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600 mb-2">Rata-rata Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(salesAnalytics.avg_transaction_value)">Rp 0</p>
                </div>
            </div>

            <!-- Customer Segmentation -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Segmentasi Pelanggan</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <p class="text-3xl font-bold text-yellow-600" x-text="segmentation.vip">0</p>
                        <p class="text-sm text-gray-600">VIP</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-3xl font-bold text-green-600" x-text="segmentation.loyal">0</p>
                        <p class="text-sm text-gray-600">Loyal</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-3xl font-bold text-blue-600" x-text="segmentation.regular">0</p>
                        <p class="text-sm text-gray-600">Regular</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-3xl font-bold text-purple-600" x-text="segmentation.new">0</p>
                        <p class="text-sm text-gray-600">Baru</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <p class="text-3xl font-bold text-red-600" x-text="segmentation.at_risk">0</p>
                        <p class="text-sm text-gray-600">Berisiko</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Growth Trends -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Tren Pertumbuhan</h3>
                    <canvas id="growthChart"></canvas>
                </div>

                <!-- Customer Lifecycle -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Siklus Pelanggan</h3>
                    <canvas id="lifecycleChart"></canvas>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Top 10 Pelanggan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaksi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Belanja</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Segmen</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(customer, index) in topCustomers" :key="index">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="customer.name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="customer.phone"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="customer.transaction_count"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatCurrency(customer.total_spent)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatCurrency(customer.avg_transaction)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full" 
                                              :class="{
                                                  'bg-yellow-100 text-yellow-800': customer.segment === 'VIP',
                                                  'bg-green-100 text-green-800': customer.segment === 'Premium',
                                                  'bg-blue-100 text-blue-800': customer.segment === 'Regular',
                                                  'bg-gray-100 text-gray-800': customer.segment === 'New'
                                              }"
                                              x-text="customer.segment"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Piutang Analysis -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Analisis Piutang</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="p-4 bg-orange-50 rounded-lg">
                                <p class="text-sm text-gray-600">Total Piutang</p>
                                <p class="text-xl font-bold text-orange-600" x-text="formatCurrency(piutangAnalysis.total_piutang)">Rp 0</p>
                                <p class="text-xs text-gray-500" x-text="piutangAnalysis.count_piutang + ' pelanggan'"></p>
                            </div>
                            <div class="p-4 bg-red-50 rounded-lg">
                                <p class="text-sm text-gray-600">Jatuh Tempo</p>
                                <p class="text-xl font-bold text-red-600" x-text="formatCurrency(piutangAnalysis.total_overdue)">Rp 0</p>
                                <p class="text-xs text-gray-500" x-text="piutangAnalysis.count_overdue + ' pelanggan'"></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold mb-2">Pelanggan dengan Piutang Jatuh Tempo</h4>
                        <div class="space-y-2">
                            <template x-for="customer in piutangAnalysis.overdue_customers" :key="customer.name">
                                <div class="flex justify-between items-center p-2 bg-red-50 rounded">
                                    <div>
                                        <p class="text-sm font-medium" x-text="customer.name"></p>
                                        <p class="text-xs text-gray-500" x-text="customer.phone"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-red-600" x-text="formatCurrency(customer.amount)"></p>
                                        <p class="text-xs text-red-500" x-text="customer.days_overdue + ' hari'"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Predictions & Strategies -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Churn Risk -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <x-icon name="alert-triangle" class="w-5 h-5 text-red-500" />
                        Prediksi Churn Risk
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-semibold text-red-600 mb-2">Risiko Tinggi</h4>
                            <template x-for="customer in predictions.churn_risk.high_risk" :key="customer.id">
                                <div class="p-3 bg-red-50 rounded mb-2">
                                    <p class="text-sm font-medium" x-text="customer.name"></p>
                                    <p class="text-xs text-gray-600" x-text="'Terakhir belanja: ' + customer.days_since_purchase + ' hari lalu'"></p>
                                    <p class="text-xs text-blue-600 mt-1">💡 Strategi: Berikan promo khusus atau hubungi langsung</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Upsell Opportunities -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <x-icon name="trending-up" class="w-5 h-5 text-green-500" />
                        Peluang Upsell
                    </h3>
                    <div class="space-y-2">
                        <template x-for="customer in predictions.upsell_opportunities" :key="customer.id">
                            <div class="p-3 bg-green-50 rounded">
                                <p class="text-sm font-medium" x-text="customer.name"></p>
                                <p class="text-xs text-gray-600" x-text="'Rata-rata belanja: ' + formatCurrency(customer.avg_purchase)"></p>
                                <p class="text-xs text-green-600 mt-1" x-text="'💡 ' + customer.recommendation"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Revenue Forecast -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Prediksi Revenue (3 Bulan Ke Depan)</h3>
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Estimasi Pertumbuhan: 
                        <span class="font-bold" :class="predictions.revenue_forecast.growth_rate >= 0 ? 'text-green-600' : 'text-red-600'" 
                              x-text="predictions.revenue_forecast.growth_rate + '%'"></span>
                    </p>
                </div>
                <canvas id="forecastChart"></canvas>
            </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function crmDashboard() {
            return {
                isLoading: false,
                filter: {
                    outlet: 'all',
                    period: '30'
                },
                stats: {
                    total: 0,
                    active: 0,
                    new_this_month: 0,
                    inactive: 0
                },
                salesAnalytics: {
                    total_revenue: 0,
                    total_transactions: 0,
                    avg_transaction_value: 0
                },
                topCustomers: [],
                segmentation: {
                    vip: 0,
                    loyal: 0,
                    regular: 0,
                    new: 0,
                    at_risk: 0
                },
                piutangAnalysis: {
                    total_piutang: 0,
                    count_piutang: 0,
                    total_overdue: 0,
                    count_overdue: 0,
                    overdue_customers: []
                },
                growthTrends: {
                    labels: [],
                    customer_growth: [],
                    revenue_growth: []
                },
                lifecycle: {
                    new: 0,
                    returning: 0,
                    churned: 0
                },
                predictions: {
                    churn_risk: {
                        high_risk: [],
                        medium_risk: []
                    },
                    upsell_opportunities: [],
                    revenue_forecast: {
                        historical: [],
                        forecast: [],
                        growth_rate: 0
                    }
                },
                charts: {},

                init() {
                    this.loadData();
                },

                async loadData() {
                    this.isLoading = true;
                    const outletId = this.filter.outlet;
                    const period = this.filter.period;

                    try {
                        // Load analytics
                        const analyticsResponse = await fetch(`{{ route('admin.crm.dashboard.analytics') }}?outlet_id=${outletId}&period=${period}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const analyticsData = await analyticsResponse.json();

                        if (analyticsData.success) {
                            this.stats = analyticsData.data.customer_stats;
                            this.salesAnalytics = analyticsData.data.sales_analytics;
                            this.topCustomers = analyticsData.data.top_customers;
                            this.segmentation = analyticsData.data.segmentation;
                            this.piutangAnalysis = analyticsData.data.piutang_analysis;
                            this.growthTrends = analyticsData.data.growth_trends;
                            this.lifecycle = analyticsData.data.lifecycle;

                            // Wait for DOM to update before rendering charts
                            this.$nextTick(() => {
                                this.renderCharts();
                            });
                        } else {
                            console.error('Failed to load analytics:', analyticsData);
                        }

                        // Load predictions
                        const predictionsResponse = await fetch(`{{ route('admin.crm.dashboard.predictions') }}?outlet_id=${outletId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const predictionsData = await predictionsResponse.json();

                        if (predictionsData.success) {
                            this.predictions = predictionsData.data;
                            // Wait for DOM to update before rendering chart
                            this.$nextTick(() => {
                                this.renderForecastChart();
                            });
                        } else {
                            console.error('Failed to load predictions:', predictionsData);
                        }

                    } catch (error) {
                        console.error('Error loading CRM data:', error);
                        alert('Gagal memuat data CRM. Silakan coba lagi.');
                    } finally {
                        this.isLoading = false;
                    }
                },

                renderCharts() {
                    // Growth Chart
                    const growthCanvas = document.getElementById('growthChart');
                    if (!growthCanvas) {
                        console.warn('Growth chart canvas not found');
                        return;
                    }

                    if (this.charts.growth) {
                        try {
                            this.charts.growth.destroy();
                        } catch (e) {
                            console.warn('Error destroying growth chart:', e);
                        }
                    }
                    const growthCtx = growthCanvas.getContext('2d');
                    this.charts.growth = new Chart(growthCtx, {
                        type: 'line',
                        data: {
                            labels: this.growthTrends.labels,
                            datasets: [{
                                label: 'Pelanggan Baru',
                                data: this.growthTrends.customer_growth,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            animation: false,
                            plugins: {
                                legend: { display: true }
                            }
                        }
                    });

                    // Lifecycle Chart
                    const lifecycleCanvas = document.getElementById('lifecycleChart');
                    if (!lifecycleCanvas) {
                        console.warn('Lifecycle chart canvas not found');
                        return;
                    }

                    if (this.charts.lifecycle) {
                        try {
                            this.charts.lifecycle.destroy();
                        } catch (e) {
                            console.warn('Error destroying lifecycle chart:', e);
                        }
                    }
                    const lifecycleCtx = lifecycleCanvas.getContext('2d');
                    this.charts.lifecycle = new Chart(lifecycleCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Pelanggan Baru', 'Pelanggan Kembali', 'Churn'],
                            datasets: [{
                                data: [this.lifecycle.new, this.lifecycle.returning, this.lifecycle.churned],
                                backgroundColor: ['rgb(147, 51, 234)', 'rgb(34, 197, 94)', 'rgb(239, 68, 68)']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            animation: false
                        }
                    });
                },

                renderForecastChart() {
                    const forecastCanvas = document.getElementById('forecastChart');
                    if (!forecastCanvas) {
                        console.warn('Forecast chart canvas not found');
                        return;
                    }

                    if (this.charts.forecast) {
                        try {
                            this.charts.forecast.destroy();
                        } catch (e) {
                            console.warn('Error destroying forecast chart:', e);
                        }
                    }
                    
                    const labels = ['M-5', 'M-4', 'M-3', 'M-2', 'M-1', 'Sekarang', 'M+1', 'M+2', 'M+3'];
                    const historical = this.predictions.revenue_forecast?.historical || [];
                    const forecast = this.predictions.revenue_forecast?.forecast || [];
                    
                    if (historical.length === 0) {
                        console.warn('No historical data for forecast');
                        return;
                    }
                    
                    const forecastCtx = forecastCanvas.getContext('2d');
                    this.charts.forecast = new Chart(forecastCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Historical',
                                data: [...historical, null, null, null],
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            }, {
                                label: 'Forecast',
                                data: [null, null, null, null, null, historical[historical.length - 1], ...forecast],
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderDash: [5, 5],
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            animation: false,
                            plugins: {
                                legend: { display: true }
                            }
                        }
                    });
                },

                formatCurrency(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('id-ID').format(value || 0);
                }
            }
        }
    </script>
</x-layouts.admin>
