<x-layouts.admin title="Setting COA Payroll">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Setting COA Payroll</h1>
                <p class="text-sm text-slate-600 mt-1">Konfigurasi akun untuk jurnal payroll otomatis</p>
            </div>
            <a href="{{ route('sdm.payroll.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 flex items-center gap-2">
                <i class='bx bx-arrow-back'></i>
                <span>Kembali</span>
            </a>
        </div>

        {{-- Info --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class='bx bx-info-circle text-2xl text-blue-600'></i>
                <div>
                    <h3 class="font-semibold text-blue-900">Informasi Jurnal Otomatis</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Saat payroll di-approve, sistem akan membuat jurnal untuk mencatat beban gaji dan hutang gaji.
                        Saat payroll dibayar, sistem akan membuat jurnal untuk mencatat pembayaran dari kas/bank.
                    </p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200">
            <form id="coaSettingForm">
                <div class="space-y-6">
                    {{-- Outlet Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Outlet <span class="text-red-500">*</span></label>
                        <select id="outlet_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required onchange="loadSettings()">
                            <option value="">Pilih Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>

                    {{-- Expense Accounts --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Akun Beban (Expense) - Debit saat Approve</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Beban Gaji Pokok <span class="text-red-500">*</span></label>
                                <select id="salary_expense_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    <option value="">Pilih Akun</option>
                                    @foreach($expenseAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Beban Lembur</label>
                                <select id="overtime_expense_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                    <option value="">Pilih Akun</option>
                                    @foreach($expenseAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Beban Bonus</label>
                                <select id="bonus_expense_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                    <option value="">Pilih Akun</option>
                                    @foreach($expenseAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Beban Tunjangan</label>
                                <select id="allowance_expense_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                    <option value="">Pilih Akun</option>
                                    @foreach($expenseAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Liability Accounts --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Akun Hutang (Liability) - Credit saat Approve</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Hutang Gaji <span class="text-red-500">*</span></label>
                                <select id="salary_payable_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    <option value="">Pilih Akun</option>
                                    @foreach($liabilityAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Hutang Pajak <span class="text-red-500">*</span></label>
                                <select id="tax_payable_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    <option value="">Pilih Akun</option>
                                    @foreach($liabilityAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Asset Accounts --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Akun Aset (Asset)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Piutang Pinjaman Karyawan</label>
                                <select id="loan_receivable_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                    <option value="">Pilih Akun</option>
                                    @foreach($assetAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-slate-500 mt-1">Debit saat approve (potongan pinjaman)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Kas/Bank <span class="text-red-500">*</span></label>
                                <select id="cash_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    <option value="">Pilih Akun</option>
                                    @foreach($assetAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-slate-500 mt-1">Credit saat pay (pembayaran gaji)</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Submit Button --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('sdm.payroll.index') }}" class="px-6 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Simpan Setting
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Jurnal Flow Info --}}
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Alur Jurnal Otomatis</h3>
            
            <div class="space-y-4">
                <div class="bg-white p-4 rounded-lg border border-slate-200">
                    <h4 class="font-semibold text-slate-900 mb-2">1. Saat Approve Payroll:</h4>
                    <div class="text-sm text-slate-700 space-y-1">
                        <p><strong>Debit:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Beban Gaji Pokok</li>
                            <li>Beban Lembur (jika ada)</li>
                            <li>Beban Bonus (jika ada)</li>
                            <li>Beban Tunjangan (jika ada)</li>
                            <li>Piutang Pinjaman Karyawan (jika ada potongan pinjaman)</li>
                        </ul>
                        <p class="mt-2"><strong>Credit:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Hutang Pajak (jika ada)</li>
                            <li>Hutang Gaji (net salary)</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200">
                    <h4 class="font-semibold text-slate-900 mb-2">2. Saat Pay Payroll:</h4>
                    <div class="text-sm text-slate-700 space-y-1">
                        <p><strong>Debit:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Hutang Gaji</li>
                        </ul>
                        <p class="mt-2"><strong>Credit:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Kas/Bank</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-load settings if only one outlet
            @if(count($outlets) === 1)
                $('#outlet_id').val('{{ $outlets[0]->id_outlet }}');
                loadSettings();
            @endif
        });

        async function loadSettings() {
            const outletId = $('#outlet_id').val();
            if (!outletId) return;

            try {
                const response = await fetch(`{{ route('sdm.payroll.coa.settings') }}?outlet_id=${outletId}`);
                const result = await response.json();

                if (result.success && result.data) {
                    const data = result.data;
                    $('#salary_expense_account_id').val(data.salary_expense_account_id || '');
                    $('#overtime_expense_account_id').val(data.overtime_expense_account_id || '');
                    $('#bonus_expense_account_id').val(data.bonus_expense_account_id || '');
                    $('#allowance_expense_account_id').val(data.allowance_expense_account_id || '');
                    $('#tax_payable_account_id').val(data.tax_payable_account_id || '');
                    $('#loan_receivable_account_id').val(data.loan_receivable_account_id || '');
                    $('#salary_payable_account_id').val(data.salary_payable_account_id || '');
                    $('#cash_account_id').val(data.cash_account_id || '');
                }
            } catch (error) {
                console.error('Error loading settings:', error);
            }
        }

        $('#coaSettingForm').on('submit', async function(e) {
            e.preventDefault();

            const data = {
                outlet_id: $('#outlet_id').val(),
                salary_expense_account_id: $('#salary_expense_account_id').val(),
                overtime_expense_account_id: $('#overtime_expense_account_id').val() || null,
                bonus_expense_account_id: $('#bonus_expense_account_id').val() || null,
                allowance_expense_account_id: $('#allowance_expense_account_id').val() || null,
                tax_payable_account_id: $('#tax_payable_account_id').val(),
                loan_receivable_account_id: $('#loan_receivable_account_id').val() || null,
                salary_payable_account_id: $('#salary_payable_account_id').val(),
                cash_account_id: $('#cash_account_id').val(),
                _token: '{{ csrf_token() }}'
            };

            try {
                const response = await fetch('{{ route('sdm.payroll.coa.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    window.location.href = '{{ route('sdm.payroll.index') }}';
                } else {
                    alert(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                alert('Gagal menyimpan setting');
            }
        });
    </script>
    @endpush
</x-layouts.admin>
