@extends('app')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $kategori }}</h3>

                <p>Total Kategori</p>
            </div>
            <div class="icon">
                <i class="fa fa-cube"></i>
            </div>
            <a href="{{ route('kategori.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $produk }}</h3>

                <p>Total Produk</p>
            </div>
            <div class="icon">
                <i class="fa fa-cubes"></i>
            </div>
            <a href="{{ route('produk.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $member }}</h3>

                <p>Total Customer</p>
            </div>
            <div class="icon">
                <i class="fa fa-id-card"></i>
            </div>
            <a href="{{ route('member.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3>{{ $supplier }}</h3>

                <p>Total Supplier</p>
            </div>
            <div class="icon">
                <i class="fa fa-truck"></i>
            </div>
            <a href="{{ route('supplier.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->
<!-- Main row -->
<div class="row">
    <div class="col-lg-3 col-xs-12">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3>Rp {{ number_format($total_omset, 0, ',', '.') }}</h3>
                <p>Total Omset Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-12">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>Rp {{ number_format($total_profit, 0, ',', '.') }}</h3>
                <p>Total Profit Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fa fa-line-chart"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-12">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3>Rp {{ number_format($total_omset, 0, ',', '.') }}</h3>
                <p>Total BON Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-12">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>Rp {{ number_format($total_profit, 0, ',', '.') }}</h3>
                <p>Total CASH Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fa fa-line-chart"></i>
            </div>
        </div>
    </div>
    <!-- /.col -->
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Grafik Rangkuman {{ tanggal_indonesia($tanggal_awal, false) }} s/d {{ tanggal_indonesia($tanggal_akhir, false) }}</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="chart">
                            <!-- Sales Chart Canvas -->
                            <canvas id="salesChart" style="height: 180px;"></canvas>
                        </div>
                        <!-- /.chart-responsive -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row (main row) -->
@endsection

@push('scripts')
<!-- ChartJS -->
<script src="{{ asset('AdminLTE-2/bower_components/chart.js/Chart.js') }}"></script>
<script>
$(function() {
    // Get context with jQuery - using jQuery's .get() method.
    var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
    // This will get the first returned node in the jQuery collection.
    var salesChart = new Chart(salesChartCanvas);

    var salesChartData = {
        labels: {{ json_encode($data_tanggal) }},
        datasets: [
            {
                label: 'Omset Penjualan',
                fillColor           : 'rgba(60,141,188,0.9)',
                strokeColor         : 'rgba(60,141,188,0.8)',
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: {{ json_encode($data_penjualan) }}
            },
            {
                label: 'Margin Profit',
                fillColor           : 'rgba(210, 214, 222, 0.9)',
                strokeColor         : 'rgba(210, 214, 222, 0.8)',
                pointColor          : '#c1c7d1',
                pointStrokeColor    : 'rgba(210, 214, 222, 1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(210, 214, 222, 1)',
                data: {{ json_encode($data_profit) }}
            },
            {
                label: 'Pembelian Bahan',
                fillColor           : 'rgba(0, 166, 90, 0.9)',
                strokeColor         : 'rgba(0, 166, 90, 0.8)',
                pointColor          : '#00a65a',
                pointStrokeColor    : 'rgba(0, 166, 90, 1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(0, 166, 90, 1)',
                data: {{ json_encode($data_pembelian) }}
            },
            {
                label: 'Pengeluaran Umum',
                fillColor           : 'rgba(245, 105, 84, 0.9)',
                strokeColor         : 'rgba(245, 105, 84, 0.8)',
                pointColor          : '#f56954',
                pointStrokeColor    : 'rgba(245, 105, 84, 1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(245, 105, 84, 1)',
                data: {{ json_encode($data_pengeluaran) }}
            }
        ]
    };

    var salesChartOptions = {
        responsive              : true,
        showScale               : true,
        scaleShowGridLines      : false,
        scaleGridLineColor      : 'rgba(0,0,0,.05)',
        scaleGridLineWidth      : 1,
        scaleShowHorizontalLines: true,
        scaleShowLabel          : true,
        scaleLineColor          : 'rgba(0,0,0,.2)',
        scaleLineWidth          : 1,
        scaleShowVerticalLines  : true,
        barShowStroke           : true,
        barStrokeWidth          : 2,
        barValueSpacing         : 5,
        barDatasetSpacing       : 1,
        scales: {
            y: {
                ticks: {
                    callback: function(value, index, values) {
                        return 'Rp ' + value.toLocaleString('id-ID'); // Format Rupiah
                    }
                }
            }
        }
    };

    salesChart.Bar(salesChartData, salesChartOptions);
});
</script>
@endpush