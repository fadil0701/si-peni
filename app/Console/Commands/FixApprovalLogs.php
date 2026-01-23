<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApprovalFlowDefinition;
use App\Models\ApprovalLog;
use App\Models\PermintaanBarang;
use Illuminate\Support\Facades\DB;

class FixApprovalLogs extends Command
{
    protected $signature = 'approval:fix-logs';
    protected $description = 'Create missing ApprovalLogs for existing DIAJUKAN permintaan';

    public function handle()
    {
        $this->info('=== Fixing Missing Approval Logs ===');
        
        // Ambil permintaan yang sudah DIAJUKAN tapi belum ada ApprovalLog
        $permintaans = PermintaanBarang::where('status_permintaan', 'DIAJUKAN')
            ->get();
        
        if ($permintaans->isEmpty()) {
            $this->info('No permintaan with status DIAJUKAN found.');
            return 0;
        }
        
        // Ambil flow step 2 (Kepala Unit)
        $flowStep2 = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('step_order', 2)
            ->first();
        
        if (!$flowStep2) {
            $this->error('ApprovalFlowDefinition step 2 not found! Run: php artisan db:seed --class=ApprovalFlowDefinitionSeeder');
            return 1;
        }
        
        $this->info("Found {$permintaans->count()} permintaan with status DIAJUKAN");
        $this->info("Flow Step 2 ID: {$flowStep2->id}, Role ID: {$flowStep2->role_id}");
        
        $created = 0;
        foreach ($permintaans as $permintaan) {
            // Cek apakah sudah ada ApprovalLog
            $existingLog = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('id_referensi', $permintaan->id_permintaan)
                ->first();
            
            if (!$existingLog) {
                try {
                    ApprovalLog::create([
                        'modul_approval' => 'PERMINTAAN_BARANG',
                        'id_referensi' => $permintaan->id_permintaan,
                        'id_approval_flow' => $flowStep2->id,
                        'user_id' => null,
                        'role_id' => $flowStep2->role_id,
                        'status' => 'MENUNGGU',
                        'catatan' => null,
                        'approved_at' => null,
                    ]);
                    $created++;
                    $this->info("Created ApprovalLog for permintaan ID: {$permintaan->id_permintaan} (No: {$permintaan->no_permintaan})");
                } catch (\Exception $e) {
                    $this->error("Failed to create ApprovalLog for permintaan ID: {$permintaan->id_permintaan} - {$e->getMessage()}");
                }
            } else {
                $this->line("ApprovalLog already exists for permintaan ID: {$permintaan->id_permintaan}");
            }
        }
        
        $this->info("\n=== Fix Complete ===");
        $this->info("Created {$created} ApprovalLog(s)");
        
        return 0;
    }
}




