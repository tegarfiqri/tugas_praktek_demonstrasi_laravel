<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Status baris resep: 'diresepkan' (menunggu penyerahan obat oleh
     * apoteker) atau 'diserahkan' (obat sudah diserahkan, stok berkurang).
     */
    public function up(): void
    {
        Schema::table('resep', function (Blueprint $table) {
            $table->string('status', 15)->default('diresepkan')->after('aturan_pakai');
        });
    }

    public function down(): void
    {
        Schema::table('resep', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
