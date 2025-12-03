{{-- Only show if user has access to at least one Inventory submenu --}}
@hasModuleAccess('inventory')
<ul class="sub-menu">
    @hasAnyPermission('sistem.outlets.view', 'inventory.barang.view')
    <li class="{{ request()->routeIs('outlet.index') ? 'active' : '' }}">
        <a href="{{ route('outlet.index') }}">
            <i data-feather="crosshair"></i> <span>Outlet</span>
        </a>
    </li>
    @endhasAnyPermission
    
    @hasPermission('inventory.kategori.view')
    <li class="{{ request()->routeIs('kategori.index') ? 'active' : '' }}">
        <a href="{{ route('kategori.index') }}">
            <i data-feather="grid"></i> <span>Kategori Umum</span>
        </a>
    </li>
    @endhasPermission
    
    @hasAnyPermission('inventory.barang.view', 'inventory.kategori.view')
    <li class="{{ request()->routeIs('satuan.index') ? 'active' : '' }}">
        <a href="{{ route('satuan.index') }}">
            <i data-feather="pocket"></i> <span>Satuan</span>
        </a>
    </li>
    @endhasAnyPermission
    
    @hasPermission('inventory.barang.view')
    <li class="{{ request()->routeIs('produk.index') ? 'active' : '' }}">
        <a href="{{ route('produk.index') }}">
            <i data-feather="package"></i> <span>Produk</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('inventory.barang.view')
    <li class="{{ request()->routeIs('bahan.index') ? 'active' : '' }}">
        <a href="{{ route('bahan.index') }}">
            <i data-feather="layers"></i> <span>Bahan</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('inventory.stok.view')
    <li class="{{ request()->routeIs('inventori.index') ? 'active' : '' }}">
        <a href="{{ route('inventori.index') }}">
            <i data-feather="database"></i> <span>Inventori/Stok</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('inventory.transfer.view')
    <li class="{{ request()->routeIs('manajemen-gudang.index') ? 'active' : '' }}">
        <a href="{{ route('manajemen-gudang.index') }}">
            <i data-feather="send"></i> <span>Transfer Gudang</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('inventory.opname.view')
    <li class="{{ request()->routeIs('opname.index') ? 'active' : '' }}">
        <a href="{{ route('opname.index') }}">
            <i data-feather="clipboard"></i> <span>Stock Opname</span>
        </a>
    </li>
    @endhasPermission
    
    <li class="unavailable">
        <a href="#">
            <i data-feather="bar-chart-2"></i> <span>Analisis Inventaris</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
@endhasModuleAccess
