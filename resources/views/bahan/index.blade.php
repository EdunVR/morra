@extends('app')

@section('title')
Bahan
@endsection

@section('breadcrumb')
@parent
<li class="active">Bahan</li>
@endsection

@section('content')
<!-- Main row -->
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addForm('{{ route('bahan.store') }}')"
                        class="btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah Bahan</button>
                    <button onclick="deleteSelected('{{ route('bahan.delete_selected') }}')"
                        class="btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i>Hapus Terpilih</button>
                </div>

            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-bahan">
                    <thead>
                        <th width="5%">
                            <input type="checkbox" name="select-all" id="select-all">
                        </th>
                        <th width="5%">No</th>
                        <th>Nama Bahan</th>
                        <th>Merk</th>
                        <th>Stok Total</th>
                        <th>Satuan</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                </table>
            </div>

        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

@includeif('bahan.detail')
@includeif('bahan.form')
    @endsection

    @push('scripts')
        <script>
            let table, table1;
            $(function () {
                table = $('.table-bahan').DataTable({
                    processing: true,
                    autoWidth: false,
                    ajax: '{{ route('bahan.data') }}',
                    columns: [{
                            data: 'select-all',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'nama_bahan'
                        },
                        {
                            data: 'merk'
                        },
                        {
                            data: 'stok'
                        },
                        {
                            data: 'nama_satuan'
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
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
                        {data: 'harga_beli'},
                        {data: 'stok'},
                    ]
                });

                $('#modal-form form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...
                    } else {
                        e.preventDefault();
                        $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                            .done((response) => {
                                $('#modal-form').modal('hide');
                                table.ajax.reload();
                            })
                            .fail((errors) => {
                                console.log(errors);
                                alert('Tidak dapat menyimpan data');
                                return;
                            });
                    }
                })

                $('#select-all').on('click', function (e) {
                    if ($(this).is(':checked')) {
                        $('.table').find('tbody').find('input[type=checkbox]').prop('checked', true);
                    } else {
                        $('.table').find('tbody').find('input[type=checkbox]').prop('checked', false);
                    }
                })
            });

            function showDetail(url) {
                $('#modal-detail').modal('show');
                table1.ajax.url(url);
                table1.ajax.reload();
            }

            function addForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Tambah Bahan');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=nama_bahan]').focus();
            }

            function editForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Edit Bahan');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('put');
                $('#modal-form [name=nama_bahan]').focus();

                $.get(url)
                    .done((response) => {
                        console.log(response);
                        $('#modal-form [name=nama_bahan]').val(response.nama_bahan);
                        $('#modal-form [name=merk]').val(response.merk);
                        $('#modal-form [name=harga_beli]').val(response.harga_beli);
                        $('#modal-form [name=stok]').val(response.stok);
                        $('#modal-form [name=id_satuan]').val(response.id_satuan);
                    })
                    .fail((errors) => {
                        console.log(errors);
                        alert('Tidak dapat menampilkan data');
                        return;
                    })
            }

            function deleteData(url) {
                if (confirm('Yakin ingin menghapus data?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            console.log(response);
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            alert('Tidak dapat menghapus data');
                            return;
                        })
                }
            }

            function deleteSelected(url) {
                // Ambil semua checkbox yang dipilih
                let selectedIds = [];
                $('.table tbody input:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    alert('Tidak ada data yang dipilih!');
                    return;
                }

                if (confirm('Yakin ingin menghapus data terpilih?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete',
                            'id_bahan': selectedIds
                        })
                        .done((response) => {
                            console.log(response);
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            console.log(errors);
                            alert('Tidak dapat menghapus data');
                        });
                }
            }
        </script>
    @endpush