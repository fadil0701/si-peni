<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PenerimaanBarang;
use App\Models\DetailPenerimaanBarang;
use App\Models\TransaksiDistribusi;
use App\Models\DetailDistribusi;
use App\Models\DraftDetailDistribusi;
use App\Models\ApprovalLog;
use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;

class CleanupTransactionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:cleanup {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus semua data transaksi dari permintaan sampai penerimaan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Apakah Anda yakin ingin menghapus semua data transaksi (Permintaan, Approval, Distribusi, Draft Distribusi, dan Penerimaan)?')) {
                $this->info('Operasi dibatalkan.');
                return 0;
            }
        }

        $this->info('Memulai penghapusan data transaksi...');
        $this->newLine();

        DB::beginTransaction();
        try {
            // 1. Hapus Detail Penerimaan Barang
            $this->info('Menghapus Detail Penerimaan Barang...');
            $detailPenerimaanCount = DetailPenerimaanBarang::count();
            DetailPenerimaanBarang::truncate();
            $this->info("✓ {$detailPenerimaanCount} detail penerimaan barang dihapus.");

            // 2. Hapus Penerimaan Barang
            $this->info('Menghapus Penerimaan Barang...');
            $penerimaanCount = PenerimaanBarang::count();
            PenerimaanBarang::truncate();
            $this->info("✓ {$penerimaanCount} penerimaan barang dihapus.");

            // 3. Hapus Detail Distribusi
            $this->info('Menghapus Detail Distribusi...');
            $detailDistribusiCount = DetailDistribusi::count();
            DetailDistribusi::truncate();
            $this->info("✓ {$detailDistribusiCount} detail distribusi dihapus.");

            // 4. Hapus Draft Detail Distribusi
            $this->info('Menghapus Draft Detail Distribusi...');
            $draftDetailCount = DraftDetailDistribusi::count();
            DraftDetailDistribusi::truncate();
            $this->info("✓ {$draftDetailCount} draft detail distribusi dihapus.");

            // 5. Hapus Transaksi Distribusi
            $this->info('Menghapus Transaksi Distribusi...');
            $distribusiCount = TransaksiDistribusi::count();
            TransaksiDistribusi::truncate();
            $this->info("✓ {$distribusiCount} transaksi distribusi dihapus.");

            // 6. Hapus Approval Log
            $this->info('Menghapus Approval Log...');
            $approvalLogCount = ApprovalLog::count();
            ApprovalLog::truncate();
            $this->info("✓ {$approvalLogCount} approval log dihapus.");

            // 7. Hapus Detail Permintaan Barang
            $this->info('Menghapus Detail Permintaan Barang...');
            $detailPermintaanCount = DetailPermintaanBarang::count();
            DetailPermintaanBarang::truncate();
            $this->info("✓ {$detailPermintaanCount} detail permintaan barang dihapus.");

            // 8. Hapus Permintaan Barang
            $this->info('Menghapus Permintaan Barang...');
            $permintaanCount = PermintaanBarang::count();
            PermintaanBarang::truncate();
            $this->info("✓ {$permintaanCount} permintaan barang dihapus.");

            DB::commit();

            $this->newLine();
            $this->info('✓ Semua data transaksi berhasil dihapus!');
            $this->newLine();
            $this->table(
                ['Tabel', 'Jumlah Data Dihapus'],
                [
                    ['Detail Penerimaan Barang', $detailPenerimaanCount],
                    ['Penerimaan Barang', $penerimaanCount],
                    ['Detail Distribusi', $detailDistribusiCount],
                    ['Draft Detail Distribusi', $draftDetailCount],
                    ['Transaksi Distribusi', $distribusiCount],
                    ['Approval Log', $approvalLogCount],
                    ['Detail Permintaan Barang', $detailPermintaanCount],
                    ['Permintaan Barang', $permintaanCount],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
