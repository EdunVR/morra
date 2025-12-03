<?php

/**
 * Script to generate migrations from existing models
 * Run: php generate_migrations_from_models.php
 */

require __DIR__.'/vendor/autoload.php';

$timestamp = date('Y_m_d_His');

// Define core tables structure based on models
$tables = [
    'users' => [
        'id' => 'bigIncrements',
        'name' => 'string',
        'email' => 'string|unique',
        'email_verified_at' => 'timestamp|nullable',
        'password' => 'string',
        'two_factor_secret' => 'text|nullable',
        'two_factor_recovery_codes' => 'text|nullable',
        'two_factor_confirmed_at' => 'timestamp|nullable',
        'remember_token' => 'string:100|nullable',
        'current_team_id' => 'foreignId|nullable',
        'profile_photo_path' => 'string:2048|nullable',
        'level' => 'integer|default:0',
        'akses' => 'json|nullable',
        'akses_outlet' => 'json|nullable',
        'akses_khusus' => 'json|nullable',
        'timestamps' => true,
    ],
    
    'outlets' => [
        'id_outlet' => 'bigIncrements',
        'kode_outlet' => 'string:50|unique',
        'nama_outlet' => 'string',
        'alamat' => 'text|nullable',
        'kota' => 'string:100|nullable',
        'telepon' => 'string:20|nullable',
        'is_active' => 'boolean|default:true',
        'catatan' => 'text|nullable',
        'timestamps' => true,
    ],
    
    'kategori' => [
        'id_kategori' => 'bigIncrements',
        'kode_kategori' => 'string:50',
        'nama_kategori' => 'string',
        'kelompok' => 'string:50|nullable',
        'id_outlet' => 'unsignedBigInteger|nullable',
        'deskripsi' => 'text|nullable',
        'is_active' => 'boolean|default:true',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'satuan' => [
        'id_satuan' => 'bigIncrements',
        'nama_satuan' => 'string:50',
        'keterangan' => 'string|nullable',
        'timestamps' => true,
    ],
    
    'produk' => [
        'id_produk' => 'bigIncrements',
        'id_outlet' => 'unsignedBigInteger',
        'id_kategori' => 'unsignedBigInteger|nullable',
        'kode_produk' => 'string:50',
        'nama_produk' => 'string',
        'merk' => 'string:100|nullable',
        'spesifikasi' => 'text|nullable',
        'diskon' => 'decimal:15,2|default:0',
        'harga_jual' => 'decimal:15,2|default:0',
        'id_satuan' => 'unsignedBigInteger|nullable',
        'tipe_produk' => 'string:50|default:barang_dagang',
        'track_inventory' => 'boolean|default:true',
        'metode_hpp' => 'string:50|default:average',
        'jenis_paket' => 'string:50|nullable',
        'keberangkatan_template_id' => 'unsignedBigInteger|nullable',
        'stok_minimum' => 'integer|default:0',
        'is_active' => 'boolean|default:true',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'cascade'],
            'id_kategori' => ['kategori', 'id_kategori', 'set null'],
            'id_satuan' => ['satuan', 'id_satuan', 'set null']
        ]
    ],
    
    'supplier' => [
        'id_supplier' => 'bigIncrements',
        'nama' => 'string',
        'telepon' => 'string:20|nullable',
        'alamat' => 'text|nullable',
        'email' => 'string|nullable',
        'id_outlet' => 'unsignedBigInteger|nullable',
        'is_active' => 'boolean|default:true',
        'bank' => 'string:100|nullable',
        'no_rekening' => 'string:50|nullable',
        'atas_nama' => 'string|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'set null']
        ]
    ],
    
    'bahan' => [
        'id_bahan' => 'bigIncrements',
        'id_outlet' => 'unsignedBigInteger',
        'kode_bahan' => 'string:50',
        'nama_bahan' => 'string',
        'id_satuan' => 'unsignedBigInteger|nullable',
        'harga_beli' => 'decimal:15,2|default:0',
        'stok' => 'decimal:15,2|default:0',
        'stok_minimum' => 'integer|default:0',
        'is_active' => 'boolean|default:true',
        'timestamps' => true,
        'foreign' => [
            'id_outlet' => ['outlets', 'id_outlet', 'cascade'],
            'id_satuan' => ['satuan', 'id_satuan', 'set null']
        ]
    ],
    
    'hpp_produk' => [
        'id' => 'bigIncrements',
        'id_produk' => 'unsignedBigInteger',
        'hpp' => 'decimal:15,2',
        'stok' => 'decimal:15,2',
        'timestamps' => true,
        'foreign' => [
            'id_produk' => ['produk', 'id_produk', 'cascade']
        ]
    ],
];

echo "Generating migrations...\n\n";

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
            $migration .= "            \$table->{$method}('{$column}', {$param})";
        } else {
            $migration .= "            \$table->{$type}('{$column}')";
        }
        
        foreach ($parts as $modifier) {
            if (strpos($modifier, ':') !== false) {
                list($method, $param) = explode(':', $modifier);
                $migration .= "->{$method}('{$param}')";
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

echo "\n✓ All migrations generated successfully!\n";
echo "\nNext steps:\n";
echo "1. Review the generated migrations\n";
echo "2. Run: php artisan migrate:fresh\n";
