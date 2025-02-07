@extends('app')

@section('title')
Piutang
@endsection

@section('breadcrumb')
@parent
<li class="active">Piutang</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body table-responsive">
                <table class="table table-striped table-bordered table-piutang">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Nama Customer</th>
                        <th>Jumlah Piutang</th>
                        <th>Status</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeif('piutang.detail')
@endsection

@push('scripts')
<script>
    let table;
    $(function () {
        table = $('.table-piutang').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('piutang.data') }}',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'tanggal'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'piutang'
                },
                {
                    data: 'status'
                },
                {
                    data: 'aksi',
                    searchable: false,
                    sortable: false
                },
            ]
        });
    });

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
                    return;
                })
        }
    }
</script>
@endpush