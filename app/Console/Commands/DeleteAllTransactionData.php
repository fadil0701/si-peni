<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteAllTransactionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:delete-all {--confirm : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus semua data transaksi (permintaan, distribusi, penerimaan, retur, pemakaian, dll)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('confirm')) {
            if (!$this->confirm('Apakah Anda yakin ingin menghapus SEMUA data transaksi? Tindakan ini tidak dapat dibatalkan!')) {
                $this->info('Operasi dibatalkan.');
                return 0;
            }
        }

        $this->info('Memulai penghapusan data transaksi...');
        
        // Disable foreign key checks sementara untuk memungkinkan truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        DB::beginTransaction();
        
        try {
            // Urutan penghapusan: Detail dulu, baru Header (untuk menghindari foreign key constraint)
            
            // 1. Detail Penerimaan Barang
            $this->info('Menghapus detail penerimaan barang...');
            $count1 = DB::table('detail_penerimaan_barang')->count();
            DB::table('detail_penerimaan_barang')->delete();
            $this->info("✓ Dihapus {$count1} record detail penerimaan barang");
            
            // 2. Penerimaan Barang
            $this->info('Menghapus penerimaan barang...');
            $count2 = DB::table('penerimaan_barang')->count();
            DB::table('penerimaan_barang')->delete();
            $this->info("✓ Dihapus {$count2} record penerimaan barang");
            
            // 3. Draft Detail Distribusi
            $this->info('Menghapus draft detail distribusi...');
            $count3 = DB::table('draft_detail_distribusi')->count();
            DB::table('draft_detail_distribusi')->delete();
            $this->info("✓ Dihapus {$count3} record draft detail distribusi");
            
            // 4. Detail Distribusi
            $this->info('Menghapus detail distribusi...');
            $count4 = DB::table('detail_distribusi')->count();
            DB::table('detail_distribusi')->delete();
            $this->info("✓ Dihapus {$count4} record detail distribusi");
            
            // 5. Transaksi Distribusi
            $this->info('Menghapus transaksi distribusi...');
            $count5 = DB::table('transaksi_distribusi')->count();
            DB::table('transaksi_distribusi')->delete();
            $this->info("✓ Dihapus {$count5} record transaksi distribusi");
            
            // 6. Detail Retur Barang
            $this->info('Menghapus detail retur barang...');
            $count6 = DB::table('detail_retur_barang')->count();
            DB::table('detail_retur_barang')->delete();
            $this->info("✓ Dihapus {$count6} record detail retur barang");
            
            // 7. Retur Barang
            $this->info('Menghapus retur barang...');
            $count7 = DB::table('retur_barang')->count();
            DB::table('retur_barang')->delete();
            $this->info("✓ Dihapus {$count7} record retur barang");
            
            // 8. Pemakaian Barang
            $this->info('Menghapus pemakaian barang...');
            $count8 = DB::table('pemakaian_barang')->count();
            DB::table('pemakaian_barang')->delete();
            $this->info("✓ Dihapus {$count8} record pemakaian barang");
            
            // 9. Approval Log (untuk transaksi)
            $this->info('Menghapus approval log untuk transaksi...');
            $count9 = DB::table('approval_log')
                ->where(function($q) {
                    $q->where('modul_approval', 'PERMINTAAN_BARANG')
                      ->orWhere('modul_approval', 'DISTRIBUSI_BARANG')
                      ->orWhere('modul_approval', 'PENERIMAAN_BARANG');
                })
                ->count();
            DB::table('approval_log')
                ->where(function($q) {
                    $q->where('modul_approval', 'PERMINTAAN_BARANG')
                      ->orWhere('modul_approval', 'DISTRIBUSI_BARANG')
                      ->orWhere('modul_approval', 'PENERIMAAN_BARANG');
                })
                ->delete();
            $this->info("✓ Dihapus {$count9} record approval log");
            
            // 10. Approval Permintaan
            $this->info('Menghapus approval permintaan...');
            $count10 = DB::table('approval_permintaan')->count();
            DB::table('approval_permintaan')->delete();
            $this->info("✓ Dihapus {$count10} record approval permintaan");
            
            // 11. Detail Permintaan Barang
            $this->info('Menghapus detail permintaan barang...');
            $count11 = DB::table('detail_permintaan_barang')->count();
            DB::table('detail_permintaan_barang')->delete();
            $this->info("✓ Dihapus {$count11} record detail permintaan barang");
            
            // 12. Permintaan Barang
            $this->info('Menghapus permintaan barang...');
            $count12 = DB::table('permintaan_barang')->count();
            DB::table('permintaan_barang')->delete();
            $this->info("✓ Dihapus {$count12} record permintaan barang");
            
            // 13. History Lokasi (untuk transaksi)
            $this->info('Menghapus history lokasi untuk transaksi...');
            $count13 = DB::table('history_lokasi')
                ->whereIn('jenis_transaksi', ['DISTRIBUSI', 'PENERIMAAN', 'RETUR'])
                ->count();
            DB::table('history_lokasi')
                ->whereIn('jenis_transaksi', ['DISTRIBUSI', 'PENERIMAAN', 'RETUR'])
                ->delete();
            $this->info("✓ Dihapus {$count13} record history lokasi");
            
            // 14. Stock Adjustment (jika dianggap transaksi)
            $this->info('Menghapus stock adjustment...');
            $count14 = DB::table('stock_adjustment')->count();
            DB::table('stock_adjustment')->delete();
            $this->info("✓ Dihapus {$count14} record stock adjustment");
            
            DB::commit();
            
            // Enable kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            DB::commit();
            
            $total = $count1 + $count2 + $count3 + $count4 + $count5 + $count6 + $count7 + $count8 + $count9 + $count10 + $count11 + $count12 + $count13 + $count14;
            
            $this->newLine();
            $this->info("✓✓✓ Semua data transaksi berhasil dihapus!");
            $this->info("Total: {$total} record dihapus");
            
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
            return 1;
        }
    }
}
