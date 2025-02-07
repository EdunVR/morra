<div class="modal-body">
    <h5 class="font-weight-bold" style="font-size: 1.5rem; text-align: center;">Produksi: {{ $produksi->produk->nama_produk }}</h5>
    <p class="font-weight-bold" style="font-size: 1.2rem; text-align: center;">Jumlah: {{ $produksi->jumlah }}</p>
    <h6 class="mt-4">Detail Bahan:</h6>

    @php
        $totalHPP = 0;
    @endphp

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Nama Bahan</th>
                <th>Jumlah</th>
                <th>Tanggal - Harga Beli</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produksi->detail as $detail)
                @php
                    $hpp = $detail->harga_beli * $detail->jumlah;
                    $totalHPP += $hpp;
                @endphp
                <tr>
                    <td>{{ $detail->bahan->nama_bahan }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>{{ tanggal_indonesia($detail->tanggal_harga) }} - {{ format_uang($detail->harga_beli) }}</td>
                    <td>{{ format_uang($detail->harga_beli * $detail->jumlah) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right font-weight-bold">HPP</th>
                <th>{{ format_uang($totalHPP) }}</th>
            </tr>
        </tfoot>
    </table>
</div>