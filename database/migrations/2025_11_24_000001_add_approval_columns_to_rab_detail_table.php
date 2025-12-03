<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rab_detail', function (Blueprint $table) {
            // Add columns in order based on existing structure
            // Existing: id, id_rab, item, deskripsi, qty, satuan, harga, subtotal, timestamps
            
            if (!Schema::hasColumn('rab_detail', 'nama_komponen')) {
                $table->string('nama_komponen')->nullable()->after('id_rab');
            }
            if (!Schema::hasColumn('rab_detail', 'jumlah')) {
                $table->decimal('jumlah', 10, 2)->default(1)->after('qty');
            }
            if (!Schema::hasColumn('rab_detail', 'harga_satuan')) {
                $table->decimal('harga_satuan', 15, 2)->default(0)->after('harga');
            }
            if (!Schema::hasColumn('rab_detail', 'budget')) {
                $table->decimal('budget', 15, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('rab_detail', 'biaya')) {
                $table->decimal('biaya', 15, 2)->default(0)->after('budget');
            }
            if (!Schema::hasColumn('rab_detail', 'nilai_disetujui')) {
                $table->decimal('nilai_disetujui', 15, 2)->default(0)->after('biaya');
            }
            if (!Schema::hasColumn('rab_detail', 'realisasi_pemakaian')) {
                $table->decimal('realisasi_pemakaian', 15, 2)->default(0)->after('nilai_disetujui');
            }
            if (!Schema::hasColumn('rab_detail', 'disetujui')) {
                $table->boolean('disetujui')->default(false)->after('realisasi_pemakaian');
            }
            if (!Schema::hasColumn('rab_detail', 'bukti_transfer')) {
                $table->string('bukti_transfer')->nullable()->after('disetujui');
            }
            if (!Schema::hasColumn('rab_detail', 'sumber_dana')) {
                $table->string('sumber_dana')->nullable()->after('bukti_transfer');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rab_detail', function (Blueprint $table) {
            $columns = ['nilai_disetujui', 'realisasi_pemakaian', 'disetujui', 'bukti_transfer', 'sumber_dana', 'nama_komponen', 'budget', 'biaya'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('rab_detail', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
