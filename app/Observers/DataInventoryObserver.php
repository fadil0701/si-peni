<?php

namespace App\Observers;

use App\Models\DataInventory;
use App\Models\InventoryItem;
use App\Models\DataStock;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DataInventoryObserver
{
    /**
     * Handle the DataInventory "created" event.
     */
    public function created(DataInventory $dataInventory): void
    {
        // Auto Register Aset: Jika jenis inventory = ASET dan qty_input > 0
        if ($dataInventory->jenis_inventory === 'ASET' && $dataInventory->qty_input > 0) {
            $this->createInventoryItems($dataInventory);
            $this->updateStock($dataInventory);
        }
    }

    /**
     * Handle the DataInventory "updated" event.
     */
    public function updated(DataInventory $dataInventory): void
    {
        // Jika jenis inventory berubah menjadi ASET dan qty_input > 0
        if ($dataInventory->isDirty('jenis_inventory') && $dataInventory->jenis_inventory === 'ASET' && $dataInventory->qty_input > 0) {
            // Cek apakah sudah ada InventoryItem untuk inventory ini
            $existingItemsCount = InventoryItem::where('id_inventory', $dataInventory->id_inventory)->count();
            
            // Jika belum ada InventoryItem, buat semua inventory items
            if ($existingItemsCount == 0) {
                $this->createInventoryItems($dataInventory);
            }
            
            $this->updateStock($dataInventory);
        }
        // Jika qty_input berubah dan jenis = ASET
        elseif ($dataInventory->jenis_inventory === 'ASET' && $dataInventory->isDirty('qty_input')) {
            $oldQty = $dataInventory->getOriginal('qty_input') ?? 0;
            $newQty = $dataInventory->qty_input;

            if ($newQty > $oldQty) {
                // Tambah inventory items
                $this->createInventoryItems($dataInventory, $oldQty, $newQty);
            }
            
            // Update stock
            $this->updateStock($dataInventory);
        }
    }

    /**
     * Create inventory items untuk aset
     */
    protected function createInventoryItems(DataInventory $dataInventory, int $startFrom = 0, ?int $totalQty = null): void
    {
        $qty = $totalQty ?? (int) $dataInventory->qty_input;
        $startIndex = $startFrom + 1;

        // Load relationships
        $dataInventory->load(['dataBarang', 'gudang.unitKerja']);

        // Get kode_data_barang langsung dari master_data_barang
        $kodeDataBarang = $dataInventory->dataBarang->kode_data_barang ?? 'UNK';
        
        // Get tahun anggaran
        $tahun = $dataInventory->tahun_anggaran;

        // Get urut terakhir untuk kode_data_barang dan tahun ini
        $lastUrut = $this->getLastUrut($kodeDataBarang, $tahun);

        // Prepare data untuk bulk insert
        $items = [];
        
        for ($i = $startIndex; $i <= $qty; $i++) {
            $urut = $lastUrut + ($i - $startIndex) + 1;
            $kodeRegister = $this->generateKodeRegister($kodeDataBarang, $tahun, $urut);
            
            $items[] = [
                'id_inventory' => $dataInventory->id_inventory,
                'kode_register' => $kodeRegister,
                'no_seri' => $dataInventory->no_seri, // Bisa null atau mass input
                'kondisi_item' => 'BAIK', // Default
                'status_item' => 'AKTIF', // Default
                'id_gudang' => $dataInventory->id_gudang,
                'id_ruangan' => null, // Belum ditempatkan
                'qr_code' => $this->generateQRCode($kodeRegister),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert untuk performa lebih baik
        if (!empty($items)) {
            InventoryItem::insert($items);
        }
    }

    /**
     * Generate kode register dengan format: [KODE_DATA_BARANG]/[TAHUN]/[URUT]
     */
    protected function generateKodeRegister(string $kodeDataBarang, int $tahun, int $urut): string
    {
        $urutFormatted = str_pad($urut, 4, '0', STR_PAD_LEFT);
        return "{$kodeDataBarang}/{$tahun}/{$urutFormatted}";
    }

    /**
     * Get urut terakhir untuk kode_data_barang dan tahun tertentu
     */
    protected function getLastUrut(string $kodeDataBarang, int $tahun): int
    {
        // Cari semua inventory_item yang memiliki kode_register dengan pattern: KODE_DATA_BARANG/TAHUN/*
        $pattern = "{$kodeDataBarang}/{$tahun}/%";
        
        $lastItem = InventoryItem::where('kode_register', 'like', $pattern)
            ->orderBy('kode_register', 'desc')
            ->first();

        if ($lastItem && $lastItem->kode_register) {
            // Extract urut dari kode_register: KODE_DATA_BARANG/TAHUN/URUT
            $parts = explode('/', $lastItem->kode_register);
            if (count($parts) === 3) {
                return (int) $parts[2];
            }
        }

        return 0;
    }

    /**
     * Generate QR Code untuk inventory item
     */
    protected function generateQRCode(string $kodeRegister): ?string
    {
        try {
            // Buat struktur direktori berdasarkan kode register: kode_data_barang/tahun/
            $pathParts = explode('/', $kodeRegister);
            $baseDir = storage_path('app/public/qrcodes/inventory_item');
            
            // Buat direktori secara rekursif berdasarkan struktur path
            $currentDir = $baseDir;
            for ($i = 0; $i < count($pathParts) - 1; $i++) {
                $currentDir .= DIRECTORY_SEPARATOR . $pathParts[$i];
                if (!file_exists($currentDir)) {
                    if (!mkdir($currentDir, 0755, true) && !is_dir($currentDir)) {
                        \Log::error('QR Code directory tidak dapat dibuat: ' . $currentDir);
                        return null;
                    }
                }
            }
            
            // Nama file adalah bagian terakhir dari kode register
            $qrCodeFileName = end($pathParts) . '.svg';
            $qrCodePath = 'qrcodes/inventory_item/' . str_replace('/', DIRECTORY_SEPARATOR, $kodeRegister) . '.svg';
            $fullPath = $currentDir . DIRECTORY_SEPARATOR . $qrCodeFileName;
            
            // Generate QR code and save to storage
            QrCode::format('svg')->size(200)->generate($kodeRegister, $fullPath);
            
            return $qrCodePath;
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage(), [
                'kode_register' => $kodeRegister,
                'error' => $e->getMessage()
            ]);
            // Jika gagal generate QR code, return null
            return null;
        }
    }

    /**
     * Update atau create data stock
     */
    protected function updateStock(DataInventory $dataInventory): void
    {
        $dataInventory->load(['dataBarang', 'gudang']);

        $stock = DataStock::firstOrNew([
            'id_data_barang' => $dataInventory->id_data_barang,
            'id_gudang' => $dataInventory->id_gudang,
        ]);

        if ($stock->exists) {
            // Update existing stock
            $oldQty = $dataInventory->getOriginal('qty_input') ?? 0;
            $newQty = $dataInventory->qty_input;
            $diff = $newQty - $oldQty;

            if ($dataInventory->jenis_inventory === 'ASET') {
                // Untuk ASET, qty_masuk bertambah
                $stock->qty_masuk += $diff;
            } else {
                // Untuk PERSEDIAAN, qty_masuk bertambah
                $stock->qty_masuk += $diff;
            }

            $stock->qty_akhir = $stock->qty_awal + $stock->qty_masuk - $stock->qty_keluar;
        } else {
            // Create new stock
            $stock->qty_awal = 0;
            $stock->qty_masuk = $dataInventory->qty_input;
            $stock->qty_keluar = 0;
            $stock->qty_akhir = $dataInventory->qty_input;
            $stock->id_satuan = $dataInventory->id_satuan;
        }

        $stock->last_updated = now();
        $stock->save();
    }
}
