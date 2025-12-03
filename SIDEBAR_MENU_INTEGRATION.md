# 📋 Sidebar Menu Integration

## Tambahkan Menu User Management ke Sidebar

Cari file sidebar layout Anda (biasanya di `resources/views/layouts/` atau `resources/views/partials/`), kemudian tambahkan kode berikut:

### Option 1: Simple Menu

```blade
<!-- User Management Section -->
@hasPermission('users.view')
<li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>User Management</span>
    </a>
</li>
@endhasPermission

@hasPermission('roles.view')
<li class="nav-item">
    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
        <i class="fas fa-user-shield"></i>
        <span>Role Management</span>
    </a>
</li>
@endhasPermission
```

### Option 2: Dropdown Menu (Recommended)

```blade
<!-- System Management Dropdown -->
@hasAnyRole('Super Admin', 'Admin')
<li class="nav-item has-treeview {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cog"></i>
        <p>
            System Management
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        @hasPermission('users.view')
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>User Management</p>
            </a>
        </li>
        @endhasPermission

        @hasPermission('roles.view')
        <li class="nav-item">
            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Role Management</p>
            </a>
        </li>
        @endhasPermission
    </ul>
</li>
@endhasAnyRole
```

### Option 3: With Badge (Show User Count)

```blade
@hasPermission('users.view')
<li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>User Management</span>
        <span class="badge badge-info right">{{ \App\Models\User::count() }}</span>
    </a>
</li>
@endhasPermission
```

---

## Contoh Lengkap Sidebar dengan User Management

```blade
<!-- resources/views/layouts/partials/sidebar.blade.php -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="brand-image">
        <span class="brand-text font-weight-light">MORRA System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Inventaris -->
                @hasPermission('inventory.view')
                <li class="nav-item">
                    <a href="{{ route('admin.inventaris') }}" class="nav-link">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Inventaris</p>
                    </a>
                </li>
                @endhasPermission

                <!-- Finance -->
                @hasPermission('finance.view')
                <li class="nav-item">
                    <a href="{{ route('admin.keuangan') }}" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Keuangan</p>
                    </a>
                </li>
                @endhasPermission

                <!-- Sales -->
                @hasPermission('sales.view')
                <li class="nav-item">
                    <a href="{{ route('admin.penjualan.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Penjualan</p>
                    </a>
                </li>
                @endhasPermission

                <!-- Divider -->
                <li class="nav-header">SYSTEM</li>

                <!-- User Management -->
                @hasAnyRole('Super Admin', 'Admin')
                <li class="nav-item has-treeview {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            User Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @hasPermission('users.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        @endhasPermission

                        @hasPermission('roles.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles & Permissions</p>
                            </a>
                        </li>
                        @endhasPermission
                    </ul>
                </li>
                @endhasAnyRole

                <!-- Logout -->
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>
```

---

## Update Navbar (Show User Info)

Tambahkan di navbar untuk menampilkan info user yang login:

```blade
<!-- resources/views/layouts/partials/navbar.blade.php -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
                <span class="ml-2">{{ auth()->user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-item">
                    <strong>{{ auth()->user()->name }}</strong><br>
                    <small class="text-muted">{{ auth()->user()->email }}</small><br>
                    @foreach(auth()->user()->roles as $role)
                        <span class="badge badge-primary">{{ $role->name }}</span>
                    @endforeach
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
```

---

## CSS untuk Active State (Optional)

Tambahkan di file CSS Anda:

```css
/* Active menu styling */
.nav-sidebar .nav-link.active {
    background-color: #007bff;
    color: #fff;
}

.nav-sidebar .nav-link.active i {
    color: #fff;
}

/* Hover effect */
.nav-sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

/* Badge styling */
.nav-link .badge {
    margin-left: auto;
}
```

---

## JavaScript untuk Dropdown (Optional)

Jika menggunakan dropdown menu:

```javascript
// Auto expand active menu
$(document).ready(function () {
    // Expand menu if child is active
    $(".nav-link.active").parents(".has-treeview").addClass("menu-open");
    $(".nav-link.active")
        .parents(".has-treeview")
        .children("a")
        .addClass("active");
});
```

---

## Testing

1. Login sebagai Super Admin
2. Cek apakah menu User Management muncul
3. Klik menu dan pastikan redirect ke halaman yang benar
4. Login sebagai user biasa (tanpa permission)
5. Pastikan menu tidak muncul

---

## Troubleshooting

### Menu tidak muncul

-   Pastikan user sudah login
-   Cek permission user dengan: `auth()->user()->permissions`
-   Clear cache: `php artisan view:clear`

### Active state tidak bekerja

-   Pastikan route name sudah benar
-   Cek dengan: `php artisan route:list | grep users`

### Dropdown tidak expand

-   Pastikan jQuery sudah loaded
-   Cek console browser untuk error JavaScript

---

## 🎉 Done!

Menu User Management sudah terintegrasi dengan sidebar!
