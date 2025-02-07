@extends('app')

@section('title')
Tipe Customer
@endsection

@section('breadcrumb')
@parent
<li class="active">Tipe Custoer</li>
@endsection

@section('content')
<!-- Main row -->
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('tipe.store') }}')"
                    class="btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tipe</th>
                        <th>Produk dan Diskon</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                    <!-- <tbody>
                        @foreach ($tipe as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_tipe }}</td>
                                <td>
                                    <ul>
                                        @foreach ($item->produkTipe as $produkTipe)
                                            <li>{{ $produkTipe->produk->nama_produk }} - {{ $produkTipe->diskon }}%</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="editForm('{{ route('tipe.update', $item->id_tipe) }}')" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                                        <button onclick="deleteData('{{ route('tipe.destroy', $item->id_tipe) }}')" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody> -->
                </table>
            </div>

        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

@includeif('tipe.form')
    @endsection

    @push('scripts')
        <script>
            let table;
            let produkCount = 0;
            $(function () {
                table = $('.table').DataTable({
                    processing: true,
                    autoWidth: false,
                    ajax: '{{ route('tipe.data') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'nama_tipe'
                        },
                        {
                            data: 'produk_diskon',
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            searchable: false,
                            sortable: false
                        },
                    ]

                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('#modal-form form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        alert('Silakan periksa kembali input Anda. Pastikan semua field yang diperlukan telah diisi dengan benar.');
                    } else {
                        e.preventDefault(); // Mencegah pengiriman form default
                        $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                            .done((response) => {
                                $('#modal-form').modal('hide'); // Menutup modal
                                table.ajax.reload(); // Reload tabel
                                alert('Data berhasil disimpan!');
                                console.log(response); // Tampilkan pesan sukses
                            })
                            .fail((errors) => {
                                alert('Tidak dapat menyimpan data: ' + errors.responseText); // Tampilkan pesan kesalahan
                                return;
                            });
                    }
                });

                document.getElementById('add-produk').addEventListener('click', function() {
                    addProdukRow();
                });
            });

            function addForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Tambah Tipe');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=nama_tipe]').focus();
            }

            function editForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Edit Tipe');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('put');
                $('#modal-form [name=nama_tipe]').focus();

                $.get(url)
                    .done((response) => {
                        $('#modal-form [name=nama_tipe]').val(response.nama_tipe);

                        // Clear existing produk rows
                        document.getElementById('produk-container').innerHTML = '';
                        produkCount = 0; // Reset produk count
                        console.log(response);

                        if (Array.isArray(response.produk_tipe)) {
                            response.produk_tipe.forEach(item => {
                                addProdukRow(null, item.id_produk, item.diskon);
                            });
                        } else {
                            console.error('produkTipe is not an array or is undefined');
                        }
                        
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
                        console.log(errors);
                        return;
                    })
            }

            function removeProduk(id) {
                const produkRow = document.querySelector(`div[data-id='${id}']`);
                if (produkRow) {
                    produkRow.remove(); // Hapus elemen produk
                }
            }

            function addProdukRow(id = null, produkId = '', diskon = '') {
                const container = document.getElementById('produk-container');
                const newRow = document.createElement('div');
                newRow.classList.add('form-group', 'row', 'align-items-center'); // Pastikan semua elemen sejajar
                newRow.setAttribute('data-id', produkCount);

                let options = '';
                @foreach ($produk as $item)
                    options += `<option value="{{ $item->id_produk }}" ${produkId == '{{ $item->id_produk }}' ? 'selected' : ''}>
                                    {{ $item->nama_produk }}
                                </option>`;
                @endforeach

                newRow.innerHTML = `
                    <div class="col-md-5 d-flex align-items-center">
                        <label for="produk" class="col-md-3 control-label" style="white-space: nowrap;">Produk</label>
                        <select name="produk[]" class="form-control col-md-9" required>
                            <option value="">Pilih Produk</option>
                            ${options}
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="diskon" class="col-md-5 control-label" style="white-space: nowrap;">Diskon (%)</label>
                        <input type="number" name="diskon[]" class="form-control col-md-7" required min="0" max="100" value="${diskon}">
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-danger remove-produk" style="height: 38px;" onclick="removeProduk(${produkCount})">Hapus</button>
                    </div>
                `;
                container.appendChild(newRow);
                produkCount++;
                console.log(produkCount);
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
        </script>
    @endpush