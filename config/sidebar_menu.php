<?php

return [
    'Master/Inventaris' => [
        'module' => 'inventaris',
        'route' => 'admin.inventaris.index',
        'icon' => 'boxes',
        'items' => [
            ['name' => 'Outlet', 'route' => 'admin.inventaris.outlet.index', 'permissions' => ['inventaris.outlet.view']],
            ['name' => 'Kategori Umum', 'route' => 'admin.inventaris.kategori.index', 'permissions' => ['inventaris.kategori.view']],
            ['name' => 'Satuan', 'route' => 'admin.inventaris.satuan.index', 'permissions' => ['inventaris.satuan.view']],
            ['name' => 'Produk', 'route' => 'admin.inventaris.produk.index', 'permissions' => ['inventaris.produk.view']],
            ['name' => 'Bahan', 'route' => 'admin.inventaris.bahan.index', 'permissions' => ['inventaris.bahan.view']],
            ['name' => 'Sparepart', 'route' => 'admin.inventaris.sparepart.index', 'permissions' => ['inventaris.sparepart.view']],
            ['name' => 'Inventori', 'route' => 'admin.inventaris.inventori.index', 'permissions' => ['inventaris.inventori.view']],
            ['name' => 'Transfer Gudang', 'route' => 'admin.inventaris.transfer-gudang.index', 'permissions' => ['inventaris.transfer-gudang.view']],
        ]
    ],

    'Pelanggan (CRM)' => [
        'module' => 'crm',
        'route' => 'admin.crm.index',
        'icon' => 'users',
        'items' => [
            ['name' => 'Tipe & Diskon Customer', 'route' => 'admin.crm.tipe.index', 'permissions' => ['crm.tipe.view']],
            ['name' => 'Manajemen Pelanggan', 'route' => 'admin.crm.pelanggan.index', 'permissions' => ['crm.pelanggan.view']],
            ['name' => 'Manajemen Prospek & Lead', 'route' => '#', 'permissions' => ['crm.leads.view']],
        ]
    ],

    'Keuangan (F&A)' => [
        'module' => 'finance',
        'route' => 'finance.dashboard.index',
        'icon' => 'wallet',
        'items' => [
            ['name' => 'Dashboard Finance', 'route' => 'finance.dashboard.index', 'permissions' => ['finance.dashboard.view']],
            ['name' => 'Daftar Akun', 'route' => 'finance.akun.index', 'permissions' => ['finance.akun.view']],
            ['name' => 'Manajemen RAB', 'route' => 'admin.finance.rab.index', 'permissions' => ['finance.rab.view']],
            ['name' => 'Biaya', 'route' => 'finance.biaya.index', 'permissions' => ['finance.biaya.view']],
            ['name' => 'Hutang', 'route' => 'finance.hutang.index', 'permissions' => ['finance.hutang.view']],
            ['name' => 'Piutang', 'route' => 'finance.piutang.index', 'permissions' => ['finance.piutang.view']],
            ['name' => 'Rekonsiliasi Bank', 'route' => 'finance.rekonsiliasi.index', 'permissions' => ['finance.rekonsiliasi.view']],
            ['name' => 'Buku Akuntansi', 'route' => 'finance.buku.index', 'permissions' => ['finance.buku.view']],
            ['name' => 'Saldo Awal', 'route' => 'finance.saldo-awal.index', 'permissions' => ['finance.buku.view']],
            ['name' => 'Jurnal', 'route' => 'finance.jurnal.index', 'permissions' => ['finance.jurnal.view']],
            ['name' => 'Aktiva Tetap', 'route' => 'finance.aktiva.index', 'permissions' => ['finance.aktiva.view']],
            ['name' => 'Buku Besar', 'route' => 'finance.buku-besar.index', 'permissions' => ['finance.buku-besar.view']],
            ['name' => 'Neraca', 'route' => 'finance.neraca.index', 'permissions' => ['finance.neraca.view']],
            ['name' => 'Neraca Saldo', 'route' => 'finance.neraca-saldo.index', 'permissions' => ['finance.neraca-saldo.view']],
            ['name' => 'Laporan Laba Rugi', 'route' => 'finance.laba-rugi.index', 'permissions' => ['finance.laba-rugi.view']],
            ['name' => 'Arus Kas', 'route' => 'finance.arus-kas.index', 'permissions' => ['finance.arus-kas.view']],
        ]
    ],

    'Penjualan (S&M)' => [
        'module' => 'sales',
        'route' => 'admin.penjualan.dashboard.index',
        'icon' => 'receipt-text',
        'items' => [
            ['name' => 'Point of Sales', 'route' => 'admin.penjualan.pos.index', 'permissions' => ['sales.pos.view']],
            ['name' => 'Invoice Penjualan', 'route' => 'admin.penjualan.invoice.index', 'permissions' => ['sales.invoice.view']],
            ['name' => 'Laporan Penjualan', 'route' => 'admin.penjualan.laporan.index', 'permissions' => ['sales.laporan.view']],
            ['name' => 'Laporan Margin', 'route' => 'admin.penjualan.margin.index', 'permissions' => ['sales.margin.view']],
            ['name' => 'Agen & Gerobak', 'route' => 'admin.penjualan.agen_gerobak.index', 'permissions' => ['sales.agen.view']],
            ['name' => 'Halaman Agen', 'route' => 'admin.penjualan.agen.index', 'permissions' => ['sales.agen.view']],
        ]
    ],

    'Pembelian (PM)' => [
        'module' => 'procurement',
        'route' => 'pembelian.purchase-order.index',
        'icon' => 'truck',
        'items' => [
            ['name' => 'Purchase Order', 'route' => 'pembelian.purchase-order.index', 'permissions' => ['procurement.purchase-order.view']],
        ]
    ],

    'Produksi (MRP)' => [
        'module' => 'production',
        'route' => 'admin.produksi.produksi.index',
        'icon' => 'factory',
        'items' => [
            ['name' => 'Data Produksi', 'route' => 'admin.produksi.produksi.index', 'permissions' => ['production.produksi.view']],
        ]
    ],

    'Rantai Pasok (SCM)' => [
        'module' => 'inventaris',
        'route' => 'admin.rantai-pasok',
        'icon' => 'git-branch',
        'items' => [
            ['name' => 'Transfer Gudang', 'route' => 'admin.inventaris.transfer-gudang.index', 'permissions' => ['inventaris.transfer-gudang.view']],
        ]
    ],

    'SDM' => [
        'module' => 'hrm',
        'route' => 'admin.sdm',
        'icon' => 'id-card',
        'hasPermission' => true,
        'items' => [
            ['name' => 'Kepegawaian & Rekrutmen', 'route' => 'sdm.kepegawaian.index', 'permissions' => ['hrm.karyawan.view']],
            ['name' => 'Penggajian / Payroll', 'route' => 'sdm.payroll.index', 'permissions' => ['hrm.payroll.view']],
            ['name' => 'Manajemen Absensi', 'route' => 'sdm.attendance.index', 'permissions' => ['hrm.absensi.view']],
            ['name' => 'Manajemen Kinerja', 'route' => 'sdm.kinerja.index', 'permissions' => ['hrm.kinerja.view']],
            ['name' => 'Kontrak & Dokumen HR', 'route' => 'sdm.kontrak.index', 'permissions' => ['hrm.kontrak.view']],
            ['name' => 'Pelatihan & Pengembangan', 'route' => '#', 'permissions' => ['hrm.karyawan.view']],
        ]
    ],

    'Service Management' => [
        'module' => 'service',
        'route' => 'admin.service',
        'icon' => 'wrench',
        'hasPermission' => true,
        'items' => [
            ['name' => 'Invoice Service', 'route' => 'admin.service.invoice.index', 'permissions' => ['service.invoice.view']],
            ['name' => 'History Service', 'route' => 'admin.service.history.index', 'permissions' => ['service.history.view']],
            ['name' => 'Ongkir Service', 'route' => 'admin.service.ongkir.index', 'permissions' => ['service.ongkir.view']],
            ['name' => 'Mesin Customer', 'route' => 'admin.service.mesin.index', 'permissions' => ['service.mesin.view']],
        ]
    ],

    'Investor' => [
        'module' => 'investor',
        'route' => 'admin.investor',
        'icon' => 'hand-coins',
        'items' => [
            ['name' => 'Profil Investor', 'route' => 'admin.investor.profil.index', 'permissions' => ['investor.profil.view']],
            ['name' => 'Bagi Hasil', 'route' => '#', 'permissions' => ['investor.bagi-hasil.view']],
            ['name' => 'List Pencairan', 'route' => '#', 'permissions' => ['investor.pencairan.view']],
        ]
    ],

    'Sistem' => [
        'module' => 'sistem',
        'route' => 'admin.sistem',
        'icon' => 'settings',
        'items' => [
            ['name' => 'User Management', 'route' => 'admin.users.index', 'permissions' => ['sistem.users.view']],
            ['name' => 'Role & Permission', 'route' => 'admin.roles.index', 'permissions' => ['sistem.roles.view']],
            ['name' => 'Pengaturan', 'route' => '#', 'permissions' => ['sistem.settings.view']],
        ]
    ],
];
