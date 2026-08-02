<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel-tabel domain Sistem Informasi Rumah Sakit (SIRS).
     */
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('telepon', 15);
            $table->timestamps();
        });

        Schema::create('dokter', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('spesialis', 50);
            $table->string('telepon', 15);
            $table->timestamps();
        });

        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_obat', 100);
            $table->unsignedInteger('harga');
            $table->unsignedInteger('stok')->default(0);
            $table->string('satuan', 20);
            $table->timestamps();
        });

        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien');
            $table->foreignId('dokter_id')->constrained('dokter');
            $table->date('tanggal');
            $table->text('keluhan');
            $table->text('diagnosa');
            $table->text('tindakan');
            $table->unsignedInteger('biaya_periksa')->default(0);
            $table->timestamps();
        });

        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis');
            $table->foreignId('obat_id')->constrained('obat');
            $table->unsignedInteger('jumlah');
            $table->string('aturan_pakai', 100);
            $table->timestamps();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->unique()->constrained('rekam_medis');
            $table->date('tanggal');
            $table->unsignedInteger('total')->default(0);
            $table->string('status', 15)->default('belum_lunas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('resep');
        Schema::dropIfExists('rekam_medis');
        Schema::dropIfExists('obat');
        Schema::dropIfExists('dokter');
        Schema::dropIfExists('pasien');
    }
};
