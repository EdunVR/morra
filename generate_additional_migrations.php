<?php

/**
 * Script to generate additional migrations from existing models
 * Run: php generate_additional_migrations.php
 */

require __DIR__.'/vendor/autoload.php';

$timestamp = date('Y_m_d_His');

// Define additional tables structure based on models
$tables = [
    'member' => [
        'id_member' => 'bigIncrements',
        'kode_member' => 'string:50|unique',
        'nama' => 'string',
        'telepon' => 'string:20|nullable',
        'alamat' => 'text|nullable',
        'email' => 'string|nullable',
        'tanggal_lahir' => 'date|nullable',
        'jenis_kelamin' => 'enum:L,P|nullable',
        'poin' => 'integer|default:0',
        'is_active' => 'boolean|default:1',
        'timestamps' => true,
    ],
    
    'penjualan' => [
        'id_penjualan' => 'bigIncrements',
        'id_outlet' => 'unsignedBigInteger',
        'id_member' => 'unsignedBigInteger|nullable',
        'id_user' => 'unsignedBigInteger',
        'id_gerobak' => 'unsignedBigInteger|nullable',
        'total_item' => 'integer|default:0',
        'total_harga' => 'decimal:15,2|default:0',
        'diskon' => 'decimal:15,2|default:0',
        'bayar' => 'decimal:15,2|default:0',
        'diterima' => 'decimal:15,2|default:0',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'cascade'],
            'id_member' => ['member', 'id_member', 'set null'],
            'id_user' => ['users', 'id', 'cascade']
        ]
    ],
    
    'penjualan_detail' => [
        'id_penjualan_detail' => 'bigIncrements',
        'id_penjualan' => 'unsignedBigInteger',
        'id_produk' => 'unsignedBigInteger',
        'id_outlet' => 'unsignedBigInteger|nullable',
        'id_hpp' => 'unsignedBigInteger|nullable',
        'hpp' => 'decimal:15,2|default:0',
        'harga_jual' => 'decimal:15,2|default:0',
        'jumlah' => 'decimal:10,2|default:0',
        'diskon' => 'decimal:15,2|default:0',
        'subtotal' => 'decimal:15,2|default:0',
        'timestamps' => true,
        'foreign' => [
            'id_penjualan' => ['penjualan', 'id_penjualan', 'cascade'],
            'id_produk' => ['produk', 'id_produk', 'cascade'],
            'id_outlet' => ['outlets', 'id_outlet', 'set null']
        ]
    ],
    
    'pembelian' => [
        'id_pembelian' => 'bigIncrements',
        'id_outlet' => 'unsignedBigInteger',
        'id_supplier' => 'unsignedBigInteger',
        'id_user' => 'unsignedBigInteger',
        'tanggal' => 'date',
        'total_item' => 'integer|default:0',
        'total_harga' => 'decimal:15,2|default:0',
        'diskon' => 'decimal:15,2|default:0',
        'bayar' => 'decimal:15,2|default:0',
        'status' => 'string:50|default:pending',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'cascade'],
            'id_supplier' => ['supplier', 'id_supplier', 'cascade'],
            'id_user' => ['users', 'id', 'cascade']
        ]
    ],
    
    'pembelian_detail' => [
        'id_pembelian_detail' => 'bigIncrements',
        'id_pembelian' => 'unsignedBigInteger',
        'id_bahan' => 'unsignedBigInteger',
        'id_outlet' => 'unsignedBigInteger|nullable',
        'harga_beli' => 'decimal:15,2|default:0',
        'jumlah' => 'decimal:10,2|default:0',
        'subtotal' => 'decimal:15,2|default:0',
        'timestamps' => true,
        'foreign' => [
            'id_pembelian' => ['pembelian', 'id_pembelian', 'cascade'],
            'id_bahan' => ['bahan', 'id_bahan', 'cascade'],
            'id_outlet' => ['outlets', 'id_outlet', 'set null']
        ]
    ],
    
    'inventori' => [
        'id_inventori' => 'bigIncrements',
        'kode_inventori' => 'string:50',
        'nama_barang' => 'string',
        'id_kategori' => 'unsignedBigInteger|nullable',
        'id_outlet' => 'unsignedBigInteger',
        'penanggung_jawab' => 'string|nullable',
        'stok' => 'integer|default:0',
        'lokasi_penyimpanan' => 'string|nullable',
        'status' => 'string:50|default:baik',
        'catatan' => 'text|nullable',
        'is_active' => 'boolean|default:1',
        'timestamps' => true,
        'foreign' => [
            'id_kategori' => ['kategori', 'id_kategori', 'set null'],
            'id_outlet' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'inventori_detail' => [
        'id' => 'bigIncrements',
        'id_inventori' => 'unsignedBigInteger',
        'tanggal' => 'date',
        'jenis_transaksi' => 'enum:masuk,keluar,penyesuaian',
        'jumlah' => 'integer',
        'keterangan' => 'text|nullable',
        'id_user' => 'unsignedBigInteger|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_inventori' => ['inventori', 'id_inventori', 'cascade'],
            'id_user' => ['users', 'id', 'set null']
        ]
    ],
    
    'bahan_detail' => [
        'id' => 'bigIncrements',
        'id_bahan' => 'unsignedBigInteger',
        'harga_beli' => 'decimal:15,2',
        'stok' => 'decimal:15,2',
        'timestamps' => true,
        'foreign' => [
            'id_bahan' => ['bahan', 'id_bahan', 'cascade']
        ]
    ],
    
    'piutang' => [
        'id_piutang' => 'bigIncrements',
        'id_penjualan' => 'unsignedBigInteger',
        'id_member' => 'unsignedBigInteger|nullable',
        'id_outlet' => 'unsignedBigInteger',
        'jumlah_piutang' => 'decimal:15,2',
        'jumlah_dibayar' => 'decimal:15,2|default:0',
        'sisa_piutang' => 'decimal:15,2',
        'tanggal_jatuh_tempo' => 'date|nullable',
        'status' => 'enum:belum_lunas,lunas|default:belum_lunas',
        'timestamps' => true,
        'foreign' => [
            'id_penjualan' => ['penjualan', 'id_penjualan', 'cascade'],
            'id_member' => ['member', 'id_member', 'set null'],
            'id_outlet' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'hutang' => [
        'id_hutang' => 'bigIncrements',
        'id_pembelian' => 'unsignedBigInteger|nullable',
        'id_supplier' => 'unsignedBigInteger',
        'id_outlet' => 'unsignedBigInteger',
        'jumlah_hutang' => 'decimal:15,2',
        'jumlah_dibayar' => 'decimal:15,2|default:0',
        'sisa_hutang' => 'decimal:15,2',
        'tanggal_jatuh_tempo' => 'date|nullable',
        'status' => 'enum:belum_lunas,lunas|default:belum_lunas',
        'timestamps' => true,
        'foreign' => [
            'id_pembelian' => ['pembelian', 'id_pembelian', 'set null'],
            'id_supplier' => ['supplier', 'id_supplier', 'cascade'],
            'id_outlet' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'gerobak' => [
        'id_gerobak' => 'bigIncrements',
        'kode_gerobak' => 'string:50|unique',
        'nama_gerobak' => 'string',
        'id_outlet' => 'unsignedBigInteger',
        'lokasi' => 'string|nullable',
        'status' => 'enum:aktif,nonaktif|default:aktif',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'pengeluaran' => [
        'id_pengeluaran' => 'bigIncrements',
        'id_outlet' => 'unsignedBigInteger',
        'id_user' => 'unsignedBigInteger',
        'tanggal' => 'date',
        'kategori' => 'string:100',
        'keterangan' => 'text|nullable',
        'jumlah' => 'decimal:15,2',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'cascade'],
            'id_user' => ['users', 'id', 'cascade']
        ]
    ],
    
    'opening_balances' => [
        'id' => 'bigIncrements',
        'account_id' => 'unsignedBigInteger',
        'outlet_id' => 'unsignedBigInteger',
        'period_year' => 'integer',
        'period_month' => 'integer',
        'debit' => 'decimal:15,2|default:0',
        'credit' => 'decimal:15,2|default:0',
        'balance' => 'decimal:15,2|default:0',
        'is_posted' => 'boolean|default:0',
        'posted_at' => 'timestamp|nullable',
        'created_by' => 'unsignedBigInteger|nullable',
        'timestamps' => true,
        'foreign' => [
            'account_id' => ['chart_of_accounts', 'id', 'cascade'],
            'outlet_id' => ['outlets', 'id_outlet', 'cascade'],
            'created_by' => ['users', 'id', 'set null']
        ]
    ],
    
    'company_bank_accounts' => [
        'id' => 'bigIncrements',
        'outlet_id' => 'unsignedBigInteger',
        'bank_name' => 'string',
        'account_number' => 'string:50',
        'account_holder' => 'string',
        'branch' => 'string:100|nullable',
        'account_type' => 'enum:checking,savings,other|default:checking',
        'currency' => 'string:3|default:IDR',
        'is_active' => 'boolean|default:1',
        'notes' => 'text|nullable',
        'timestamps' => true,
        'foreign' => [
            'outlet_id' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'cache' => [
        'key' => 'string|primary',
        'value' => 'mediumText',
        'expiration' => 'integer',
    ],
    
    'cache_locks' => [
        'key' => 'string|primary',
        'owner' => 'string',
        'expiration' => 'integer',
    ],
    
    'password_reset_tokens' => [
        'email' => 'string|primary',
        'token' => 'string',
        'created_at' => 'timestamp|nullable',
    ],
    
    'personal_access_tokens' => [
        'id' => 'bigIncrements',
        'tokenable_type' => 'string',
        'tokenable_id' => 'unsignedBigInteger',
        'name' => 'string',
        'token' => 'string:64|unique',
        'abilities' => 'text|nullable',
        'last_used_at' => 'timestamp|nullable',
        'expires_at' => 'timestamp|nullable',
        'timestamps' => true,
        'index' => [
            ['tokenable_type', 'tokenable_id']
        ]
    ],
];

echo "Generating additional migrations...\n\n";

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
    $indexes = [];
    
    foreach ($columns as $column => $definition) {
        if ($column === 'timestamps' && $definition === true) {
            $migration .= "            \$table->timestamps();\n";
            continue;
        }
        
        if ($column === 'foreign') {
            $foreignKeys = $definition;
            continue;
        }
        
        if ($column === 'index') {
            $indexes = $definition;
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
    
    // Add indexes
    foreach ($indexes as $indexColumns) {
        $columnList = "'" . implode("', '", $indexColumns) . "'";
        $migration .= "            \$table->index([{$columnList}]);\n";
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

echo "\n✓ All additional migrations generated successfully!\n";
echo "\nNext steps:\n";
echo "1. Review the generated migrations\n";
echo "2. Run: php artisan migrate\n";
