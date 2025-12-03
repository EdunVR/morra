<?php

/**
 * Script to generate essential missing migrations
 * Run: php generate_essential_migrations.php
 */

require __DIR__.'/vendor/autoload.php';

$timestamp = date('Y_m_d_His');

// Define essential tables that are commonly used
$tables = [
    'product_images' => [
        'id_image' => 'bigIncrements',
        'id_produk' => 'unsignedBigInteger',
        'path' => 'string',
        'is_primary' => 'boolean|default:0',
        'timestamps' => true,
        'foreign' => [
            'id_produk' => ['produk', 'id_produk', 'cascade']
        ]
    ],
    
    'product_variants' => [
        'id' => 'bigIncrements',
        'product_id' => 'unsignedBigInteger',
        'nama_varian' => 'string',
        'deskripsi' => 'text|nullable',
        'harga' => 'decimal:15,2',
        'is_default' => 'boolean|default:0',
        'timestamps' => true,
        'foreign' => [
            'product_id' => ['produk', 'id_produk', 'cascade']
        ]
    ],
    
    'produk_tipe' => [
        'id' => 'bigIncrements',
        'id_produk' => 'unsignedBigInteger',
        'id_tipe' => 'unsignedBigInteger',
        'timestamps' => true,
        'foreign' => [
            'id_produk' => ['produk', 'id_produk', 'cascade']
        ]
    ],
    
    'tipe' => [
        'id_tipe' => 'bigIncrements',
        'nama_tipe' => 'string',
        'keterangan' => 'text|nullable',
        'timestamps' => true,
    ],
    
    'setting' => [
        'id_setting' => 'bigIncrements',
        'key' => 'string|unique',
        'value' => 'text|nullable',
        'description' => 'text|nullable',
        'timestamps' => true,
    ],
    
    'teams' => [
        'id' => 'bigIncrements',
        'user_id' => 'unsignedBigInteger',
        'name' => 'string',
        'personal_team' => 'boolean',
        'timestamps' => true,
        'foreign' => [
            'user_id' => ['users', 'id', 'cascade']
        ]
    ],
    
    'team_invitations' => [
        'id' => 'bigIncrements',
        'team_id' => 'unsignedBigInteger',
        'email' => 'string',
        'role' => 'string|nullable',
        'timestamps' => true,
        'foreign' => [
            'team_id' => ['teams', 'id', 'cascade']
        ]
    ],
    
    'team_user' => [
        'id' => 'bigIncrements',
        'team_id' => 'unsignedBigInteger',
        'user_id' => 'unsignedBigInteger',
        'role' => 'string|nullable',
        'timestamps' => true,
        'unique' => [
            ['team_id', 'user_id']
        ],
        'foreign' => [
            'team_id' => ['teams', 'id', 'cascade'],
            'user_id' => ['users', 'id', 'cascade']
        ]
    ],
    
    'rab_template' => [
        'id_rab' => 'bigIncrements',
        'nama_template' => 'string',
        'deskripsi' => 'text|nullable',
        'total_biaya' => 'decimal:15,2|default:0',
        'is_active' => 'boolean|default:1',
        'timestamps' => true,
    ],
    
    'rab_detail' => [
        'id' => 'bigIncrements',
        'id_rab' => 'unsignedBigInteger',
        'item' => 'string',
        'deskripsi' => 'text|nullable',
        'qty' => 'decimal:10,2',
        'satuan' => 'string:50',
        'harga' => 'decimal:15,2',
        'subtotal' => 'decimal:15,2',
        'timestamps' => true,
        'foreign' => [
            'id_rab' => ['rab_template', 'id_rab', 'cascade']
        ]
    ],
    
    'produk_rab' => [
        'id' => 'bigIncrements',
        'id_produk' => 'unsignedBigInteger',
        'id_rab' => 'unsignedBigInteger',
        'timestamps' => true,
        'foreign' => [
            'id_produk' => ['produk', 'id_produk', 'cascade'],
            'id_rab' => ['rab_template', 'id_rab', 'cascade']
        ]
    ],
    
    'po_penjualan' => [
        'id_po' => 'bigIncrements',
        'no_po' => 'string:50|unique',
        'tanggal' => 'date',
        'id_member' => 'unsignedBigInteger|nullable',
        'id_outlet' => 'unsignedBigInteger',
        'total' => 'decimal:15,2|default:0',
        'status' => 'string:50|default:pending',
        'keterangan' => 'text|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_member' => ['member', 'id_member', 'set null'],
            'id_outlet' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'po_penjualan_detail' => [
        'id' => 'bigIncrements',
        'id_po' => 'unsignedBigInteger',
        'id_produk' => 'unsignedBigInteger',
        'qty' => 'decimal:10,2',
        'harga' => 'decimal:15,2',
        'subtotal' => 'decimal:15,2',
        'timestamps' => true,
        'foreign' => [
            'id_po' => ['po_penjualan', 'id_po', 'cascade'],
            'id_produk' => ['produk', 'id_produk', 'cascade']
        ]
    ],
    
    'kontra_bon' => [
        'id' => 'bigIncrements',
        'no_kontra_bon' => 'string:50|unique',
        'tanggal' => 'date',
        'id_supplier' => 'unsignedBigInteger',
        'id_outlet' => 'unsignedBigInteger',
        'total' => 'decimal:15,2|default:0',
        'status' => 'string:50|default:pending',
        'keterangan' => 'text|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_supplier' => ['supplier', 'id_supplier', 'cascade'],
            'id_outlet' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'kontra_bon_detail' => [
        'id' => 'bigIncrements',
        'id_kontra_bon' => 'unsignedBigInteger',
        'id_bahan' => 'unsignedBigInteger',
        'qty' => 'decimal:10,2',
        'harga' => 'decimal:15,2',
        'subtotal' => 'decimal:15,2',
        'timestamps' => true,
        'foreign' => [
            'id_kontra_bon' => ['kontra_bon', 'id', 'cascade'],
            'id_bahan' => ['bahan', 'id_bahan', 'cascade']
        ]
    ],
    
    'permintaan_pengiriman' => [
        'id' => 'bigIncrements',
        'no_permintaan' => 'string:50|unique',
        'tanggal' => 'date',
        'id_outlet_asal' => 'unsignedBigInteger',
        'id_outlet_tujuan' => 'unsignedBigInteger',
        'status' => 'string:50|default:pending',
        'keterangan' => 'text|nullable',
        'timestamps' => true,
        'foreign' => [
            'id_outlet_asal' => ['outlets', 'id_outlet', 'cascade'],
            'id_outlet_tujuan' => ['outlets', 'id_outlet', 'cascade']
        ]
    ],
    
    'reg_provinces' => [
        'id' => 'string:2|primary',
        'name' => 'string',
    ],
    
    'reg_regencies' => [
        'id' => 'string:4|primary',
        'province_id' => 'string:2',
        'name' => 'string',
        'foreign' => [
            'province_id' => ['reg_provinces', 'id', 'cascade']
        ]
    ],
    
    'reg_districts' => [
        'id' => 'string:7|primary',
        'regency_id' => 'string:4',
        'name' => 'string',
        'foreign' => [
            'regency_id' => ['reg_regencies', 'id', 'cascade']
        ]
    ],
    
    'reg_villages' => [
        'id' => 'string:10|primary',
        'district_id' => 'string:7',
        'name' => 'string',
        'foreign' => [
            'district_id' => ['reg_districts', 'id', 'cascade']
        ]
    ],
    
    'activity_logs' => [
        'id' => 'bigIncrements',
        'log_name' => 'string|nullable',
        'description' => 'text',
        'subject_type' => 'string|nullable',
        'subject_id' => 'unsignedBigInteger|nullable',
        'causer_type' => 'string|nullable',
        'causer_id' => 'unsignedBigInteger|nullable',
        'properties' => 'json|nullable',
        'timestamps' => true,
        'index' => [
            ['subject_type', 'subject_id'],
            ['causer_type', 'causer_id'],
            ['log_name']
        ]
    ],
    
    'failed_jobs' => [
        'id' => 'bigIncrements',
        'uuid' => 'string|unique',
        'connection' => 'text',
        'queue' => 'text',
        'payload' => 'longText',
        'exception' => 'longText',
        'failed_at' => 'timestamp|useCurrent',
    ],
    
    'jobs' => [
        'id' => 'bigIncrements',
        'queue' => 'string',
        'payload' => 'longText',
        'attempts' => 'unsignedTinyInteger',
        'reserved_at' => 'unsignedInteger|nullable',
        'available_at' => 'unsignedInteger',
        'created_at' => 'unsignedInteger',
        'index' => [
            ['queue']
        ]
    ],
    
    'job_batches' => [
        'id' => 'string|primary',
        'name' => 'string',
        'total_jobs' => 'integer',
        'pending_jobs' => 'integer',
        'failed_jobs' => 'integer',
        'failed_job_ids' => 'longText',
        'options' => 'mediumText|nullable',
        'cancelled_at' => 'integer|nullable',
        'created_at' => 'integer',
        'finished_at' => 'integer|nullable',
    ],
];

echo "Generating essential migrations...\n\n";

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
    $uniqueKeys = [];
    
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
        
        if ($column === 'unique') {
            $uniqueKeys = $definition;
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
            } elseif ($modifier === 'useCurrent') {
                $migration .= "->useCurrent()";
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
    
    // Add unique constraints
    foreach ($uniqueKeys as $uniqueColumns) {
        $columnList = "'" . implode("', '", $uniqueColumns) . "'";
        $migration .= "            \$table->unique([{$columnList}]);\n";
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

echo "\n✓ All essential migrations generated successfully!\n";
echo "\nNext steps:\n";
echo "1. Review the generated migrations\n";
echo "2. Run: php artisan migrate\n";
