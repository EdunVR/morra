<?php

/**
 * Script to generate remaining migrations from existing models
 * Run: php generate_remaining_migrations.php
 */

require __DIR__.'/vendor/autoload.php';

$timestamp = date('Y_m_d_His');

// Define remaining tables structure based on models
$tables = [
    'sales_invoice' => [
        'id_sales_invoice' => 'bigIncrements',
        'no_invoice' => 'string:50|unique',
        'tanggal' => 'datetime',
        'id_member' => 'unsignedBigInteger|nullable',
        'id_prospek' => 'unsignedBigInteger|nullable',
        'id_outlet' => 'unsignedBigInteger',
        'id_customer_price' => 'unsignedBigInteger|nullable',
        'id_user' => 'unsignedBigInteger',
        'id_penjualan' => 'unsignedBigInteger|nullable',
        'total' => 'decimal:15,2|default:0',
        'status' => 'string:50|default:pending',
        'due_date' => 'date|nullable',
        'keterangan' => 'text|nullable',
        'jenis_pembayaran' => 'string:50|nullable',
        'penerima' => 'string|nullable',
        'tanggal_pembayaran' => 'datetime|nullable',
        'catatan_pembayaran' => 'text|nullable',
        'bukti_transfer' => 'string|nullable',
        'nama_bank' => 'string:100|nullable',
        'nama_pengirim' => 'string|nullable',
        'jumlah_transfer' => 'decimal:15,2|nullable',
        'total_diskon' => 'decimal:15,2|default:0',
        'subtotal' => 'decimal:15,2|default:0',
        'id_ongkir' => 'unsignedBigInteger|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_member' => ['member', 'id_member', 'set null'],
            'id_outlet' => ['outlets', 'id_outlet', 'cascade'],
            'id_user' => ['users', 'id', 'cascade'],
            'id_penjualan' => ['penjualan', 'id_penjualan', 'set null']
        ]
    ],
    
    'sales_invoice_item' => [
        'id_sales_invoice_item' => 'bigIncrements',
        'id_sales_invoice' => 'unsignedBigInteger',
        'id_produk' => 'unsignedBigInteger|nullable',
        'deskripsi' => 'string|nullable',
        'keterangan' => 'text|nullable',
        'kuantitas' => 'decimal:10,2|default:0',
        'satuan' => 'string:50|nullable',
        'harga' => 'decimal:15,2|default:0',
        'subtotal' => 'decimal:15,2|default:0',
        'tipe' => 'string:50|nullable',
        'diskon' => 'decimal:15,2|default:0',
        'harga_normal' => 'decimal:15,2|default:0',
        'timestamps' => true,
        'foreign' => [
            'id_sales_invoice' => ['sales_invoice', 'id_sales_invoice', 'cascade'],
            'id_produk' => ['produk', 'id_produk', 'set null']
        ]
    ],
    
    'service_invoices' => [
        'id_service_invoice' => 'bigIncrements',
        'no_invoice' => 'string:50|unique',
        'tanggal' => 'date',
        'tanggal_mulai_service' => 'date|nullable',
        'tanggal_selesai_service' => 'date|nullable',
        'id_member' => 'unsignedBigInteger|nullable',
        'id_mesin_customer' => 'unsignedBigInteger|nullable',
        'jenis_service' => 'string:100|nullable',
        'jumlah_teknisi' => 'integer|default:0',
        'jumlah_jam' => 'decimal:10,2|default:0',
        'biaya_teknisi' => 'decimal:15,2|default:0',
        'is_garansi' => 'boolean|default:0',
        'diskon' => 'decimal:15,2|default:0',
        'total_sebelum_diskon' => 'decimal:15,2|default:0',
        'total' => 'decimal:15,2|default:0',
        'status' => 'string:50|default:pending',
        'due_date' => 'datetime|nullable',
        'catatan' => 'text|nullable',
        'tanggal_service_berikutnya' => 'date|nullable',
        'id_invoice_sebelumnya' => 'unsignedBigInteger|nullable',
        'service_lanjutan_ke' => 'integer|default:0',
        'jenis_pembayaran' => 'string:50|nullable',
        'penerima' => 'string|nullable',
        'tanggal_pembayaran' => 'datetime|nullable',
        'catatan_pembayaran' => 'text|nullable',
        'id_user' => 'unsignedBigInteger|nullable',
        'keterangan_service' => 'text|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_member' => ['member', 'id_member', 'set null'],
            'id_user' => ['users', 'id', 'set null']
        ]
    ],
    
    'service_invoice_item' => [
        'id' => 'bigIncrements',
        'id_service_invoice' => 'unsignedBigInteger',
        'id_produk' => 'unsignedBigInteger|nullable',
        'deskripsi' => 'string|nullable',
        'kuantitas' => 'decimal:10,2|default:0',
        'satuan' => 'string:50|nullable',
        'harga' => 'decimal:15,2|default:0',
        'subtotal' => 'decimal:15,2|default:0',
        'timestamps' => true,
        'foreign' => [
            'id_service_invoice' => ['service_invoices', 'id_service_invoice', 'cascade'],
            'id_produk' => ['produk', 'id_produk', 'set null']
        ]
    ],
    
    'produksi' => [
        'id_produksi' => 'bigIncrements',
        'id_produk' => 'unsignedBigInteger',
        'id_outlet' => 'unsignedBigInteger',
        'tanggal_produksi' => 'date',
        'jumlah_produksi' => 'decimal:10,2',
        'biaya_produksi' => 'decimal:15,2|default:0',
        'keterangan' => 'text|nullable',
        'status' => 'string:50|default:draft',
        'timestamps' => true,
        'foreign' => [
            'id_produk' => ['produk', 'id_produk', 'cascade'],
            'id_outlet' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'produksi_detail' => [
        'id' => 'bigIncrements',
        'id_produksi' => 'unsignedBigInteger',
        'id_bahan' => 'unsignedBigInteger',
        'jumlah' => 'decimal:10,2',
        'harga' => 'decimal:15,2|default:0',
        'subtotal' => 'decimal:15,2|default:0',
        'timestamps' => true,
        'foreign' => [
            'id_produksi' => ['produksi', 'id_produksi', 'cascade'],
            'id_bahan' => ['bahan', 'id_bahan', 'cascade']
        ]
    ],
    
    'log_stok' => [
        'id_log' => 'bigIncrements',
        'id_produk' => 'unsignedBigInteger|nullable',
        'id_bahan' => 'unsignedBigInteger|nullable',
        'id_outlet' => 'unsignedBigInteger',
        'jenis_transaksi' => 'string:50',
        'referensi' => 'string:100|nullable',
        'jumlah' => 'decimal:10,2',
        'stok_sebelum' => 'decimal:10,2',
        'stok_sesudah' => 'decimal:10,2',
        'keterangan' => 'text|nullable',
        'id_user' => 'unsignedBigInteger|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_produk' => ['produk', 'id_produk', 'set null'],
            'id_bahan' => ['bahan', 'id_bahan', 'set null'],
            'id_outlet' => ['outlets', 'id_outlet', 'cascade'],
            'id_user' => ['users', 'id', 'set null']
        ]
    ],
    
    'customer_price' => [
        'id' => 'bigIncrements',
        'id_member' => 'unsignedBigInteger',
        'id_produk' => 'unsignedBigInteger',
        'id_outlet' => 'unsignedBigInteger|nullable',
        'harga_khusus' => 'decimal:15,2',
        'tanggal_mulai' => 'date|nullable',
        'tanggal_selesai' => 'date|nullable',
        'is_active' => 'boolean|default:1',
        'timestamps' => true,
        'foreign' => [
            'id_member' => ['member', 'id_member', 'cascade'],
            'id_produk' => ['produk', 'id_produk', 'cascade'],
            'id_outlet' => ['outlets', 'id_outlet', 'set null']
        ]
    ],
    
    'ongkos_kirim' => [
        'id_ongkir' => 'bigIncrements',
        'id_outlet' => 'unsignedBigInteger|nullable',
        'nama_tujuan' => 'string',
        'biaya' => 'decimal:15,2',
        'keterangan' => 'text|nullable',
        'is_active' => 'boolean|default:1',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'set null']
        ]
    ],
    
    'prospek' => [
        'id_prospek' => 'bigIncrements',
        'nama' => 'string',
        'telepon' => 'string:20|nullable',
        'email' => 'string|nullable',
        'alamat' => 'text|nullable',
        'perusahaan' => 'string|nullable',
        'jabatan' => 'string:100|nullable',
        'sumber' => 'string:100|nullable',
        'status' => 'string:50|default:new',
        'nilai_estimasi' => 'decimal:15,2|nullable',
        'tanggal_follow_up' => 'date|nullable',
        'catatan' => 'text|nullable',
        'id_user' => 'unsignedBigInteger|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_user' => ['users', 'id', 'set null']
        ]
    ],
    
    'prospek_timeline' => [
        'id' => 'bigIncrements',
        'id_prospek' => 'unsignedBigInteger',
        'tanggal' => 'datetime',
        'aktivitas' => 'string',
        'keterangan' => 'text|nullable',
        'id_user' => 'unsignedBigInteger|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_prospek' => ['prospek', 'id_prospek', 'cascade'],
            'id_user' => ['users', 'id', 'set null']
        ]
    ],
    
    'mesin_customer' => [
        'id' => 'bigIncrements',
        'id_member' => 'unsignedBigInteger',
        'kode_mesin' => 'string:50|unique',
        'nama_mesin' => 'string',
        'merk' => 'string:100|nullable',
        'tipe' => 'string:100|nullable',
        'no_seri' => 'string:100|nullable',
        'tahun_pembuatan' => 'integer|nullable',
        'tanggal_beli' => 'date|nullable',
        'lokasi' => 'string|nullable',
        'status' => 'string:50|default:aktif',
        'catatan' => 'text|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_member' => ['member', 'id_member', 'cascade']
        ]
    ],
    
    'setting_coa_sales' => [
        'id' => 'bigIncrements',
        'id_outlet' => 'unsignedBigInteger|nullable',
        'akun_penjualan' => 'unsignedBigInteger|nullable',
        'akun_piutang' => 'unsignedBigInteger|nullable',
        'akun_kas' => 'unsignedBigInteger|nullable',
        'akun_diskon' => 'unsignedBigInteger|nullable',
        'akun_hpp' => 'unsignedBigInteger|nullable',
        'akun_persediaan' => 'unsignedBigInteger|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'set null'],
            'akun_penjualan' => ['chart_of_accounts', 'id', 'set null'],
            'akun_piutang' => ['chart_of_accounts', 'id', 'set null'],
            'akun_kas' => ['chart_of_accounts', 'id', 'set null'],
            'akun_diskon' => ['chart_of_accounts', 'id', 'set null'],
            'akun_hpp' => ['chart_of_accounts', 'id', 'set null'],
            'akun_persediaan' => ['chart_of_accounts', 'id', 'set null']
        ]
    ],
    
    'invoice_sales_counter' => [
        'id' => 'bigIncrements',
        'id_outlet' => 'unsignedBigInteger|nullable',
        'prefix' => 'string:10',
        'last_number' => 'integer|default:0',
        'year' => 'integer',
        'month' => 'integer',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'set null']
        ]
    ],
    
    'invoice_service_counter' => [
        'id' => 'bigIncrements',
        'prefix' => 'string:10',
        'last_number' => 'integer|default:0',
        'year' => 'integer',
        'month' => 'integer',
        'timestamps' => true,
    ],
];

echo "Generating remaining migrations...\n\n";

foreach ($tables as $tableName => $columns) {
    $migrationName = "create_{$tableName}_table";
    $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $migrationName)));
    
    $migration = "<?php\n\n";
    $migration .= "use Illuminate\\Database\\Migrations\\Migration;\n";
    $migration .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
    $migration .= "use Illuminate\\Support\\Facades\\Schema;\n\n";
    $migration .= "return new class extends Migration\n";
    $migration .= "{\n";
    $migration .= "    public function up(): void\n";
    $migration .= "    {\n";
    $migration .= "        Schema::create('{$tableName}', function (Blueprint \$table) {\n";
    
    $foreignKeys = [];
    
    foreach ($columns as $column => $definition) {
        if ($column === 'timestamps' && $definition === true) {
            $migration .= "            \$table->timestamps();\n";
            continue;
        }
        
        if ($column === 'foreign') {
            $foreignKeys = $definition;
            continue;
        }
        
        $parts = explode('|', $definition);
        $type = array_shift($parts);
        
        if (strpos($type, ':') !== false) {
            list($method, $param) = explode(':', $type);
            if (strpos($param, ',') !== false) {
                $migration .= "            \$table->{$method}('{$column}', {$param})";
            } else {
                $migration .= "            \$table->{$method}('{$column}', {$param})";
            }
        } else {
            $migration .= "            \$table->{$type}('{$column}')";
        }
        
        foreach ($parts as $modifier) {
            if ($modifier === 'primary') {
                $migration .= "->primary()";
            } elseif (strpos($modifier, ':') !== false) {
                list($method, $param) = explode(':', $modifier);
                if ($method === 'default' && in_array($param, ['0', '1'])) {
                    $migration .= "->{$method}({$param})";
                } else {
                    $migration .= "->{$method}('{$param}')";
                }
            } else {
                $migration .= "->{$modifier}()";
            }
        }
        
        $migration .= ";\n";
    }
    
    // Add foreign keys
    foreach ($foreignKeys as $column => $fk) {
        list($refTable, $refColumn, $onDelete) = $fk;
        $migration .= "            \$table->foreign('{$column}')->references('{$refColumn}')->on('{$refTable}')->onDelete('{$onDelete}');\n";
    }
    
    $migration .= "        });\n";
    $migration .= "    }\n\n";
    $migration .= "    public function down(): void\n";
    $migration .= "    {\n";
    $migration .= "        Schema::dropIfExists('{$tableName}');\n";
    $migration .= "    }\n";
    $migration .= "};\n";
    
    $filename = "database/migrations/{$timestamp}_{$migrationName}.php";
    file_put_contents($filename, $migration);
    
    echo "✓ Created: {$filename}\n";
    
    // Increment timestamp to ensure unique filenames
    sleep(1);
    $timestamp = date('Y_m_d_His');
}

echo "\n✓ All remaining migrations generated successfully!\n";
echo "\nNext steps:\n";
echo "1. Review the generated migrations\n";
echo "2. Run: php artisan migrate\n";
