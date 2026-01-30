<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearTransactionData extends Command
{
    protected $signature = 'transaction:clear-all 
                            {--force : Force deletion without confirmation}';
    
    protected $description = 'Hapus semua data transaksi dari database (permintaan, distribusi, penerimaan, retur, pemeliharaan, approval)';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Apakah Anda yakin ingin menghapus SEMUA data transaksi? Tindakan ini tidak dapat dibatalkan!', false)) {
                $this->info('Operasi dibatalkan.');
                return Command::SUCCESS;
            }
        }

        $this->info('Memulai penghapusan data transaksi...');
        $this->newLine();

        DB::beginTransaction();
        try {
            // Urutan penghapusan: Detail dulu, baru Header (untuk menghindari foreign key constraint)

            // 1. Hapus Detail Retur Barang
            $this->clearTable('detail_retur_barang', 'Detail Retur Barang');
            
            // 2. Hapus Retur Barang
            $this->clearTable('retur_barang', 'Retur Barang');
            
            // 3. Hapus Detail Penerimaan Barang
            $this->clearTable('detail_penerimaan_barang', 'Detail Penerimaan Barang');
            
            // 4. Hapus Penerimaan Barang
            $this->clearTable('penerimaan_barang', 'Penerimaan Barang');
            
            // 5. Hapus Detail Distribusi
            $this->clearTable('detail_distribusi', 'Detail Distribusi');
            
            // 6. Hapus Draft Detail Distribusi
            if (Schema::hasTable('draft_detail_distribusi')) {
                $this->clearTable('draft_detail_distribusi', 'Draft Detail Distribusi');
            }
            
            // 7. Hapus Draft Distribusi (jika ada)
            if (Schema::hasTable('draft_distribusi')) {
                $this->clearTable('draft_distribusi', 'Draft Distribusi');
            }
            
            // 8. Hapus Transaksi Distribusi
            $this->clearTable('transaksi_distribusi', 'Transaksi Distribusi');
            
            // 9. Hapus Detail Permintaan Barang
            $this->clearTable('detail_permintaan_barang', 'Detail Permintaan Barang');
            
            // 10. Hapus Permintaan Barang
            $this->clearTable('permintaan_barang', 'Permintaan Barang');
            
            // 11. Hapus Approval Log (yang terkait transaksi)
            if (Schema::hasTable('approval_log')) {
                $this->info('Menghapus Approval Log...');
                $deleted = DB::table('approval_log')
                    ->whereIn('modul_approval', ['PERMINTAAN_BARANG', 'PERMINTAAN_PEMELIHARAAN'])
                    ->delete();
                $this->line("  ✓ Dihapus: {$deleted} record(s)");
            }
            
            // 12. Hapus Approval Permintaan (jika masih ada)
            if (Schema::hasTable('approval_permintaan')) {
                $this->clearTable('approval_permintaan', 'Approval Permintaan');
            }
            
            // 13. Hapus Riwayat Pemeliharaan
            if (Schema::hasTable('riwayat_pemeliharaan')) {
                $this->clearTable('riwayat_pemeliharaan', 'Riwayat Pemeliharaan');
            }
            
            // 14. Hapus Service Report
            if (Schema::hasTable('service_report')) {
                $this->clearTable('service_report', 'Service Report');
            }
            
            // 15. Hapus Kalibrasi Aset
            if (Schema::hasTable('kalibrasi_aset')) {
                $this->clearTable('kalibrasi_aset', 'Kalibrasi Aset');
            }
            
            // 16. Hapus Jadwal Maintenance
            if (Schema::hasTable('jadwal_maintenance')) {
                $this->clearTable('jadwal_maintenance', 'Jadwal Maintenance');
            }
            
            // 17. Hapus Permintaan Pemeliharaan
            if (Schema::hasTable('permintaan_pemeliharaan')) {
                $this->clearTable('permintaan_pemeliharaan', 'Permintaan Pemeliharaan');
            }

            DB::commit();
            
            $this->newLine();
            $this->info('✓ Semua data transaksi berhasil dihapus!');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    private function clearTable(string $tableName, string $displayName): void
    {
        if (!Schema::hasTable($tableName)) {
            $this->line("  ⊘ Tabel {$displayName} tidak ditemukan, dilewati.");
            return;
        }

        $count = DB::table($tableName)->count();
        
        if ($count > 0) {
            // Gunakan DELETE daripada TRUNCATE untuk menghindari foreign key constraint issues
            DB::table($tableName)->delete();
            $this->line("  ✓ {$displayName}: {$count} record(s) dihapus");
        } else {
            $this->line("  ⊘ {$displayName}: Tidak ada data");
        }
    }
}
