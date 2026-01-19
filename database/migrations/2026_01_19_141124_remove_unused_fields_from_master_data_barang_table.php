<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_data_barang', function (Blueprint $table) {
            $table->dropColumn(['spesifikasi', 'merk', 'tipe', 'tahun_produksi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_data_barang', function (Blueprint $table) {
            $table->text('spesifikasi')->nullable()->after('deskripsi');
            $table->string('merk', 100)->nullable()->after('spesifikasi');
            $table->string('tipe', 100)->nullable()->after('merk');
            $table->integer('tahun_produksi')->nullable()->after('tipe');
        });
    }
};
