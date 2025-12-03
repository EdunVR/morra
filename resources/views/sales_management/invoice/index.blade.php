@extends('app')

@section('title')
    Buat Invoice Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Buat Invoice Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <a href="{{ route('sales.invoice.history') }}" class="btn btn-success btn-sm" style="margin-right: 10px;">
                            <i class="fa fa-list"></i> History Invoice
                        </a>
                        <a href="{{ route('sales.coa.setting') }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-cog"></i> Setting COA
                        </a>
                        <a href="{{ route('sales.ongkos-kirim.index') }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-cog"></i> Setting Ongkir
                        </a>
                        <a href="{{ route('sales.customer-price.index') }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-cog"></i> Setting Harga Customer
                        </a>
                    </div>
                </div>
                
                <form id="invoice-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_display" placeholder="Pilih Customer..." readonly>
                                    <input type="hidden" name="customer_type" id="customer_type">
                                    <input type="hidden" name="customer_id" id="customer_id">
                                    <span class="input-group-btn">
                                        <button type="button" onclick="tampilCustomer()" class="btn btn-info btn-flat">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Harga Khusus Customer</label>
                                <div>
                                    <button type="button" class="btn btn-warning btn-block" id="btn-harga-khusus" disabled onclick="tampilProdukHargaKhusus()">
                                        <i class="fa fa-tags"></i> 
                                        <span id="count-produk">0 Produk Khusus</span>
                                    </button>
                                    <input type="hidden" name="id_customer_price" id="id_customer_price">
                                    <small class="text-muted" id="info-harga-khusus">Pilih customer terlebih dahulu</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal">Tanggal Invoice</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="keterangan">Keterangan (Opsional)</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Tambahkan keterangan invoice..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h4>Detail Invoice</h4>
                            <table class="table table-bordered" id="invoice-items">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Deskripsi</th>
                                        <th width="20%">Keterangan</th>
                                        <th width="10%">Kuantitas</th>
                                        <th width="10%">Satuan</th>
                                        <th width="15%">Harga</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Items will be added automatically -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right"><strong>Total</strong></td>
                                        <td><strong id="total-amount">0</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-info" id="add-produk">
                                <i class="fa fa-plus"></i> Tambah Produk
                            </button>
                            <button type="button" class="btn btn-info" id="add-ongkir">
                                <i class="fa fa-truck"></i> Tambah Ongkir
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Simpan Invoice
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-preview">
                                <i class="fa fa-eye"></i> Preview
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Customer -->
<div class="modal fade" id="modal-customer" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pilih Customer</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" id="search-customer" class="form-control" placeholder="Cari customer...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th>Tipe</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="customer-list">
                            <!-- Data customer akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div id="customer-pagination" class="text-center" style="display: none;">
                    <nav>
                        <ul class="pagination pagination-sm" id="pagination-links">
                            <!-- Pagination links akan diisi oleh JavaScript -->
                        </ul>
                    </nav>
                    <div class="text-muted" id="pagination-info"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Produk -->
<div class="modal fade" id="modal-produk" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal-produk-title">Pilih Produk</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" id="search-produk" class="form-control" placeholder="Cari produk...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="produk-list">
                            <!-- Data produk akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="modal-preview" tabindex="-1" role="dialog" style="z-index: 1060;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Preview Invoice & Jurnal</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tab-invoice" aria-controls="tab-invoice" role="tab" data-toggle="tab">
                            <i class="fa fa-file-text"></i> Preview Invoice
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#tab-journal" aria-controls="tab-journal" role="tab" data-toggle="tab">
                            <i class="fa fa-book"></i> Preview Jurnal
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content" style="padding-top: 20px;">
                    <div role="tabpanel" class="tab-pane active" id="tab-invoice">
                        <div id="invoice-preview">
                            <!-- Invoice preview akan diisi oleh JavaScript -->
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-journal">
                        <div id="journal-preview">
                            <div class="text-center">
                                <i class="fa fa-spinner fa-spin"></i> Memuat preview jurnal...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="$('#invoice-form').submit()">
                    <i class="fa fa-save"></i> Simpan Invoice
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentCustomerPrice = null;
        let currentOngkosKirim = null;
        let currentCustomerPriceId = null;
        let produkHargaKhusus = [];
        let semuaProduk = [];

    $(document).ready(function() {

        // Search produk
        $('#search-produk').on('input', function() {
            const searchTerm = $(this).val();
            const isHargaKhusus = $('#modal-produk-title').text().includes('Harga Khusus');
            
            if (isHargaKhusus) {
                loadProdukList(produkHargaKhusus, searchTerm);
            } else {
                loadSemuaProduk(searchTerm);
            }
        });

        // Button event handlers
        $('#add-produk').click(function() {
            tampilModalProduk(false);
        });

        $('#add-ongkir').click(function() {
            const hargaOngkir = currentOngkosKirim && currentOngkosKirim.harga ? currentOngkosKirim.harga : 0;
            const daerah = currentOngkosKirim && currentOngkosKirim.daerah ? currentOngkosKirim.daerah : 'Default';
            
            addItemRow({
                deskripsi: 'Ongkos Kirim - ' + daerah,
                keterangan: 'Pengiriman ke ' + daerah,
                kuantitas: 1,
                satuan: 'Trip',
                harga: hargaOngkir,
                subtotal: hargaOngkir,
                tipe: 'ongkir'
            });
        });

        // Preview invoice (sebelum disimpan) - DIPERBAIKI dengan modal
        $('#btn-preview').click(function() {
            // Validasi form dasar
            if (!$('#customer_id').val()) {
                alert('Pilih customer terlebih dahulu');
                return;
            }

            if ($('#invoice-items tbody tr').length === 0) {
                alert('Tambahkan minimal satu item ke invoice');
                return;
            }

            // Tampilkan modal preview
            $('#modal-preview').modal('show');
            
            // Generate preview data
            const formData = generateFormData();
            
            // Tampilkan preview invoice
            showInvoicePreview(formData);
            
            // Load preview jurnal
            loadJournalPreview(formData.total);
        });

        // Generate form data untuk preview
        function generateFormData() {
            const items = [];
            let total = 0;

            $('#invoice-items tbody tr').each(function() {
                const row = $(this);
                const item = {
                    deskripsi: row.find('input[name="items[deskripsi][]"]').val(),
                    keterangan: row.find('input[name="items[keterangan][]"]').val(),
                    kuantitas: parseFloat(row.find('input[name="items[kuantitas][]"]').val()) || 1,
                    satuan: row.find('input[name="items[satuan][]"]').val(),
                    harga: parseNumber(row.find('input[name="items[harga][]"]').val()),
                    subtotal: parseNumber(row.find('input[name="items[subtotal][]"]').val()),
                    tipe: row.find('input[name="items[tipe][]"]').val(),
                };
                items.push(item);
                total += item.subtotal;
            });

            return {
                customer: $('#customer_display').val(),
                tanggal: $('#tanggal').val(),
                keterangan: $('#keterangan').val(),
                items: items,
                total: total
            };
        }

        // Show invoice preview
        function showInvoicePreview(data) {
            const invoicePreview = $('#invoice-preview');
            let itemsHTML = '';
            
            data.items.forEach((item, index) => {
                itemsHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.deskripsi}</td>
                        <td>${item.keterangan || '-'}</td>
                        <td>${item.kuantitas}</td>
                        <td>${item.satuan || '-'}</td>
                        <td class="text-right">Rp ${formatNumber(item.harga)}</td>
                        <td class="text-right">Rp ${formatNumber(item.subtotal)}</td>
                    </tr>
                `;
            });
            
            invoicePreview.html(`
                <div class="header text-center">
                    <h3>PREVIEW INVOICE PENJUALAN</h3>
                    <p class="text-muted">Preview - Belum Disimpan</p>
                </div>

                <div class="invoice-info">
                    <table class="table table-bordered">
                        <tr>
                            <td width="30%"><strong>Customer</strong></td>
                            <td>${data.customer}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>${data.tanggal}</td>
                        </tr>
                    </table>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Deskripsi</th>
                            <th>Keterangan</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHTML}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-right"><strong>Total</strong></td>
                            <td class="text-right"><strong>Rp ${formatNumber(data.total)}</strong></td>
                        </tr>
                    </tfoot>
                </table>

                ${data.keterangan ? `
                <div class="keterangan">
                    <strong>Keterangan:</strong> ${data.keterangan}
                </div>
                ` : ''}
            `);
        }

        // Load journal preview
        function loadJournalPreview(total) {
            $.post('{{ route("sales.invoice.preview-journal-before-save") }}', {
                _token: '{{ csrf_token() }}',
                total: total,
                customer_type: $('#customer_type').val(),
                customer_id: $('#customer_id').val()
            }, function(response) {
                if (response.success) {
                    showJournalPreview(response.preview);
                } else {
                    $('#journal-preview').html(`
                        <div class="alert alert-warning">
                            <i class="fa fa-warning"></i> ${response.message || 'Gagal memuat preview jurnal'}
                        </div>
                    `);
                }
            }).fail(function(xhr) {
                $('#journal-preview').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-times"></i> Error loading journal preview
                    </div>
                `);
            });
        }

        // Show journal preview
        function showJournalPreview(preview) {
            const journalPreview = $('#journal-preview');
            let entriesHTML = '';
            
            if (preview.entries && preview.entries.length > 0) {
                preview.entries.forEach(entry => {
                    entriesHTML += `
                        <tr>
                            <td>${entry.account_code}</td>
                            <td>${entry.account_name}</td>
                            <td class="text-right">${entry.debit > 0 ? 'Rp ' + formatNumber(entry.debit) : '-'}</td>
                            <td class="text-right">${entry.credit > 0 ? 'Rp ' + formatNumber(entry.credit) : '-'}</td>
                        </tr>
                    `;
                });
                
                const totalDebit = preview.entries.reduce((sum, entry) => sum + parseFloat(entry.debit), 0);
                const totalCredit = preview.entries.reduce((sum, entry) => sum + parseFloat(entry.credit), 0);
                
                journalPreview.html(`
                    <h4>Preview Jurnal Transaksi</h4>
                    <div class="alert alert-info">
                        <strong>Keterangan:</strong> ${preview.description}<br>
                        <strong>Total:</strong> Rp ${formatNumber(preview.total)}<br>
                        <strong>Status:</strong> ${preview.status.toUpperCase()}
                    </div>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="15%">Kode Akun</th>
                                <th width="45%">Nama Akun</th>
                                <th width="20%" class="text-right">Debit</th>
                                <th width="20%" class="text-right">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${entriesHTML}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                <td class="text-right"><strong>Rp ${formatNumber(totalDebit)}</strong></td>
                                <td class="text-right"><strong>Rp ${formatNumber(totalCredit)}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    ${preview.is_balanced ? 
                        '<div class="alert alert-success"><i class="fa fa-check"></i> Jurnal balanced dan siap dibuat.</div>' :
                        '<div class="alert alert-warning"><i class="fa fa-warning"></i> Jurnal tidak balanced!</div>'
                    }
                `);
            } else {
                journalPreview.html(`
                    <div class="alert alert-warning">
                        <i class="fa fa-info-circle"></i> Tidak ada jurnal yang akan dibuat karena akun yang diperlukan belum diatur.
                    </div>
                `);
            }
        }

        // Handle form submission
        $('#invoice-form').submit(function(e) {
            e.preventDefault();
            
            const formData = {
                _token: $('input[name=_token]').val(),
                tanggal: $('#tanggal').val(),
                customer_type: $('#customer_type').val(),
                customer_id: $('#customer_id').val(),
                id_customer_price: $('#id_customer_price').val(),
                keterangan: $('#keterangan').val(),
                items: []
            };
            
            $('#invoice-items tbody tr').each(function() {
                var row = $(this);
                const item = {
                    id_produk: row.find('input[name="items[id_produk][]"]').val() || null,
                    deskripsi: row.find('input[name="items[deskripsi][]"]').val(),
                    keterangan: row.find('input[name="items[keterangan][]"]').val(),
                    kuantitas: parseFloat(row.find('input[name="items[kuantitas][]"]').val()) || 1,
                    satuan: row.find('input[name="items[satuan][]"]').val() || '',
                    harga: parseNumber(row.find('input[name="items[harga][]"]').val()),
                    subtotal: parseNumber(row.find('input[name="items[subtotal][]"]').val()),
                    tipe: row.find('input[name="items[tipe][]"]').val(),
                };
                
                formData.items.push(item);
            });
            
            $.ajax({
                url: '{{ route("sales.invoice.store") }}',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
                        alert('Invoice penjualan berhasil dibuat');
                        window.location.href = '{{ route("sales.invoice.history") }}';
                    } else {
                        alert('Gagal membuat invoice: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(error) {
                    let errorMessage = 'Terjadi kesalahan';
                    if (error.responseJSON && error.responseJSON.message) {
                        errorMessage = error.responseJSON.message;
                    }
                    if (error.responseJSON && error.responseJSON.errors) {
                        const errors = error.responseJSON.errors;
                        errorMessage = 'Error Validasi:\n';
                        for (const key in errors) {
                            errorMessage += `• ${key}: ${errors[key].join(', ')}\n`;
                        }
                    }
                    alert(errorMessage);
                }
            });
        });

        
    });

    // ========== FUNGSI CUSTOMER MANAGEMENT ==========
    let currentPage = 1;
    let totalPages = 1;
    let currentSearch = '';

    // Format number helper
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Parse number helper
        function parseNumber(formattedNumber) {
            if (typeof formattedNumber === 'number') {
                return formattedNumber;
            }
            if (typeof formattedNumber !== 'string') {
                return 0;
            }
            const cleanNumber = formattedNumber.toString().replace(/\./g, '');
            return parseInt(cleanNumber) || 0;
        }

    // Load customers dengan pagination
    function loadCustomers(search = '', page = 1) {
        currentSearch = search;
        currentPage = page;
        
        $.get('{{ route("sales.get-customers") }}', { 
            search: search,
            page: page 
        }, function(response) {
            if (response.success) {
                const tbody = $('#customer-list');
                tbody.empty();
                
                if (response.customers.length > 0) {
                    response.customers.forEach(customer => {
                        const namaEscaped = customer.nama ? customer.nama.replace(/'/g, "\\'") : 'N/A';
                        const telepon = customer.telepon || '-';
                        const alamat = customer.alamat || '-';
                        
                        tbody.append(`
                            <tr>
                                <td>${customer.nama || 'N/A'}</td>
                                <td>${telepon}</td>
                                <td>${alamat}</td>
                                <td>${customer.type === 'member' ? 'Member' : 'Prospek'}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs" 
                                            onclick="pilihCustomer('${customer.type}', '${customer.id}', '${namaEscaped}')">
                                        <i class="fa fa-check"></i> Pilih
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                    
                    updatePagination(response.pagination);
                    
                } else {
                    tbody.append('<tr><td colspan="5" class="text-center">Tidak ada data customer</td></tr>');
                    $('#customer-pagination').hide();
                }
            } else {
                $('#customer-list').html('<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>');
                $('#customer-pagination').hide();
            }
        }).fail(function(xhr, status, error) {
            $('#customer-list').html('<tr><td colspan="5" class="text-center">Error loading data: ' + error + '</td></tr>');
            $('#customer-pagination').hide();
        });
    }

    // Update pagination UI
    function updatePagination(pagination) {
        const paginationContainer = $('#customer-pagination');
        const paginationLinks = $('#pagination-links');
        const paginationInfo = $('#pagination-info');
        
        if (pagination.total > pagination.per_page) {
            paginationContainer.show();
            
            paginationInfo.html(
                `Menampilkan ${pagination.from} - ${pagination.to} dari ${pagination.total} customer`
            );
            
            paginationLinks.empty();
            
            // Previous button
            const prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
            paginationLinks.append(`
                <li class="${prevDisabled}">
                    <a href="#" onclick="loadCustomers(currentSearch, ${pagination.current_page - 1}); return false;">
                        <span>&laquo;</span>
                    </a>
                </li>
            `);
            
            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                const active = i === pagination.current_page ? 'active' : '';
                paginationLinks.append(`
                    <li class="${active}">
                        <a href="#" onclick="loadCustomers(currentSearch, ${i}); return false;">${i}</a>
                    </li>
                `);
            }
            
            // Next button
            const nextDisabled = pagination.current_page === pagination.last_page ? 'disabled' : '';
            paginationLinks.append(`
                <li class="${nextDisabled}">
                    <a href="#" onclick="loadCustomers(currentSearch, ${pagination.current_page + 1}); return false;">
                        <span>&raquo;</span>
                    </a>
                </li>
            `);
            
        } else {
            paginationContainer.hide();
            if (pagination.total > 0) {
                paginationInfo.html(`Menampilkan semua ${pagination.total} customer`);
                paginationContainer.show();
            }
        }
    }

    // Load customers saat modal dibuka
    function tampilCustomer() {
        $('#modal-customer').modal('show');
        $('#search-customer').val('');
        currentSearch = '';
        currentPage = 1;
        loadCustomers('', 1);
    }

    // Search customer dengan delay
    let searchTimeout;
    $('#search-customer').on('input', function() {
        const searchTerm = $(this).val();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadCustomers(searchTerm, 1);
        }, 500);
    });

    // Global function untuk pilih customer
    window.pilihCustomer = function(type, id, nama) {
        $('#customer_type').val(type);
        $('#customer_id').val(id);
        $('#customer_display').val(nama);
        $('#modal-customer').modal('hide');
        
        // Reset search dan pagination
        $('#search-customer').val('');
        currentSearch = '';
        currentPage = 1;
        
        // Reset invoice items dan load customer prices
        resetInvoiceItems();
        loadCustomerPrices(id, type);
    }

    // ========== FUNGSI INVOICE ITEMS ==========
    
    // Add item row function
    function addItemRow(item) {
        var rowCount = $('#invoice-items tbody tr').length;
        
        const harga = parseInt(item.harga) || 0;
        const subtotal = parseInt(item.subtotal) || 0;
        
        var row = '<tr>' +
            '<td>' + (rowCount + 1) + '</td>' +
            '<td><input type="text" name="items[deskripsi][]" class="form-control" value="' + (item.deskripsi || '') + '" required></td>' +
            '<td><input type="text" name="items[keterangan][]" class="form-control" value="' + (item.keterangan || '') + '"></td>' +
            '<td><input type="number" name="items[kuantitas][]" class="form-control quantity" value="' + (parseInt(item.kuantitas) || 1) + '" min="0.01" step="0.01" required></td>' +
            '<td><input type="text" name="items[satuan][]" class="form-control" value="' + (item.satuan || '') + '"></td>' +
            '<td><input type="text" name="items[harga][]" class="form-control price" value="' + formatNumber(harga) + '" required></td>' +
            '<td><input type="text" name="items[subtotal][]" class="form-control subtotal" value="' + formatNumber(subtotal) + '" readonly></td>' +
            '<td>' +
                '<input type="hidden" name="items[tipe][]" value="' + (item.tipe || 'produk') + '">' +
                '<input type="hidden" name="items[id_produk][]" value="' + (item.id_produk || '') + '">' +
                '<button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-trash"></i></button>' +
            '</td>' +
            '</tr>';
        
        $('#invoice-items tbody').append(row);
        
        var newRow = $('#invoice-items tbody tr:last');
        
        newRow.find('.quantity, .price').on('input', function() {
            updateRowTotal(newRow);
        });
        
        newRow.find('.price').on('blur', function() {
            formatCurrency(this);
        });
        
        newRow.find('.remove-row').click(function() {
            $(this).closest('tr').remove();
            updateTotal();
            renumberRows();
        });
        
        updateTotal();
    }

    // Update row total
    function updateRowTotal(row) {
        let quantity = parseFloat(row.find('.quantity').val()) || 0;
        let price = parseNumber(row.find('.price').val());
        let subtotal = quantity * price;
        
        row.find('.subtotal').val(formatNumber(subtotal));
        updateTotal();
    }

    // Update total
    function updateTotal() {
        let total = 0;
        $('#invoice-items tbody tr').each(function() {
            let subtotal = parseNumber($(this).find('.subtotal').val());
            total += subtotal;
        });
        
        $('#total-amount').text(formatNumber(total));
    }

    // Renumber rows
    function renumberRows() {
        $('#invoice-items tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // Format currency
    function formatCurrency(input) {
        let value = input.value.replace(/\./g, '');
        if (!isNaN(value)) {
            const intValue = parseInt(value) || 0;
            input.value = formatNumber(intValue);
        }
    }

    // Reset form saat customer berubah
        function resetInvoiceItems() {
            $('#invoice-items tbody').empty();
            $('#total-amount').text('0');
            currentOngkosKirim = null;
            produkHargaKhusus = [];
            updateTotal();
        }

        // Load customer prices saat customer dipilih - DIPERBAIKI
        function loadCustomerPrices(customerId, customerType) {
            $.get('{{ url("sales-management/get-customer-prices") }}/' + customerId + '/' + customerType, function(response) {
                console.log('Customer prices response:', response);
                
                $('#btn-harga-khusus').prop('disabled', true);
                $('#count-produk').text('0 Produk Khusus');
                $('#info-harga-khusus').text('Tidak ada harga khusus');
                currentCustomerPriceId = null;

                if (response && response.length > 0) {
                    // Ambil customer price pertama
                    const customerPrice = response[0];
                    currentCustomerPriceId = customerPrice.id_customer_price;
                    $('#id_customer_price').val(currentCustomerPriceId);
                    
                    // Simpan data ongkos kirim
                    currentOngkosKirim = customerPrice.ongkos_kirim;
                    
                    // Simpan produk harga khusus - DIPERBAIKI: gunakan properti yang benar
                    produkHargaKhusus = customerPrice.produk || [];
                    
                    // console.log('Produk harga khusus:', produkHargaKhusus);
                    // console.log('Ongkos kirim:', currentOngkosKirim);
                    
                    // Update tombol harga khusus
                    $('#btn-harga-khusus').prop('disabled', false);
                    $('#count-produk').text(produkHargaKhusus.length + ' Produk Khusus');
                    $('#info-harga-khusus').text('Klik untuk lihat produk dengan harga khusus');
                    
                } else {
                    $('#id_customer_price').val('');
                    $('#info-harga-khusus').text('Tidak ada harga khusus untuk customer ini');
                }
            }).fail(function(xhr, status, error) {
                console.error('Error loading customer prices:', error);
                $('#info-harga-khusus').text('Error loading harga khusus');
            });
        }

        // Tampilkan modal produk (untuk harga khusus dan semua produk)
        function tampilModalProduk(isHargaKhusus = false) {
            const modal = $('#modal-produk');
            const title = isHargaKhusus ? 'Pilih Produk (Harga Khusus)' : 'Pilih Produk (Semua Produk)';
            
            $('#modal-produk-title').text(title);
            modal.modal('show');
            
            // Load produk berdasarkan jenis
            if (isHargaKhusus) {
                loadProdukList(produkHargaKhusus, '');
            } else {
                loadSemuaProduk('');
            }
        }

        // Load semua produk
        function loadSemuaProduk(search = '') {
            $.get('{{ route("sales.get-produk-harga-normal") }}', { 
                search: search 
            }, function(response) {
                if (response.success) {
                    semuaProduk = response.produks;
                    displayProdukList(semuaProduk);
                }
            });
        }

        // Load produk harga khusus - DIPERBAIKI
        function loadProdukList(produks, search = '') {
            let filteredProduks = produks;
            
            if (search) {
                filteredProduks = produks.filter(produk => 
                    produk.nama_produk.toLowerCase().includes(search.toLowerCase()) ||
                    (produk.kode_produk && produk.kode_produk.toLowerCase().includes(search.toLowerCase()))
                );
            }
            
            displayProdukList(filteredProduks);
        }

        // Display produk list di modal - DIPERBAIKI
        function displayProdukList(produks) {
            const tbody = $('#produk-list');
            tbody.empty();
            
            if (produks.length > 0) {
                produks.forEach(function(produk) {
                    // Handle struktur data yang berbeda antara harga khusus dan normal
                    const harga = parseFloat(produk.pivot ? produk.pivot.harga_khusus : produk.harga) || 0;
                    const kodeProduk = produk.kode_produk || '-';
                    const namaProduk = produk.nama_produk || 'N/A';
                    let satuan = 'Unit';
                    if (typeof produk.satuan === 'string') {
                        satuan = produk.satuan;
                    } else if (produk.satuan && produk.satuan.nama_satuan) {
                        satuan = produk.satuan.nama_satuan;
                    } else if (produk.formatted_satuan) {
                        satuan = produk.formatted_satuan;
                    }
                    const idProduk = produk.id_produk;
                    const stok = parseInt(produk.stok) || 0;
                    const stokTersedia = parseInt(produk.stok) || 0;

                    console.log('Produk data:', produk);

                    let stokBadgeClass = 'success';
                    let stokText = stokTersedia;
                    
                    if (stokTersedia <= 0) {
                        stokBadgeClass = 'danger';
                        stokText = 'Habis';
                    } else if (stokTersedia < 10) {
                        stokBadgeClass = 'warning';
                        stokText = stokTersedia;
                    }
                    
                    // Tentukan apakah produk bisa dipilih
                    const canSelect = stokTersedia > 0;
                    const buttonClass = canSelect ? 'btn-primary' : 'btn-default';
                    const buttonText = canSelect ? 'Pilih' : 'Stok Habis';
                    const buttonDisabled = !canSelect ? 'disabled' : '';
                    
                    tbody.append(`
                        <tr>
                            <td>${kodeProduk}</td>
                            <td>${namaProduk}</td>
                            <td class="text-right">Rp ${formatNumber(harga)}</td>
                            <td class="text-center">
                                <span class="label label-${stokBadgeClass}">
                                    ${stokText}
                                </span>
                                ${stokTersedia > 0 && stokTersedia !== stok ? 
                                    `<br><small class="text-muted">Dari ${stok} ${satuan}</small>` : ''
                                }
                            </td>
                            <td>${satuan}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-xs" 
                                        onclick="pilihProduk(
                                            '${idProduk}', 
                                            '${namaProduk.replace(/'/g, "\\'")}', 
                                            ${harga}, 
                                            '${satuan}'
                                        )">
                                    <i class="fa fa-check"></i> Pilih
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="5" class="text-center">Tidak ada data produk</td></tr>');
            }
        }

        // Global function untuk pilih produk
        window.pilihProduk = function(id, nama, harga, satuan) {
            const isHargaKhusus = $('#modal-produk-title').text().includes('Harga Khusus');
            const produks = isHargaKhusus ? produkHargaKhusus : semuaProduk;
            const produk = produks.find(p => p.id_produk == id);
            console.log(produk);
            if (produk) {
                const stokTersedia = parseInt(produk.stok) || 0;
                
                if (stokTersedia <= 0) {
                    alert('Maaf, stok produk ' + nama + ' sudah habis');
                    return;
                }
                
                // Tampilkan konfirmasi jika stok rendah
                if (stokTersedia < 10) {
                    if (!confirm('Stok ' + nama + ' hanya tersisa ' + stokTersedia + ' ' + satuan + '. Lanjutkan?')) {
                        return;
                    }
                }
            }

            addItemRow({
                id_produk: id,
                deskripsi: nama,
                keterangan: 'Penjualan ' + nama,
                kuantitas: 1,
                satuan: satuan,
                harga: harga,
                subtotal: harga,
                tipe: 'produk'
            });
            $('#modal-produk').modal('hide');
        }

        // Tampilkan produk harga khusus
        window.tampilProdukHargaKhusus = function() {
            if (produkHargaKhusus.length === 0) {
                alert('Tidak ada produk dengan harga khusus untuk customer ini');
                return;
            }
            tampilModalProduk(true);
        }
</script>
@endpush
