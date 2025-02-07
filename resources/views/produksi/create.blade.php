<div class="modal fade" id="modal-produk" tabindex="-1" aria-labelledby="modal-produk" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Modal diperlebar -->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h1 class="modal-title">Tambah Produksi</h1>
            </div>
            <div class="modal-body">
                <form id="form-produksi" method="POST" action="{{ route('produksi.store') }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="id_produk">Pilih Produk</label>
                        <select name="id_produk" id="id_produk" class="form-control form-control-sm">
                            <option value="">Pilih Produk</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id_produk }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="jumlah">Jumlah Produksi</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Bahan</label>
                        <div class="row font-weight-bold mb-1 text-center">
                            <div class="col-md-4 p-0">Nama Bahan</div>
                            <div class="col-md-4 p-0">Pilih Harga</div>
                            <div class="col-md-4 p-0">Jumlah</div>
                        </div>
                        @foreach($bahans as $bahan)
                            <div class="row align-items-center mb-1">
                                <div class="col-md-4 p-1">
                                    <div class="form-check d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input me-1" name="bahan[{{ $bahan->id_bahan }}][checked]" id="bahan_{{ $bahan->id_bahan }}" value="1">
                                        <label class="form-check-label" for="bahan_{{ $bahan->id_bahan }}">{{ $bahan->nama_bahan }}</label>
                                    </div>
                                </div>
                                <div class="col-md-4 p-1">
                                    <select name="bahan[{{ $bahan->id_bahan }}][harga_id]" class="form-control form-control-sm select-harga" data-bahan="{{ $bahan->id_bahan }}">
                                        <option value="">Pilih Harga</option>
                                        @foreach($bahan->hargaBahan as $detail)
                                            <option value="{{ $detail->id }}" data-harga="{{ $detail->harga_beli }}" data-stok="{{ $detail->stok }}">
                                                {{ tanggal_indonesia($detail->created_at, false) }} - Rp{{ number_format($detail->harga_beli, 0, ',', '.') }} - Stok: {{ $detail->stok }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 p-1">
                                    <input type="number" name="bahan[{{ $bahan->id_bahan }}][jumlah]" class="form-control form-control-sm text-center" placeholder="Jumlah" min="1">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Simpan Produksi</button>
                </form>
            </div>
        </div>
    </div>
</div>
