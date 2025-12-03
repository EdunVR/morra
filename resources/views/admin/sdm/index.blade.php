<x-layouts.admin title="Dashboard SDM">
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard SDM</h1>
            <p class="text-sm text-slate-600 mt-1">Manajemen Sumber Daya Manusia</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class='bx bx-user-check text-2xl text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Karyawan Aktif</p>
                        <p class="text-2xl font-bold text-slate-900">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-briefcase text-2xl text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Departemen</p>
                        <p class="text-2xl font-bold text-slate-900">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <i class='bx bx-time text-2xl text-yellow-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Absensi Hari Ini</p>
                        <p class="text-2xl font-bold text-slate-900">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class='bx bx-money text-2xl text-purple-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Total Gaji</p>
                        <p class="text-2xl font-bold text-slate-900">Rp 0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Access Menu --}}
        <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Menu Cepat</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('sdm.kepegawaian.index') }}" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-primary-500 hover:bg-primary-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-primary-100 flex items-center justify-center">
                        <i class='bx bx-id-card text-2xl text-primary-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Kepegawaian & Rekrutmen</span>
                </a>

                <button onclick="showDemoModal('Penggajian / Payroll')" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-wallet text-2xl text-red-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Penggajian / Payroll</span>
                </button>

                <button onclick="showDemoModal('Manajemen Kinerja')" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-line-chart text-2xl text-red-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Manajemen Kinerja</span>
                </button>

                <button onclick="showDemoModal('Pelatihan & Pengembangan')" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-book-reader text-2xl text-red-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Pelatihan & Pengembangan</span>
                </button>

                <button onclick="showDemoModal('Manajemen Absensi & Waktu Kerja')" class="flex flex-col items-center gap-3 p-4 rounded-lg border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-time-five text-2xl text-red-600'></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 text-center">Absensi & Waktu Kerja</span>
                </button>
            </div>
        </div>

        {{-- Info --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class='bx bx-info-circle text-2xl text-blue-600'></i>
                <div>
                    <h3 class="font-semibold text-blue-900">Modul SDM</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Modul Kepegawaian & Rekrutmen sudah tersedia. Modul lainnya masih dalam tahap pengembangan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showDemoModal(title) {
            Alpine.store('demoModal').show(title);
        }
    </script>
    @endpush
</x-layouts.admin>
