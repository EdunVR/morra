@extends('app')

@section('title')
    Transaksi Penjualan
@endsection

@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
    }

    .table-penjualan tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Transaksi Penjaualn</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                    
                <form class="form-produk">
                    @csrf
                    <div class="form-group row">
                                <label for="nama_member" class="col-lg-2 control-label">Customer</label>
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nama_member" value="{{ ! empty($memberSelected->nama ) ? $memberSelected->nama : 'Customer Umum' }}">
                                        <span class="input-group-btn">
                                            <button onclick="tampilMember()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                    <div class="form-group row">
                        <label for="kode_produk" class="col-lg-2">Kode Produk</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_penjualan" id="id_penjualan" value="{{ $id_penjualan }}">
                                <input type="hidden" name="id_produk" id="id_produk">
                                <input type="hidden" name="hpp" id="hpp">
                                <input type="hidden" name="stok" id="stok">
                                <input type="hidden" name="id_hpp" id="id_hpp">
                                <input type="text" class="form-control" name="kode_produk" id="kode_produk">
                                <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th width="15%">Jumlah</th>
                        <th>Diskon</th>
                        <th>Subtotal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-bayar bg-primary"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('transaksi.simpan', ['isChecked' => 'XXXX' ]) }}" class="form-penjualan" method="post">
                            @csrf
                            <input type="hidden" name="id_penjualan" value="{{ $id_penjualan }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="bayar" id="bayar">
                            <input type="hidden" name="piutangHidden" id="piutangHidden">
                            <input type="hidden" name="id_member" id="id_member" value="{{ $memberSelected->id_member }}">

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="piutang" class="col-lg-2 control-label">Piutang</label>
                                <div class="col-lg-8">
                                    <input type="text" id="piutang" class="form-control" value="{{ $memberSelected->piutang ?? 0 }}" readonly>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="bayarDenganPiutang"> Bayar dengan Piutang
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diskon" class="col-lg-2 control-label">Diskon</label>
                                <div class="col-lg-8">
                                    <input type="number" name="diskon" id="diskon" class="form-control" 
                                        value="{{ ! empty($memberSelected->id_member) ? $diskon : 0 }}" 
                                        >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bayarrp" class="col-lg-2 control-label">Bayar</label>
                                <div class="col-lg-8">
                                    <input type="text" id="bayarrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diterima" class="col-lg-2 control-label">Diterima</label>
                                <div class="col-lg-8">
                                    <input type="number" id="diterima" class="form-control" name="diterima" value="{{ $penjualan->diterima ?? 0 }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kembali" class="col-lg-2 control-label">Kembali</label>
                                <div class="col-lg-8">
                                    <input type="text" id="kembali" name="kembali" class="form-control" value="0" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i class="fa fa-floppy-o"></i> Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan_detail.detail')
@includeIf('penjualan_detail.produk')
@includeIf('penjualan_detail.member')
@endsection

@push('scripts')
<script>
    let table, table2, table3;
    let tipeMember = null;
    let selectedProductIds = [];

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('transaksi.data', $id_penjualan) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'diskon'},
                {data: 'subtotal'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            loadForm($('#diskon').val());
            setTimeout(() => {
                $('#diterima').trigger('input');
            }, 300);
        });
        table2 = $('.table-produk').DataTable();

        table3 = $('.table-detail').DataTable({
                    processing: true,
                    bSort: false,
                    dom: 'Brt',
                    destroy: true,
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, sortable: false},
                        {data: 'tanggal'},
                        {data: 'harga_jual'},
                        {data: 'hpp'},
                        {data: 'stok'},
                        {data: 'aksi', searchable: false, sortable: false},
                    ]
                });

        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let jumlah = parseInt($(this).val());

            if (jumlah < 1) {
                $(this).val(1);
                alert('Jumlah tidak boleh kurang dari 1');
                return;
            }
            if (jumlah > 10000) {
                $(this).val(10000);
                alert('Jumlah tidak boleh lebih dari 10000');
                return;
            }

            $.post(`{{ url('/transaksi') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah
                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload(() => loadForm($('#diskon').val()));
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });

        $(document).on('input', '#diskon', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($(this).val());
        });

        $('#diterima').on('input', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($('#diskon').val(), $(this).val());
        });

        $('.btn-simpan').on('click', function () {
            var isChecked = $('#bayarDenganPiutang').is(':checked') ? 'true' : 'false';
            var piutang = $('#piutangHidden').val();
            // Setelah transaksi disimpan, perbarui piutang
            $.post('{{ route('transaksi.updatePiutang') }}', {
                '_token': $('[name=csrf-token]').attr('content'),
                'id_member': $('#id_member').val(),
                'bayar': $('#bayar').val(), // Pastikan ini diisi dengan nilai yang benar
                'diterima': $('#diterima').val(), // Pastikan ini diisi dengan nilai yang benar
                'isChecked': isChecked,
                'piutang': piutang
            })
            .done(response => {
                $('.form-penjualan').submit();
                alert(response); // Tampilkan pesan sukses
            })
            .fail(errors => {
                console.log(errors);
                alert('Gagal memperbarui piutang');
            });
        });

        $('#bayarDenganPiutang').change(function() {
            var bayar = parseFloat($('#bayar').val()) || 0;
            var piutang = parseFloat($('#piutang').val()) || 0;
            var diterima = parseFloat($('#diterima').val()) || 0;
            var isChecked = "true";
            if ($(this).is(':checked')) {
                // Tambahkan piutang ke bayar
                //$('#bayarrp').val(bayar + piutang);
                //session('isChecked') === 'true';
                isChecked = "true";
            } else {
                //session('isChecked') === 'false';
                isChecked = "false";
                // Kembalikan bayar ke nilai semula (tanpa piutang)
                //$('#bayarrp').val(bayar);
            }
            
            loadForm($('#diskon').val(), diterima, isChecked);
        });
        

    });

    function tampilProduk() {
        $('#modal-produk').modal('show');
    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function pilihHarga(url, id) {
                $('#id_produk').val(id);
                hideProduk();
                $('#modal-detail').modal('show');
                table3.ajax.url(url);
                table3.ajax.reload();
            }

            // function pilihBahan(id, nama, harga_beli, stok, id_harga_bahan) {
            //     $('#id_bahan').val(id);
            //     $('#id_harga_bahan').val(id_harga_bahan);
            //     $('#nama_bahan').val(nama);
            //     $('#harga_beli').val(harga_beli);
            //     $('#stok').val(stok);
            //     $('#modal-detail').modal('hide');
            //     tambahBahan();
            // }

    function pilihProduk(id, kode, hpp, stok, id_hpp) {
        $('#id_produk').val(id);
        $('#id_hpp').val(id_hpp);
        $('#kode_produk').val(kode);
        $('#hpp').val(hpp);
        $('#stok').val(stok);
        // Validasi stok
        if (stok <= 0) {
            alert('Produk tidak dapat ditambahkan karena stok habis.');
            return; // Menghentikan eksekusi jika stok 0
        }

        if (!selectedProductIds.includes(id)) {
            selectedProductIds.push(id);
        }

        console.log("Produk dipilih:", selectedProductIds);

        $('#modal-detail').modal('hide');
        tambahProduk();
    }

    function tambahProduk() {
        const id_member = $('#id_member').val();
        $.post('{{ route('transaksi.store') }}', {
            ...$('.form-produk').serializeArray().reduce((obj, item) => {
                obj[item.name] = item.value;
                return obj;
            }, {}),
            id_member: id_member // Tambahkan id_member
        })
            .done(response => {
                $('#kode_produk').focus();
                table.ajax.reload(() => loadForm($('#diskon').val()));
            })
            .fail(errors => {
                console.log(errors);
                alert('Tidak dapat menyimpan data');
                return;
            });
    }

    function tampilMember() {
        $('#modal-member').modal('show');
    }

    function pilihMember(id, nama, piutang, tipe_id) {
        $('#id_member').val(id);
        $('#nama_member').val(nama);
        $('#piutang').val(piutang);
        $('#piutangHidden').val(piutang);
        tipeMember = tipe_id;

        var id_member = $('#id_member').val();
        var produkIds = [];

        // $('.table-penjualan tbody tr').slice(0, -1).each(function(index) {
        //     var id_produk = selectedProductIds[index];
        //     console.log(id_produk);
        //     if (id_produk) {
        //         updateDiscount(id_produk, id, $(this)); // Kirim elemen baris untuk update
        //     }
        // });
        $.ajax({
            url: '{{ route('hapus.produk') }}',
            type: 'POST',
            data: {
                _token: $('[name=csrf-token]').attr('content'), // CSRF token
                id_penjualan: $('#id_penjualan').val() // ID transaksi saat ini
            },
            success: function(response) {
                console.log("Semua produk terhapus dari database.");
                
                // Reset produk terpilih di frontend
                selectedProductIds = [];
                $('.table-penjualan tbody').empty(); // Hapus tampilan tabel

                console.log("Produk telah direset setelah memilih member.");
            },
            error: function(xhr, status, error) {
                console.log("Gagal menghapus produk:", error);
            }
        });

        // selectedProductIds = []; // Kosongkan array
        // $('.table-penjualan tbody').empty(); // Reload tabel untuk menghapus data yang ditampilkan

        // // Reset input produk
        // $('#id_produk').val('');
        // $('#kode_produk').val('');

        loadForm($('#diskon').val());
        $('#diterima').val(0).focus().select();

        hideMember();
    }

    function hideMember() {
        $('#modal-member').modal('hide');
    }

    // function updateDiscount(id_produk, id_member, row) {
    //     $.get('{{ route('getDiscount') }}', { id_produk, id_member })
    //         .done(response => {
    //             const diskon = response.diskon;
    //             const harga_jual = parseFloat(row.find('.harga_jual').data('harga')); // Ambil harga dari data attribute
    //             // const subtotal = harga_jual - (diskon / 100 * harga_jual);
    //             var subtotal = harga_jual * parseInt(row.find('.jumlah').val()) - (diskon / 100 * harga_jual * parseInt(row.find('.jumlah').val()));
                
    //             // Update diskon dan subtotal di baris
    //             row.find('.diskon').text(diskon + '%');
    //             row.find('.subtotal').text('XXX');
    //         })
    //         .fail(errors => {
    //             console.log(errors);
    //             alert('Error fetching discount');
    //         });
    // }

    function deleteData(url, id) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    selectedProductIds = selectedProductIds.filter(item => item !== id);
                    console.log("Produk setelah dihapus:", selectedProductIds);
                    table.ajax.reload(() => loadForm($('#diskon').val()));
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function loadForm(diskon = 0, diterima = 0) {
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());

        var piutang = parseFloat($('#piutangHidden').val()) || 0; // Ambil nilai piutang
        var isChecked = $('#bayarDenganPiutang').is(':checked') ? 'true' : 'false';
        //var isChecked = $('#bayarDenganPiutang').is(':checked');

        $.get(`{{ url('/transaksi/loadform') }}/${diskon}/${$('.total').text()}/${diterima}/${piutang}/${isChecked}`)
            .done(response => {
                $('#totalrp').val(response.totalrp);
                $('#bayar').val(response.bayar);
                $('#piutang').val(response.piutang);
                $('#bayarrp').val(response.bayarrp);
                $('.tampil-bayar').text('Bayar: ' + response.bayarrp);
                $('.tampil-terbilang').text(response.terbilang);

                $('#kembali').val(response.kembalirp);
                if ($('#diterima').val() != 0) {
                    $('.tampil-bayar').text('Kembali: ' + response.kembalirp);
                    $('.tampil-terbilang').text(response.kembali_terbilang);
                }
            })
            .fail(errors => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

</script>
@endpush