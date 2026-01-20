<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Untuk MySQL/PostgreSQL, gunakan ALTER TABLE untuk menambahkan nilai enum baru
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE approval_log MODIFY COLUMN status ENUM('MENUNGGU', 'DIKETAHUI', 'DIVERIFIKASI', 'DISETUJUI', 'DITOLAK', 'DIDISPOSISIKAN', 'DIPROSES') DEFAULT 'MENUNGGU'");
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE approval_log DROP CONSTRAINT IF EXISTS approval_log_status_check");
            DB::statement("ALTER TABLE approval_log ADD CONSTRAINT approval_log_status_check CHECK (status IN ('MENUNGGU', 'DIKETAHUI', 'DIVERIFIKASI', 'DISETUJUI', 'DITOLAK', 'DIDISPOSISIKAN', 'DIPROSES'))");
        } else {
            // Untuk SQLite, tidak bisa modify enum, jadi kita skip atau gunakan cara lain
            // SQLite tidak mendukung enum, jadi ini hanya untuk MySQL/PostgreSQL
        }
    }

    public function down(): void
    {
        // Kembalikan ke enum sebelumnya tanpa DIPROSES
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE approval_log MODIFY COLUMN status ENUM('MENUNGGU', 'DIKETAHUI', 'DIVERIFIKASI', 'DISETUJUI', 'DITOLAK', 'DIDISPOSISIKAN') DEFAULT 'MENUNGGU'");
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE approval_log DROP CONSTRAINT IF EXISTS approval_log_status_check");
            DB::statement("ALTER TABLE approval_log ADD CONSTRAINT approval_log_status_check CHECK (status IN ('MENUNGGU', 'DIKETAHUI', 'DIVERIFIKASI', 'DISETUJUI', 'DITOLAK', 'DIDISPOSISIKAN'))");
        }
    }
};
