@extends('app')

@section('title')
Produk
@endsection

@section('breadcrumb')
@parent
<li class="active">Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addForm('{{ route('produk.store') }}')"
                        class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                    <button onclick="deleteSelected('{{ route('produk.delete_selected') }}')"
                        class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button>
                    <button onclick="cetakBarcode('{{ route('produk.cetak_barcode') }}')"
                        class="btn btn-info btn-xs btn-flat"><i class="fa fa-barcode"></i> Cetak Barcode</button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <form action="" method="post" class="form-produk">
                    @csrf
                    <table class="table table-stiped table-bordered table-produk">
                        <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th>Harga Jual</th>
                            <th>Diskon</th>
                            <th>Stok Total</th>
                            <th>Satuan</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

@includeif('produk.detail')
@includeIf('produk.form')
    @endsection

    @push('scripts')
        <script>
            let table, table1;

            $(function () {
                table = $('.table-produk').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('produk.data') }}',
                    },
                    columns: [{
                            data: 'select_all',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'kode_produk'
                        },
                        {
                            data: 'nama_produk'
                        },
                        {
                            data: 'nama_kategori'
                        },
                        {
                            data: 'merk'
                        },
                        {
                            data: 'harga_jual'
                        },
                        {
                            data: 'diskon'
                        },
                        {
                            data: 'stok'
                        },
                        {
                            data: 'nama_satuan'
                        },
                        {
                            data: 'aksi',
                            searchable: false,
                            sortable: false
                        },
                    ]
                });

                table1 = $('.table-detail').DataTable({
                    processing: true,
                    bSort: false,
                    dom: 'Brt',
                    destroy: true,
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, sortable: false},
                        {data: 'tanggal'},
                        {data: 'hpp'},
                        {data: 'stok'},
                    ]
                });

                $('#modal-form').validator().on('submit', function (e) {
                    if (!e.preventDefault()) {
                        $.ajax({
                            url: $('#modal-form form').attr('action'),
                            type: "POST",  // Ganti dengan PUT
                            data: $('#modal-form form').serialize(),
                            success: function(response) {
                                $('#modal-form').modal('hide');
                                table.ajax.reload();
                            },
                            error: function(errors) {
                                console.log(errors.responseJSON);
                                alert('Tidak dapat menyimpan data');
                                return;
                            }
                        });
                    }
                });

                $('[name=select_all]').on('click', function () {
                    $(':checkbox').prop('checked', this.checked);
                });
            });

            function showDetail(url) {
                $('#modal-detail').modal('show');
                table1.ajax.url(url);
                table1.ajax.reload();
            }

            function addForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Tambah Produk');

                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=nama_produk]').focus();
            }

            function editForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Edit Produk');

                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('PUT');
                $('#modal-form [name=nama_produk]').focus();

                $.get(url)
                    .done((response) => {
                        $('#modal-form [name=nama_produk]').val(response.nama_produk);
                        $('#modal-form [name=id_kategori]').val(response.id_kategori);
                        $('#modal-form [name=merk]').val(response.merk);
                        $('#modal-form [name=harga_jual]').val(response.harga_jual);
                        $('#modal-form [name=diskon]').val(response.diskon);
                        $('#modal-form [name=id_satuan]').val(response.id_satuan);
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menampilkan data');
                        return;
                    });
            }

            function deleteData(url) {
                if (confirm('Yakin ingin menghapus data terpilih?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            alert('Tidak dapat menghapus data');
                            return;
                        });
                }
            }

            function deleteSelected(url) {
                if ($('input:checked').length > 1) {
                    if (confirm('Yakin ingin menghapus data terpilih?')) {
                        $.post(url, $('.form-produk').serialize())
                            .done((response) => {
                                table.ajax.reload();
                            })
                            .fail((errors) => {
                                alert('Tidak dapat menghapus data');
                                return;
                            });
                    }
                } else {
                    alert('Pilih data yang akan dihapus');
                    return;
                }
            }

            function cetakBarcode(url) {
                if ($('input:checked').length < 1) {
                    alert('Pilih data yang akan dicetak');
                    return;
                } else if ($('input:checked').length < 3) {
                    alert('Pilih minimal 3 data untuk dicetak');
                    return;
                } else {
                    $('.form-produk')
                        .attr('target', '_blank')
                        .attr('action', url)
                        .submit();
                }
            }
        </script>
    @endpush