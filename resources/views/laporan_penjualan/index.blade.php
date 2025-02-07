@extends('app')

@section('content')
<div class="container">
    <h2 class="mb-4">Laporan Penjualan</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Produk</th>
                <th>HPP</th>
                <th>Harga Jual</th>
                <th>Jumlah</th>
                <th>Profit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->nama_produk }}</td>
                <td>Rp{{ number_format($item->hpp, 0, ',', '.') }}</td>
                <td>Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>Rp{{ number_format(($item->harga_jual - $item->hpp) * $item->jumlah, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('laporan_penjualan.detail', $item->id_laporan) }}" class="btn btn-info">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
