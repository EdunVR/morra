<style>
    /* Sidebar styling */
    .main-sidebar {
        background: #2c3e50;
        color: #ecf0f1;
        width: 230px;
        position: fixed;
        height: 100vh;
        transition: all 0.3s;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        overflow-y: auto;
    }

    .scroll-indicator {
        position: absolute;
        left: 0;
        right: 0;
        height: 20px;
        background: linear-gradient(to bottom, rgba(44, 62, 80, 0) 0%, rgba(44, 62, 80, 1) 100%);
        z-index: 1;
    }

    .scroll-indicator-bottom {
        bottom: 0;
        transform: rotate(180deg); /* Flip the gradient for the bottom */
    }

    /* User panel */
    .user-panel {
        background: #34495e;
        padding: 20px 10px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    .user-panel img {
        width: 70px;
        height: 70px;
        border: 2px solid #ecf0f1;
        transition: transform 0.3s ease-in-out;
    }
    .user-panel img:hover {
        transform: rotate(360deg) scale(1.7);
    }

    /* Sidebar menu */
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu li {
        position: relative;
    }

    .sidebar-menu li a {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #ecf0f1;
        padding: 7px 20px;
        font-size: 14px;
        transition: all 0.3s ease-in-out;
        border-left: 4px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .sidebar-menu li a::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background: #1abc9c;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-menu li a:hover::before {
        transform: scaleX(1);
    }

    .sidebar-menu li a:hover, 
    .sidebar-menu li a:focus {
        background: rgba(255, 255, 255, 0.1);
        border-left: 4px solid #1abc9c;
        color: #1abc9c;
        transform: translateX(10px) scale(1.05);
        box-shadow: 3px 3px 10px rgba(26, 188, 156, 0.5);
    }

    .sidebar-menu li a i {
        margin-right: 10px;
        font-size: 16px;
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-menu li a:hover i {
        transform: rotate(360deg) scale(1.3);
    }

    /* Active menu item */
    .sidebar-menu li.active a {
        background: #1abc9c;
        color: #fff;
        border-left: 4px solid #16a085;
        transform: scale(1.1);
        box-shadow: 5px 5px 15px rgba(26, 188, 156, 0.6);
    }

    /* Section headers */
    .sidebar-menu .header {
        padding: 10px 20px;
        font-size: 12px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.5);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Click animation */
    .sidebar-menu li a:active {
        transform: scale(0.9) rotate(-2deg);
    }
</style>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
    <div class="scroll-indicator"></div>
        <div class="user-panel text-center" style="padding: 20px 10px; overflow: hidden; min-height: 100px;">
            <div class="image">
                <img src="{{ url($setting->path_logo) ?? Auth::user()->foto }}"
                    class="" alt="User Image" 
                    style="display: block; margin: auto;">
                    <!-- class="img-circle" alt="User Image" -->
            </div>
            <div class="info">
                <p style="margin: 10px 0 5px; font-size: 16px; font-weight: bold;">{{ Auth::user()->name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            @if(in_array('Dashboard', Auth::user()->akses))
            <li>
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            @endif
            <li class="header">MASTER</li>
            @if(in_array('Kategori', Auth::user()->akses))
                <li>
                    <a href="{{ route('kategori.index') }}">
                        <i class="fa fa-cube"></i> <span>Kategori Produk</span>
                    </a>
                </li>
            @endif
            @if(in_array('Tipe', Auth::user()->akses))
                <li>
                    <a href="{{ route('tipe.index') }}">
                        <i class="fa fa-cube"></i> <span>Tipe Customer</span>
                    </a>
                </li>
            @endif
            @if(in_array('Satuan', Auth::user()->akses))
                <li>
                    <a href="{{ route('satuan.index') }}">
                        <i class="fa fa-cube"></i> <span>Satuan</span>
                    </a>
                </li>
            @endif
            @if(in_array('Produk', Auth::user()->akses))
                <li>
                    <a href="{{ route('produk.index') }}">
                        <i class="fa fa-database"></i> <span>Produk</span>
                    </a>
                </li>
            @endif
            @if(in_array('Bahan', Auth::user()->akses))
                <li>
                    <a href="{{ route('bahan.index') }}">
                        <i class="fa fa-dropbox"></i> <span>Bahan</span>
                    </a>
                </li>
            @endif
            @if(in_array('Customer', Auth::user()->akses))
                <li>
                    <a href="{{ route('member.index') }}">
                        <i class="fa fa-id-card"></i> <span>Customer</span>
                    </a>
                </li>
            @endif
            @if(in_array('Supplier', Auth::user()->akses))
                <li>
                    <a href="{{ route('supplier.index') }}">
                        <i class="fa fa-truck"></i> <span>Supplier</span>
                    </a>
                </li>
            @endif
            <li class="header">PRODUKSI</li>
            @if(in_array('Produksi', Auth::user()->akses))
                <li>
                    <a href="{{ route('produksi.index') }}">
                        <i class="fa fa-spinner"></i> <span>Produksi</span>
                    </a>
                </li>
            @endif
            <li class="header">TRANSAKSI</li>
            @if(in_array('Pengeluaran', Auth::user()->akses))
                <li>
                    <a href="{{ route('pengeluaran.index') }}">
                        <i class="fa fa-money"></i> <span>Pengeluaran</span>
                    </a>
                </li>
            @endif
            @if(in_array('Pembelian', Auth::user()->akses))
                <li>
                    <a href="{{ route('pembelian.index') }}">
                        <i class="fa fa-credit-card-alt"></i> <span>Pembelian</span>
                    </a>
                </li>
            @endif
            @if(in_array('Penjualan', Auth::user()->akses))
                <li>
                    <a href="{{ route('penjualan.index') }}">
                        <i class="fa fa-shopping-cart"></i> <span>Penjualan</span>
                    </a>
                </li>
            @endif
            @if(in_array('Hutang', Auth::user()->akses))
                <li>
                    <a href="{{ route('hutang.index') }}">
                        <i class="fa fa-balance-scale"></i> <span>Hutang ke Supplier</span>
                    </a>
                </li>
            @endif
            @if(in_array('Piutang', Auth::user()->akses))
                <li>
                    <a href="{{ route('piutang.index') }}">
                        <i class="fa fa-balance-scale"></i> <span>Piutang dari Customer</span>
                    </a>
                </li>
            @endif
            @if(in_array('Transaksi', Auth::user()->akses))
            <li>
                <a href="{{ route('transaksi.baru') }}">
                    <i class="fa fa-exchange"></i> <span>Transaksi Baru</span>
                </a>
            </li>
            @endif
            @if(in_array('Transaksi Aktif', Auth::user()->akses))
            <li>
                <a href="{{ route('transaksi.index') }}">
                    <i class="fa fa-exchange"></i> <span>Transaksi Aktif</span>
                </a>
            </li>
            @endif
            <li class="header">REPORT</li>
            @if(in_array('Laporan', Auth::user()->akses))
                <li>
                    <a href="{{ route('laporan.index') }}">
                        <i class="fa fa-file-pdf-o"></i> <span>Laporan Umum</span>
                    </a>
                </li>
            @endif
            @if(in_array('Laporan Penjualan', Auth::user()->akses))
                <li>
                    <a href="{{ route('laporan_penjualan.index') }}">
                        <i class="fa fa-file-pdf-o"></i> <span>Laporan Penjualan</span>
                    </a>
                </li>
            @endif
            <li class="header">SYSTEM</li>
            @if(in_array('User', Auth::user()->akses))
                <li>
                    <a href="{{ route('user.index') }}">
                        <i class="fa fa-user-circle"></i> <span>User</span>
                    </a>
                </li>
            @endif
            @if(in_array('Pengaturan', Auth::user()->akses))
                <li>
                    <a href="{{ route('setting.index') }}">
                        <i class="fa fa-cogs"></i> <span>Pengaturan</span>
                    </a>
                </li>
            @endif

        </ul>
        <div class="scroll-indicator scroll-indicator-bottom"></div>
    </section>
    <!-- /.sidebar -->
</aside>