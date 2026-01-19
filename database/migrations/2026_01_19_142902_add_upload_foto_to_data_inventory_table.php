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
        Schema::table('data_inventory', function (Blueprint $table) {
            $table->string('upload_foto', 255)->nullable()->after('status_inventory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_inventory', function (Blueprint $table) {
            $table->dropColumn('upload_foto');
        });
    }
};
