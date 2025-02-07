@extends('app')

@section('title')
Data Produksi
@endsection

@section('breadcrumb')
@parent
<li class="active">Data Produksi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addForm()" class="btn btn-success btn-xs btn-flat"><i
                            class="fa fa-plus-circle"></i> Produksi Baru</button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <form action="" method="post" class="form-produksi">
                    @csrf
                    <table class="table table-stiped table-bordered table-produksi">
                        <thead>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-detail-produksi" tabindex="-1" aria-labelledby="modal-detail-produksi" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detail Produksi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@includeIf('produksi.create')
    @endsection

    @push('scripts')
        <script>
            let table;
            $(function () {
                table = $('.table-produksi').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('produksi.data') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'tanggal'
                        },
                        {
                            data: 'produk'
                        },
                        {
                            data: 'jumlah'
                        },
                        {
                            data: 'aksi',
                            searchable: false,
                            sortable: false
                        }
                    ]
                });
            });

            $(document).on('change', '.select-harga', function() {
                let bahanId = $(this).data('bahan');
                let selectedOption = $(this).find(':selected');
                let harga = selectedOption.data('harga');
                let stok = selectedOption.data('stok');

                $(`#harga_beli_${bahanId}`).val(harga);
                $(`#stok_${bahanId}`).val(stok);

                console.log(bahanId, harga, stok);
            });


            function addForm() {
                $('#modal-produk').modal('show');
            }

            function showDetail(url) {
                console.log(url); // Memeriksa URL
                $('#modal-detail-produksi .modal-body').html('<i class="fa fa-spinner fa-spin"></i>'); // Menampilkan loading spinner
                $.get(url)
                    .done((response) => {
                        console.log(response); // Memeriksa respons
                        $('#modal-detail-produksi .modal-body').html(response); // Mengisi konten modal
                        $('#modal-detail-produksi').modal('show'); // Menampilkan modal setelah konten diisi
                    })
                    .fail((errors) => {
                        console.log(errors); // Memeriksa kesalahan
                        alert('Tidak dapat menampilkan detail data');
                        return;
                    });
            }

            function deleteData(url) {
                if (confirm('Yakin ingin menghapus data?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            alert('Tidak dapat menghapus data');
                        });
                }
            }
        </script>
    @endpush