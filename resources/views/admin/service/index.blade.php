<x-layouts.admin>
    <x-slot name="title">Service Management</x-slot>

    <div class="container px-6 py-8 mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Service Management</h1>
            <p class="mt-2 text-gray-600">Kelola invoice service, history, ongkir, dan mesin customer</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
            <!-- Invoice Menunggu -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Invoice Menunggu</p>
                        <p class="mt-2 text-3xl font-bold text-yellow-600" id="count-menunggu">-</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="text-2xl text-yellow-600 fas fa-clock"></i>
                    </div>
                </div>
                <a href="{{ route('admin.service.history.index', ['status' => 'menunggu']) }}" class="inline-block mt-4 text-sm text-yellow-600 hover:text-yellow-700">
                    Lihat Detail →
                </a>
            </div>

            <!-- Invoice Lunas -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Invoice Lunas</p>
                        <p class="mt-2 text-3xl font-bold text-green-600" id="count-lunas">-</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="text-2xl text-green-600 fas fa-check-circle"></i>
                    </div>
                </div>
                <a href="{{ route('admin.service.history.index', ['status' => 'lunas']) }}" class="inline-block mt-4 text-sm text-green-600 hover:text-green-700">
                    Lihat Detail →
                </a>
            </div>

            <!-- Service Berikutnya -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Service Berikutnya</p>
                        <p class="mt-2 text-3xl font-bold text-blue-600" id="count-service-berikutnya">-</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="text-2xl text-blue-600 fas fa-calendar-alt"></i>
                    </div>
                </div>
                <a href="{{ route('admin.service.history.index', ['status' => 'service-berikutnya']) }}" class="inline-block mt-4 text-sm text-blue-600 hover:text-blue-700">
                    Lihat Detail →
                </a>
            </div>

            <!-- Total Invoice -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Invoice</p>
                        <p class="mt-2 text-3xl font-bold text-gray-800" id="count-total">-</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-full">
                        <i class="text-2xl text-gray-600 fas fa-file-invoice"></i>
                    </div>
                </div>
                <a href="{{ route('admin.service.history.index') }}" class="inline-block mt-4 text-sm text-gray-600 hover:text-gray-700">
                    Lihat Detail →
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="p-6 mb-8 bg-white rounded-lg shadow-md">
            <h2 class="mb-4 text-xl font-semibold text-gray-800">Quick Actions</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.service.invoice.index') }}" class="flex items-center p-4 transition border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md">
                    <div class="flex items-center justify-center w-12 h-12 mr-4 bg-blue-100 rounded-lg">
                        <i class="text-xl text-blue-600 fas fa-plus"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Buat Invoice</p>
                        <p class="text-sm text-gray-600">Invoice baru</p>
                    </div>
                </a>

                <a href="{{ route('admin.service.history.index') }}" class="flex items-center p-4 transition border border-gray-200 rounded-lg hover:border-green-500 hover:shadow-md">
                    <div class="flex items-center justify-center w-12 h-12 mr-4 bg-green-100 rounded-lg">
                        <i class="text-xl text-green-600 fas fa-history"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">History</p>
                        <p class="text-sm text-gray-600">Riwayat invoice</p>
                    </div>
                </a>

                <a href="{{ route('admin.service.ongkir.index') }}" class="flex items-center p-4 transition border border-gray-200 rounded-lg hover:border-yellow-500 hover:shadow-md">
                    <div class="flex items-center justify-center w-12 h-12 mr-4 bg-yellow-100 rounded-lg">
                        <i class="text-xl text-yellow-600 fas fa-truck"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Ongkir</p>
                        <p class="text-sm text-gray-600">Kelola ongkir</p>
                    </div>
                </a>

                <a href="{{ route('admin.service.mesin.index') }}" class="flex items-center p-4 transition border border-gray-200 rounded-lg hover:border-purple-500 hover:shadow-md">
                    <div class="flex items-center justify-center w-12 h-12 mr-4 bg-purple-100 rounded-lg">
                        <i class="text-xl text-purple-600 fas fa-cog"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Mesin Customer</p>
                        <p class="text-sm text-gray-600">Kelola mesin</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Info -->
        <div class="p-6 bg-blue-50 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="text-2xl text-blue-600 fas fa-info-circle"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-blue-900">Tentang Service Management</h3>
                    <p class="mt-2 text-blue-800">
                        Modul Service Management membantu Anda mengelola invoice service, riwayat service, tarif ongkos kirim, dan data mesin customer. 
                        Semua data terintegrasi dengan sistem outlet untuk memudahkan pengelolaan per cabang.
                    </p>
                    <div class="mt-4">
                        <h4 class="font-semibold text-blue-900">Fitur Utama:</h4>
                        <ul class="mt-2 space-y-1 text-blue-800 list-disc list-inside">
                            <li>Buat invoice service dengan multiple items</li>
                            <li>Kelola riwayat invoice dengan filter status</li>
                            <li>Atur tarif ongkos kirim per daerah</li>
                            <li>Kelola data mesin customer dan produk</li>
                            <li>Print invoice PDF</li>
                            <li>Jadwalkan service berikutnya</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Load status counts
        fetch('{{ route("admin.service.status-counts") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('count-menunggu').textContent = data.menunggu || 0;
                document.getElementById('count-lunas').textContent = data.lunas || 0;
                document.getElementById('count-service-berikutnya').textContent = data.service_berikutnya || 0;
                
                const total = (data.menunggu || 0) + (data.lunas || 0) + (data.gagal || 0);
                document.getElementById('count-total').textContent = total;
            })
            .catch(error => {
                console.error('Error loading stats:', error);
            });
    </script>
    @endpush
</x-layouts.admin>
