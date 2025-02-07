<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-tipe" action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal-form-label">Form Tipe</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="nama_tipe" class="col-lg-3 col-lg-offset-1 control-label">Nama Tipe</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_tipe" id="nama_tipe" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div id="produk-container">
                        <!-- Produk dan Diskon akan ditambahkan di sini -->
                    </div>
                    <button type="button" id="add-produk" class="btn btn-primary">Tambah Produk</button>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>